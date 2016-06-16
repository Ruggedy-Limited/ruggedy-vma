<?php

namespace App\Entities;

use App\Contracts\HasGetId;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection;
use stdClass;


/**
 * @ORM\Entity(repositoryClass="App\Repositories\ComponentPermissionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ComponentPermission extends Base\ComponentPermission
{
    /** Read and read/write value constants */
    const PERMISSION_READ_ONLY  = 'r';
    const PERMISSION_READ_WRITE = 'rw';

    /**
     * Key constants for the result Collection returned by the
     * command handlers that handle permission-related commands
     */
    const RESULT_KEY_AFFECTED = 'affected_permissions';
    const RESULT_KEY_ALL      = 'all_permissions';

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
                'component' ,'instance_id', 'user_id', 'team_id', 'permission', 'granted_by', 'created_at', 'updated_at'
            ];
        }

        return parent::toStdClass($onlyTheseAttributes);
    }

    /**
     * Resync the scalar foreign key members from the related entity ids after every flush
     *
     * @ORM\PreFlush
     */
    public function syncForeignKeyMembers()
    {
        $this->getRelatedEntityGetters()->each([$this, 'syncForeignKeyMember']);
    }

    /**
     * Sync a related entity's ID to the related member that stores the scalar foreign key value
     *
     * @param $getter
     * @param $member
     */
    public function syncForeignKeyMember($getter, $member)
    {
        // Make sure the relevant getter and property exist
        if (!method_exists($this, $getter) || !property_exists($this, $member)) {
            return;
        }

        // Get the related entity using the getter method and if it is set, get it's ID and populate the relevant
        // member with the ID value
        $relatedEntity = $this->$getter();
        if (isset($relatedEntity) && $relatedEntity instanceof HasGetId) {
            $this->$member = $relatedEntity->getId();
        }
    }

    /**
     * Get a collection with member names as keys and related entity getters as values
     *
     * @return Collection
     */
    public function getRelatedEntityGetters()
    {
        return new Collection([
            'component_id' => 'getComponent',
            'user_id'      => 'getUserRelatedByUserId',
            'team_id'      => 'getTeam',
            'granted_by'   => 'getUserRelatedByGrantedBy',
        ]);
    }
}