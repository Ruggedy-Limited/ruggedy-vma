<?php

namespace App\Entities;

use App\Contracts\HasComponentPermissions;
use App\Contracts\SystemComponent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use stdClass;


/**
 * App\Entities\Team
 *
 * @ORM\Entity(repositoryClass="App\Repositories\TeamRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Team extends Base\Team implements SystemComponent, HasComponentPermissions
{
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="teams", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="`owner_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(
     *     name="team_users",
     *     joinColumns={@ORM\JoinColumn(name="team_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
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
     * A more sensible alias for the generated Entity's getComponentPermissionRelatedByUserIds() method
     *
     * @return Collection
     */
    public function getPermissions(): Collection
    {
        return parent::getComponentPermissions();
    }

    /**
     * Get the parent Entity of this Entity
     *
     * @return null
     */
    public function getParent()
    {
        return null;
    }

    /**
     * Check if a person is in a particular team
     *
     * @param User $user
     * @return bool
     */
    public function personIsInTeam(User $user)
    {
        return $this->users->contains($user);
    }
}