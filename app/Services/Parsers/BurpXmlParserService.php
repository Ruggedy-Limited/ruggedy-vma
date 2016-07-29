<?php

namespace App\Services\Parsers;

use App\Contracts\ParsesXmlFiles;
use App\Entities\Asset;
use App\Models\BurpModel;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use Doctrine\ORM\EntityManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Validation\Factory;
use XMLReader;

class BurpXmlParserService extends AbstractXmlParserService implements ParsesXmlFiles
{
    /**
     * BurpXmlParserService constructor.
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
            'host'            => new Collection([

                'setIpV4'     => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'ip',
                    parent::MAP_ATTRIBUTE_VALIDATION    => FILTER_FLAG_IPV4,
                ]),

                'setHostname' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => [
                        'filled',
                        'regex:' . Asset::REGEX_HOSTNAME
                    ]
                ]),
            ]),

            'name'            => new Collection([
                'setVulnerabilityName' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled',
                ]),
            ]),

            'severity'        => new Collection([
                'setSeverity' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled|in:High,Medium,Low,Information',
                ]),
            ]),

            'issueBackground' => new Collection([
                'setIssueBackground' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled',
                ]),
            ]),

            'issueDetail'     => new Collection([
                'setIssueDetail' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled',
                ]),
            ]),

            'interactionType' => new Collection([
                'setInteractionType' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled',
                ]),
            ]),

            'originIp'        => new Collection([
                'setOriginIp' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled|ip',
                ]),
            ]),

            'time'            => new Collection([
                'setTime' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled',
                ]),
            ]),

            'lookupType'      => new Collection([
                'setLookupType' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled',
                ]),
            ]),

            'lookupHost'      => new Collection([
                'setLookupHost' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled|url',
                ]),
            ]),

            'request'         => new Collection([
                'setHttpMethod' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'method',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|in:GET,HEAD,POST,PUT,OPTIONS,CONNECT,TRACE,DELETE'
                ]),

                'setHttpRequest' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled'
                ]),
            ]),

            'response' => new Collection([
                'setHttpResponse' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled'
                ]),
            ]),

            'location' => new Collection([
                'setHttpUri' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled',
                ]),
            ]),

            'references' => new Collection([
                'setOnlineReferences' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION => 'filled',
                ]),
            ]),
        ]);

        // Instantiate a model
        $this->model = new BurpModel();
    }

    /**
     * @inheritdoc
     */
    protected function resetModel()
    {
        $this->model = new BurpModel();
    }

    /**
     * @inheritdoc
     */
    protected function getBaseTagName()
    {
        return 'issue';
    }

    /**
     * @inheritdoc
     */
    public function getStoragePath(): string
    {
        return storage_path('scans/xml/burp');
    }
}