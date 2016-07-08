<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection;

/**
 * App\Entities\File
 *
 * @ORM\Entity(repositoryClass="App\Repositories\FileRepository")
 * @ORM\HasLifecycleCallbacks
 */
class File extends Base\File
{
    const FILE_TYPE_XML  = 'xml';
    const FILE_TYPE_CSV  = 'csv';
    const FILE_TYPE_JSON = 'json';

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="files", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;
    
    public static function getValidFileTypes()
    {
        return new Collection([
            static::FILE_TYPE_XML,
            static::FILE_TYPE_CSV,
            static::FILE_TYPE_JSON,
        ]);
    }
    
    public static function isValidFileType(string $fileType)
    {
        return static::getValidFileTypes()->contains($fileType);
    }
}