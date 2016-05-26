<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * App\Entities\User
 *
 * @ORM\Entity(repositoryClass="App\Repositories\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User extends Base\User implements Authenticatable
{

    /**
     * @ORM\OneToMany(targetEntity="Team", mappedBy="users")
     */
    protected $ownedTeams;

    /**
     * @ORM\ManyToMany(targetEntity="Team", inversedBy="users")
     * @ORM\JoinTable(name="team_users")
     */
    protected $teams;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->ownedTeams = new ArrayCollection();
        $this->teams      = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getOwnedTeams()
    {
        return $this->ownedTeams;
    }

    /**
     * @param ArrayCollection $ownedTeams
     */
    public function setOwnedTeams($ownedTeams)
    {
        $this->ownedTeams = $ownedTeams;
    }

    /**
     * Add a team as owned by this User
     *
     * @param Team $team
     */
    public function addOwnedTeam(Team $team)
    {
        $this->ownedTeams->add($team);
    }

    /**
     * Remove this User as an owner of the given Team
     *
     * @param Team $team
     * @return bool
     */
    public function removeOwnedTeam(Team $team)
    {
        return $this->ownedTeams->removeElement($team);
    }

    /**
     * Check if this user owns a certain team
     *
     * @param Team $team
     * @return bool
     */
    public function ownsTeam(Team $team)
    {
        return $this->ownedTeams->contains($team);
    }

    /**
     * Get all the Teams that this user is part of
     *
     * @return ArrayCollection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * Reset all the teams that this User is part of
     *
     * @param ArrayCollection $teams
     */
    public function setTeams($teams)
    {
        $this->teams = $teams;
    }

    /**
     * Add this User to a Team
     *
     * @param Team $team
     */
    public function addToTeam(Team $team)
    {
        $this->teams->add($team);
    }

    /**
     * @param Team $team
     * @return bool
     */
    public function removeFromTeam(Team $team)
    {
        return $this->teams->removeElement($team);
    }

    /**
     * Checks if a User is in a Team
     *
     * @param Team $team
     * @return bool
     */
    public function isInTeam(Team $team)
    {
        return $this->teams->contains($team);
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * @inheritdoc
     * @return int
     */
    public function getAuthIdentifier()
    {
        return $this->getId();
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->getPassword();
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}