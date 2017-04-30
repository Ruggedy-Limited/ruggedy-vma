<?php

namespace App\Services;

use App\Entities\Vulnerability;
use Illuminate\Http\UploadedFile;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageService
{
    /**
     * ImageService constructor.
     *
     * @param Filesystem $fileSystem
     */
    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * Store Vulnerability
     *
     * @param Vulnerability $vulnerability
     */
    public function storeVulnerabilityThumbnails(Vulnerability $vulnerability)
    {
        if (!$this->fileSystem->exists(Vulnerability::getThumbnailStoragePath())) {
            $this->fileSystem->mkdir(Vulnerability::getThumbnailStoragePath(), 0755);
        }

        collect([
            'setThumbnail1' => $vulnerability->getThumbnail1(),
            'setThumbnail2' => $vulnerability->getThumbnail2(),
            'setThumbnail3' => $vulnerability->getThumbnail3(),
        ])->filter(function ($thumbnail) {
            return !empty($thumbnail) && $thumbnail instanceof UploadedFile;
        })->each(function ($thumbnail, $setter) use ($vulnerability) {
            /** @var UploadedFile $thumbnail */
            try {
                if (!Vulnerability::isAcceptedThumbnailExtension($thumbnail->getClientOriginalExtension())) {
                    throw new FileException(
                        "Only files with the following extensions are accepted as thumnails:"
                        ."jpeg, png, bmp, gif, or svg"
                    );
                }

                $file = $thumbnail->move(
                    storage_path('app' . DIRECTORY_SEPARATOR . static::pocPath()),
                    date("YmdHis") . '_' . md5($thumbnail->getClientOriginalName() . microtime()) . '.'
                    . $thumbnail->getClientOriginalExtension()
                );
            } catch (FileException $e) {
                return;
            }

            $vulnerability->$setter($file->getRealPath());
        });
    }

    /**
     * Remove the Vulnerability Thumbnail stored at the given path
     *
     * @param string $path
     * @return bool
     */
    public function removeVulnerabilityThumbnail(string $path): bool
    {
        if (empty($path) || !$this->fileSystem->exists($path)) {
            return true;
        }

        try {
            $this->fileSystem->remove($path);
            return true;
        } catch (IOException $e) {
            return false;
        }
    }

    /**
     * Return the disk to use for image storage
     *
     * @return string
     */
    public static function disk(): string
    {
        return 'local';
    }

    /**
     * Return the storage path for POC images
     *
     * @return string
     */
    public static function pocPath(): string
    {
        return 'poc-images' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return Filesystem
     */
    public function getFileSystem(): Filesystem
    {
        return $this->fileSystem;
    }
}