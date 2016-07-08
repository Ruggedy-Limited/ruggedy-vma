<?php

namespace App\Console\Commands\Parsers\Xml;

use App\Auth\RuggedyTokenGuard;
use App\Commands\CreateAsset;
use App\Console\Kernel;
use App\Entities\File;
use App\Entities\User;
use App\Models\NmapModel;
use App\Repositories\AssetRepository;
use App\Services\Parsers\NmapXmlParserService;
use Doctrine\ORM\EntityManager;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use League\Tactician\CommandBus;
use Exception;

class ParseNmapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:xml:nmap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse NMAP XML output. It will parse all files in storage/scans/xml/nmap';

    /** @var NmapXmlParserService */
    protected $service;

    /** @var EntityManager */
    protected $em;
    
    /** @var AssetRepository */
    protected $repository;

    /** @var CommandBus */
    protected $bus;

    /**
     * Create a new command instance.
     *
     * @param NmapXmlParserService $service
     * @param AssetRepository $assetRepository
     * @param CommandBus $bus
     * @param EntityManager $em
     */
    public function __construct(
        NmapXmlParserService $service, AssetRepository $assetRepository, CommandBus $bus, EntityManager $em
    )
    {
        parent::__construct();
        $this->service    = $service;
        $this->em         = $em;
        $this->repository = $assetRepository;
        $this->bus        = $bus;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filesByWorkspace = $this->getService()->getParseableFiles();
        if ($filesByWorkspace->isEmpty()) {
            $this->info("No files to process at the moment.");
            return true;
        }

        $filesByWorkspace->each(function($file, $offset) {
            /** @var File $file */
            return $this->processFile($file);
        });

        return true;
    }

    /**
     * Process all the NMAP files related to a Workspace
     *
     * @param Collection $files
     * @param int $workspaceId
     * @return bool
     */
    protected function processWorkspaceFiles(Collection $files, int $workspaceId)
    {
        if (!isset($files, $workspaceId) || $files->isEmpty()) {
            return true;
        }

        $this->info("Processing files for Workspace: $workspaceId");

        $files->each(function($file, $offset) use ($workspaceId) {

        });

        return true;
    }

    /**
     * Process a single NMAP file
     *
     * @param File $file
     * @return bool
     */
    protected function processFile(File $file)
    {
        try {
            /** @var Collection $nmapModels */
            $nmapModels = $this->getService()->processXmlFile($file);
        } catch (FileNotFoundException $e) {
            $this->error("File not found: $file.");
            return true;
        }

        if ($nmapModels->isEmpty()) {
            $this->error("Unable to process file: $file.");
            return true;
        }

        $assets = new Collection();
        $workspaceId = $file->getWorkspaceId();

        $currentUser = Auth::user();
        $fileUser    = $file->getUser();
        if (empty($currentUser) || !($currentUser instanceof User)
            || $currentUser->getId() !== $fileUser->getId()) {

            Auth::login($fileUser);
            
        }

        $nmapModels->each(function($model, $offset) use ($workspaceId, $file, $assets) {
            /** @var NmapModel $model */
            $assetDetails = $model->exportForAsset()->toArray();

            $command = new CreateAsset($workspaceId, $assetDetails);

            try {
                $asset = $this->getBus()->handle($command);
                $assets->push($asset);
            } catch (Exception $e) {
                $this->error("Error: {$e->getMessage()} when processing file: $file");
            }
        });

        if ($assets->count() !== $nmapModels->count()) {
            $failed = $nmapModels->count() - $assets->count();
            $this->error(
                "Failed to process $failed of {$nmapModels->count()} Assets found in file: {$file->getPath()}"
            );
            
            return true;
        }

        $this->getService()->moveFileToProcessed($file);
        
        $this->info(
            "Successfully created/updated {$assets->count()} of {$nmapModels->count()} Assets found in file:"
            . " {$file->getPath()}"
        );
        $this->getService()->resetModels();

        return true;
    }

    /**
     * @return NmapXmlParserService
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @return AssetRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return CommandBus
     */
    public function getBus()
    {
        return $this->bus;
    }
}
