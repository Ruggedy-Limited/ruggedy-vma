<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\Migration
 *
 * @ORM\Entity(repositoryClass="App\Repositories\MigrationRepository")
 * @ORM\Table(name="`migrations`")
 */
class Migration extends AbstractEntity
{
    /**
     * @ORM\Column(name="`migration`", type="string", length=255)
     */
    protected $migration;

    /**
     * @ORM\Column(name="`batch`", type="integer")
     */
    protected $batch;

    public function __construct()
    {
    }

    /**
     * Set the value of migration.
     *
     * @param string $migration
     * @return \App\Entities\Base\Migration
     */
    public function setMigration($migration)
    {
        $this->migration = $migration;

        return $this;
    }

    /**
     * Get the value of migration.
     *
     * @return string
     */
    public function getMigration()
    {
        return $this->migration;
    }

    /**
     * Set the value of batch.
     *
     * @param integer $batch
     * @return \App\Entities\Base\Migration
     */
    public function setBatch($batch)
    {
        $this->batch = $batch;

        return $this;
    }

    /**
     * Get the value of batch.
     *
     * @return integer
     */
    public function getBatch()
    {
        return $this->batch;
    }

    public function __sleep()
    {
        return array('migration', 'batch');
    }
}