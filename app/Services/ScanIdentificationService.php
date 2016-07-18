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
    protected $scannerPatternMap;

    /**
     * ScanIdentificationService constructor.
     *
     * @param Filesystem $fileSystem
     */
    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem        = $fileSystem;
        $this->scannerPatternMap = new Collection();
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function initialise(UploadedFile $file): bool
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

        // Attempt to get the MIME type
        if (!empty($mimeParts) && is_array($mimeParts)) {
            $mimeExtension = array_pop($mimeParts);
        }

        // Prefer the MIME type extension to the file extension
        if (!empty($mimeExtension) && $mimeExtension != $fileType) {
            $fileType = $mimeExtension;
        }

        // Check that it is a valid/accepted file type
        if (!File::isValidFileType($fileType)) {
            throw new FileException("File of unsupported type '$fileType' given");
        }

        // Set the file and format and initialise the
        $this->file   = $file;
        $this->format = $fileType;
        $this->initialisePatternMap();

        return $this->identifyScanner();
    }

    /**
     * Attempt to identify the scanner. Returns the name if it finds a match, FALSE otherwise.
     *
     * @return bool
     */
    protected function identifyScanner(): bool
    {
        // Make sure the file and file format have been set by the initialise() method
        if (empty($this->file) || !($this->file instanceof UploadedFile) || empty($this->format)) {
            return false;
        }

        // Make sure we have a set of patterns for this file type
        $patternsByFormat = $this->scannerPatternMap->get($this->format);
        if (empty($patternsByFormat) || !($patternsByFormat instanceof Collection)) {
            return false;
        }

        // Iterate over the regex and return the key (scanner) of the first regex that matches the file contents
        $scanner = $patternsByFormat->first(function ($regex, $scanner) {
            $fileSize = $this->getFile()->getClientSize();
            return preg_match($regex, $this->getFile()->openFile()->fread($fileSize));
        }, false);

        // Set the scanner on the service
        $this->scanner = $scanner;

        return !empty($scanner);
    }

    /**
     * Initialise the pattern map
     *
     * @return bool
     */
    protected function initialisePatternMap()
    {
        // Define the XML-based scanner output patterns
        $xmlScannerPatterns = new Collection([
            self::XML_NMAP_REGEX => 'nmap',
        ]);

        // Define the CSV-based scanner output patterns
        $csvScannerPatterns = new Collection();

        // Define the JSON-based scanner output patterns
        $jsonScannerPatterns = new Collection();

        // Set the pattern Collection for each group of file types on the main Collection
        $this->scannerPatternMap->put(File::FILE_TYPE_XML, $xmlScannerPatterns);
        $this->scannerPatternMap->put(File::FILE_TYPE_CSV, $csvScannerPatterns);
        $this->scannerPatternMap->put(File::FILE_TYPE_JSON, $jsonScannerPatterns);

        return true;
    }

    /**
     * Store the file
     *
     * @param int $workspaceId
     * @return bool
     */
    public function storeUploadedFile(int $workspaceId): bool
    {
        // Make sure we received a valid workspaceId
        if (!isset($workspaceId)) {
            return false;
        }

        // Move the file to the relevant storage path
        $storagePath = $this->getProvisionalStoragePath($workspaceId);

        // If the destination folder does not exist, create it
        if (!$this->getFileSystem()->exists($storagePath)) {
            $this->getFileSystem()->mkdir($storagePath, 0755);
        }

        return !empty($this->file->move($storagePath, $this->file->getClientOriginalName()));
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
            . $workspaceId . DIRECTORY_SEPARATOR;
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