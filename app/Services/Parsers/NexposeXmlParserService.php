<?php

namespace App\Services\Parsers;

use App\Contracts\ParsesXmlFiles;
use App\Models\NexposeModel;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Collection;
use Illuminate\Validation\Factory;
use App\Repositories\FileRepository;
use Illuminate\Filesystem\Filesystem;
use App\Services\JsonLogService;
use XMLReader;

class NexposeXmlParserService extends AbstractXmlParserService implements ParsesXmlFiles
{
    /**
     * NexposeXmlParserService constructor.
     *
     * @param XMLReader $parser
     * @param Filesystem $fileSystem
     * @param Factory $validatorFactory
     * @param FileRepository $fileRepository
     * @param EntityManager $em
     * @param JsonLogService $logger
     */
    public function __construct(
        XMLReader $parser, Filesystem $fileSystem, Factory $validatorFactory, FileRepository $fileRepository,
        EntityManager $em, JsonLogService $logger
    )
    {
        parent::__construct($parser, $fileSystem, $validatorFactory, $fileRepository, $em, $logger);

    }

    /**
     * @inheritdoc
     */
    protected function resetModel()
    {
        $this->model = new NexposeModel();
    }

    /**
     * @inheritdoc
     */
    protected function getBaseTagName()
    {
        return new Collection([
            'host',
        ]);
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getStoragePath(): string
    {
        return storage_path('scans/xml/nexpose');
    }
}