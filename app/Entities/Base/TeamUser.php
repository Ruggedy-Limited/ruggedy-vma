<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\TeamUser
 *
 * @ORM\Entity(repositoryClass="App\Repositories\TeamUserRepository")
 * @ORM\Table(name="`team_users`", uniqueConstraints={@ORM\UniqueConstraint(name="team_users_team_id_user_id_unique", columns={"`team_id`", "`user_id`"})})
 */
class TeamUser extends AbstractEntity
{
    /**
     * @ORM\Column(name="`team_id`", type="integer")
     */
    protected $team_id;

    /**
     * @ORM\Column(name="`user_id`", type="integer")
     */
    protected $user_id;

    /**
     * @ORM\Column(name="`role`", type="string", length=20)
     */
    protected $role;

    public function __construct()
    {
    }

    /**
     * Set the value of team_id.
     *
     * @param integer $team_id
     * @return \App\Entities\Base\TeamUser
     */
    public function setTeamId($team_id)
    {
        $this->team_id = $team_id;

        return $this;
    }

    /**
     * Get the value of team_id.
     *
     * @return integer
     */
    public function getTeamId()
    {
        return $this->team_id;
    }

    /**
     * Set the value of user_id.
     *
     * @param integer $user_id
     * @return \App\Entities\Base\TeamUser
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of user_id.
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set the value of role.
     *
     * @param string $role
     * @return \App\Entities\Base\TeamUser
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the value of role.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    public function __sleep()
    {
        return array('team_id', 'user_id', 'role');
    }
}