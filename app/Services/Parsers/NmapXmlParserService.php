<?php

namespace App\Services\Parsers;

use App\Contracts\ParsesXmlFiles;
use App\Entities\Asset;
use App\Models\NmapModel;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use Doctrine\ORM\EntityManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Validation\Factory;
use XMLReader;

class NmapXmlParserService extends AbstractXmlParserService implements ParsesXmlFiles
{
    /**
     * NmapXmlParserService constructor.
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

        // Create the mappings to use when parsing the NMAP XML output
        $this->fileToSchemaMapping = new Collection([
            'osclass'        => new Collection([
                'setOsVendor' => new Collection([
                    'xmlAttribute'  => 'vendor',
                    'validation'    => [
                        'filled',
                        'regex:/(Linux|Mac|Windows)/',
                    ]
                ]),
            ]),
            'osmatch'        => new Collection([
                'setOsVersion' => new Collection([
                    'xmlAttribute'  => 'name',
                    'validation'    => [
                        'filled',
                        'regex:/(Linux|Mac|Windows)/',
                    ]
                ]),
            ]),
            'address' => new Collection([
                'setIpV4' => new Collection([
                    'xmlAttribute' => 'addr',
                    'validation'   => FILTER_FLAG_IPV4,
                ]),
                'setIpV6' => new Collection([
                    'xmlAttribute' => 'addr',
                    'validation'   => FILTER_FLAG_IPV6,
                ]),
                'setMacAddress' => new Collection([
                    'xmlAttribute' => 'addr',
                    'validation'   => [
                        'filled',
                        'regex:' . Asset::REGEX_MAC_ADDRESS,
                    ]
                ]),
                'setMacVendor' => new Collection([
                    'xmlAttribute'  => 'vendor',
                    'validation'    => new Collection([
                        'main'    => ['vendor'   => 'filled'],
                        'related' => new Collection(['addrtype' => 'filled|in:mac']),
                    ]),
                ]),
            ]),
            'hostname' => new Collection([
                'setHostname' => new Collection([
                    'xmlAttribute' => 'name',
                    'validation'   => 'filled|url'
                ]),
            ]),
        ]);

        // Instantiate a model to collect the relevant information from the model
        $this->model = new NmapModel();
    }

    /**
     * @inheritdoc
     */
    protected function resetModel()
    {
        $this->model = new NmapModel();
    }

    /**
     * @inheritdoc
     */
    protected function getBaseTagName()
    {
        return 'host';
    }
    
    /**
     * @inheritdoc
     */
    public function getStoragePath(): string
    {
        return storage_path('scans/xml/nmap');
    }
}