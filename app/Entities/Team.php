<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use stdClass;


/**
 * App\Entities\Team
 *
 * @ORM\Entity(repositoryClass="App\Repositories\TeamRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Team extends Base\Team
{
    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="team_users",
     *      joinColumns={@ORM\JoinColumn(name="team_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
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
     * Override the AbstractEntity method, just to provide a default set of attributes to include when coercing to
     * stdClass for JSON
     *
     * @param array $onlyTheseAttributes
     * @return stdClass
     */
    public function toStdClass($onlyTheseAttributes = [])
    {
        // Set a list of attributes to include by default when no specific list is given
        if (empty($onlyTheseAttributes)) {
            $onlyTheseAttributes = [
                'id', 'name', 'created_at', 'updated_at'
            ];
        }

        return parent::toStdClass($onlyTheseAttributes);
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add a User to this Team
     *
     * @param User $user
     */
    public function addUser(User $user)
    {
        $this->users->add($user);
    }

    /**
     * Remove a User from this Team
     *
     * @param User $user
     * @return bool
     */
    public function removeUser(User $user)
    {
        return $this->users->removeElement($user);
    }

    /**
     * Check if a person is in a particular team
     *
     * @param User $user
     * @return bool
     */
    public function personIsInTeam(User $user)
    {
        return $this->getUsers()->contains($user);
    }
}