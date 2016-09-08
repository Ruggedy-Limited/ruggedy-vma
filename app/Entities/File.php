<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection;

/**
 * App\Entities\File
 *
 * @ORM\Entity(repositoryClass="App\Repositories\FileRepository")
 * @ORM\HasLifecycleCallbacks
 */
class File extends Base\File implements SystemComponent
{
    const FILE_TYPE_XML  = 'xml';
    const FILE_TYPE_CSV  = 'csv';
    const FILE_TYPE_JSON = 'json';

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="files", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    /**
     * Get a Collection of valid file types
     *
     * @return Collection
     */
    public static function getValidFileTypes()
    {
        return new Collection([
            static::FILE_TYPE_XML,
            static::FILE_TYPE_CSV,
            static::FILE_TYPE_JSON,
        ]);
    }

    /**
     * Check if the given file type is valid
     *
     * @param string $fileType
     * @return bool
     */
    public static function isValidFileType(string $fileType)
    {
        return static::getValidFileTypes()->contains($fileType);
    }

    /**
     * @inheritdoc
     *
     * @return Base\Asset
     */
    function getParent()
    {
        return $this->asset;
    }
}