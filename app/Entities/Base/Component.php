<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Base\Component
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`components`")
 */
class Component extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The name of the component and the name of the
     *
     * @ORM\Column(name="`name`", type="string", length=45)
     */
    protected $name;

    /**
     * The class used to store row instances in the application
     *
     * @ORM\Column(name="`class_name`", type="string", length=100)
     */
    protected $class_name;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="ComponentPermission", mappedBy="component", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`component_id`", nullable=false)
     */
    protected $componentPermissions;

    public function __construct()
    {
        $this->componentPermissions = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\Component
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
     * @return \App\Entities\Base\Component
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
     * Set the value of class_name.
     *
     * @param string $class_name
     * @return \App\Entities\Base\Component
     */
    public function setClassName($class_name)
    {
        $this->class_name = $class_name;

        return $this;
    }

    /**
     * Get the value of class_name.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\Component
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
     * @return \App\Entities\Base\Component
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
     * Add ComponentPermission entity to collection (one to many).
     *
     * @param \App\Entities\Base\ComponentPermission $componentPermission
     * @return \App\Entities\Base\Component
     */
    public function addComponentPermission(ComponentPermission $componentPermission)
    {
        $this->componentPermissions[] = $componentPermission;

        return $this;
    }

    /**
     * Remove ComponentPermission entity from collection (one to many).
     *
     * @param \App\Entities\Base\ComponentPermission $componentPermission
     * @return \App\Entities\Base\Component
     */
    public function removeComponentPermission(ComponentPermission $componentPermission)
    {
        $this->componentPermissions->removeElement($componentPermission);

        return $this;
    }

    /**
     * Get ComponentPermission entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComponentPermissions()
    {
        return $this->componentPermissions;
    }

    public function __sleep()
    {
        return array('id', 'name', 'class_name', 'created_at', 'updated_at');
    }
}