<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Team
 *
 * @ORM\Entity(repositoryClass="App\Repositories\TeamRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Team extends Base\Team
{
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="teams")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="teams")
     */
    protected $users;

    /**
     * Team constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->users = new ArrayCollection();
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get all the users in this team
     *
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Reset the users in this team
     *
     * @param ArrayCollection $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * Add a user to the team
     *
     * @param User $user
     */
    public function addUser(User $user)
    {
        $this->users->add($user);
    }

    /**
     * Remove a user from the team
     *
     * @param User $userToRemove
     * @return bool
     */
    public function removeUser(User $userToRemove) {
        return $this->users->removeElement($userToRemove);
    }
}