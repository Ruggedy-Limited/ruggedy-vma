<?php

namespace App\Repositories;

use App\Entities\File;
use App\Entities\Folder;
use App\Entities\FoldersVulnerabilities;
use App\Entities\Vulnerability;
use Doctrine\ORM\EntityRepository;

class FoldersVulnerabilitiesRepository extends EntityRepository
{
    /**
     * Find an existing FoldersVulnerabilities instance or failing to find an existing one,
     * create a new one and populate it
     *
     * @param Folder $folder
     * @param Vulnerability $vulnerability
     * @param File $file
     * @return \App\Entities\Base\FoldersVulnerabilities|FoldersVulnerabilities
     */
    public function findOrCreateOne(Folder $folder, Vulnerability $vulnerability, File $file)
    {
        /** @var FoldersVulnerabilities $foldersVulnerabilities */
        $foldersVulnerabilities = $this->findOneBy([
            FoldersVulnerabilities::FOLDER_ID        => $folder->getId(),
            FoldersVulnerabilities::VULNERABILITY_ID => $vulnerability->getId(),
            FoldersVulnerabilities::FILE_ID          => $file->getId(),
        ]);

        if (!empty($foldersVulnerabilities)) {
            return $foldersVulnerabilities;
        }

        $folderVulnerability = new FoldersVulnerabilities();
        return $folderVulnerability
            ->setFolder($folder)
            ->setVulnerability($vulnerability)
            ->setFile($file);

    }
}