<?php

namespace App\Services;

use App\Entities\File;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;

class ScanIdentificationService
{
    const XML_NMAP_REGEX = '%^<nmaprun.*<host.*<address.*\/>.*<ports>.*</ports>.*</host>.*</nmaprun>$%ms';

    /** @var UploadedFile */
    protected $file;

    /** @var string */
    protected $format;

    /** @var string */
    protected $scanner;

    /** @var Filesystem */
    protected $fileSystem;

    /** @var Collection */
    protected $scannerValidationMap;

    /**
     * ScanIdentificationService constructor.
     *
     * @param Filesystem $fileSystem
     */
    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem           = $fileSystem;
        $this->scannerValidationMap = new Collection();
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function initialise(UploadedFile $file)
    {
        // Check that the file is a valid UploadedFile
        if (!$file->isFile() || !$file->isValid() || !$file->isReadable()) {
            throw new FileException("The uploaded scan output file is not valid");
        }

        // Get the file type by MIME type or extension
        $fileType = $file->extension();
        $mimeType = $file->getClientMimeType();
        if (!empty($mimeType)) {
            $mimeParts = explode("/", $mimeType);
        }

        if (!empty($mimeParts) && is_array($mimeParts)) {
            $mimeExtension = array_pop($mimeParts);
        }

        if (!empty($mimeExtension) && $mimeExtension != $fileType) {
            $fileType = $mimeExtension;
        }

        // Check that it is a valid/accepted file type
        if (!File::isValidFileType($fileType)) {
            throw new FileException("File of unsupported type '$fileType' given");
        }

        $this->file   = $file;
        $this->format = $fileType;
        $this->initialiseValidationMap();

        return $this->identifyScanner();
    }

    /**
     * Attempt to identify the scanner. Returns the name if it finds a match, FALSE otherwise
     *
     * @return bool|string
     */
    protected function identifyScanner()
    {
        if (empty($this->file) || !($this->file instanceof UploadedFile) || empty($this->format)) {
            return false;
        }

        if (!$this->scannerValidationMap->has($this->format)) {
            return false;
        }

        $patternsByFormat = $this->scannerValidationMap->get($this->format);
        if (empty($patternsByFormat) || !($patternsByFormat instanceof Collection)) {
            return false;
        }

        // Iterate over the regex and return the key (scanner) of the first regex that matches the file contents
        $scanner = $patternsByFormat->first(function ($regex, $scanner) {
            $fileSize = $this->getFile()->getClientSize();
            return preg_match($regex, $this->getFile()->openFile()->fread($fileSize));
        }, false);

        $this->scanner = $scanner;

        return !empty($scanner);
    }

    /**
     * Initialise the validator map
     *
     * @return bool
     */
    protected function initialiseValidationMap()
    {
        $xmlScannerRules = new Collection([
            self::XML_NMAP_REGEX => 'nmap',
        ]);

        $csvScannerRules = new Collection();
        $jsonScannerRules = new Collection();

        $this->scannerValidationMap->put(File::FILE_TYPE_XML, $xmlScannerRules);
        $this->scannerValidationMap->put(File::FILE_TYPE_CSV, $csvScannerRules);
        $this->scannerValidationMap->put(File::FILE_TYPE_JSON, $jsonScannerRules);

        return true;
    }

    /**
     * Store the file
     *
     * @param int $workspaceId
     * @return bool|\Symfony\Component\HttpFoundation\File\File
     */
    public function storeUploadedFile(int $workspaceId)
    {
        if (!isset($workspaceId)) {
            return false;
        }

        // Move the file to the relevant storage path
        $storagePath = $this->getProvisionalStoragePath($workspaceId);

        if (!$this->getFileSystem()->exists($storagePath)) {
            $this->getFileSystem()->mkdir($storagePath, 0755);
        }

        return $this->file->move($storagePath);
    }

    /**
     * Get the full path that the file will be stored when successfully uploaded
     *
     * @param int $workspaceId
     * @return string
     */
    public function getProvisionalStoragePath(int $workspaceId): string
    {
        return $this->getScanFileStorageBasePath() . $this->format . DIRECTORY_SEPARATOR
            . $this->scanner . DIRECTORY_SEPARATOR
            . $workspaceId . DIRECTORY_SEPARATOR
            . $this->file->getFilename();
    }

    /**
     * Get the base storage path for scanner files
     *
     * @return string
     */
    protected function getScanFileStorageBasePath(): string
    {
        return storage_path('scans') . DIRECTORY_SEPARATOR;
    }

    /**
     * @return UploadedFile
     */
    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getScanner(): string
    {
        return $this->scanner;
    }

    /**
     * @param string $scanner
     */
    public function setScanner(string $scanner)
    {
        $this->scanner = $scanner;
    }

    /**
     * @return Filesystem
     */
    public function getFileSystem(): Filesystem
    {
        return $this->fileSystem;
    }

    /**
     * @param Filesystem $fileSystem
     */
    public function setFileSystem(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }
}