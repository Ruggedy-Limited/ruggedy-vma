<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
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
    const RESULT_KEY_CREATED  = 'created';
    const RESULT_KEY_MODIFIED = 'modified';
    const RESULT_KEY_DELETED  = 'deleted';
    const RESULT_KEY_ALL      = 'all';

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
                'instance_id', 'user_id', 'permission', 'team_id', 'granted_by', 'created_at', 'updated_at'
            ];
        }

        return parent::toStdClass($onlyTheseAttributes);
    }
}