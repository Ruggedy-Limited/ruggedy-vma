<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Base\ScannerApp
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`scanner_apps`")
 */
class ScannerApp extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'scanner_apps';

    /** Column name constants */
    const NAME          = 'name';
    const FRIENDLY_NAME = 'friendly_name';
    const DESCRIPTION   = 'description';
    const LOGO          = 'logo';
    const ORDER         = 'order';
    const WORKSPACEAPPS = 'workspaceApps';

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`name`", type="string", length=45)
     */
    protected $name;

    /**
     * @ORM\Column(name="`friendly_name`", type="string", length=255)
     */
    protected $friendly_name;

    /**
     * @ORM\Column(name="`description`", type="string", length=255, nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="`logo`", type="string", length=255, nullable=true)
     */
    protected $logo;

    /**
     * @ORM\Column(name="`order`", type="integer", options={"unsigned":true})
     */
    protected $order;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="WorkspaceApp", mappedBy="scannerApp", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`scanner_app_id`", nullable=false)
     */
    protected $workspaceApps;

    public function __construct()
    {
        $this->workspaceApps = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\ScannerApp
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of name.
     *
     * @param string $name
     * @return \App\Entities\Base\ScannerApp
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of friendly_name.
     *
     * @param string $friendly_name
     * @return ScannerApp
     */
    public function setFriendlyName(string $friendly_name)
    {
        $this->friendly_name = $friendly_name;

        return $this;
    }

    /**
     * Get the value of friendly_name.
     *
     * @return string
     */
    public function getFriendlyName()
    {
        return $this->friendly_name;
    }

    /**
     * Set the value of description.
     *
     * @param string $description
     * @return \App\Entities\Base\ScannerApp
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of logo.
     *
     * @param string $logo
     * @return \App\Entities\Base\ScannerApp
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get the value of logo.
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set the value of order.
     *
     * @param mixed $order
     * @return ScannerApp
     */
    public function setOrder(int $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get the value of order.
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\ScannerApp
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of created_at.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set the value of updated_at.
     *
     * @param \DateTime $updated_at
     * @return \App\Entities\Base\ScannerApp
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Get the value of updated_at.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Add WorkspaceApp entity to collection (one to many).
     *
     * @param \App\Entities\Base\WorkspaceApp $workspaceApp
     * @return \App\Entities\Base\ScannerApp
     */
    public function addWorkspaceApp(WorkspaceApp $workspaceApp)
    {
        $this->workspaceApps[] = $workspaceApp;

        return $this;
    }

    /**
     * Remove WorkspaceApp entity from collection (one to many).
     *
     * @param \App\Entities\Base\WorkspaceApp $workspaceApp
     * @return \App\Entities\Base\ScannerApp
     */
    public function removeWorkspaceApp(WorkspaceApp $workspaceApp)
    {
        $this->workspaceApps->removeElement($workspaceApp);

        return $this;
    }

    /**
     * Get WorkspaceApp entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWorkspaceApps()
    {
        return $this->workspaceApps;
    }

    /**
     * Get the display name for the entity
     *
     * @param bool $plural
     * @return string
     */
    public function getDisplayName(bool $plural = false): string
    {
        return $plural === false ? 'App' : 'Apps';
    }

    public function __sleep()
    {
        return array('id', 'name', 'description', 'logo', 'created_at', 'updated_at');
    }
}