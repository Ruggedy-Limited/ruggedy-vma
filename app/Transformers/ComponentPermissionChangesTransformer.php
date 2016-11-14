<?php

namespace App\Transformers;

use App\Entities\ComponentPermission;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

class ComponentPermissionChangesTransformer extends TransformerAbstract
{
    /**
     * Transform a ComponentPermission entity for the API
     *
     * @param Collection $componentPermissions
     * @return array
     */
    public function transform(Collection $componentPermissions)
    {
        return [
            ComponentPermission::RESULT_KEY_AFFECTED => fractal()->item(
                $componentPermissions->get(ComponentPermission::RESULT_KEY_AFFECTED),
                new ComponentPermissionTransformer()
            ),
            ComponentPermission::RESULT_KEY_ALL      => fractal()->collection(
                $componentPermissions->get(ComponentPermission::RESULT_KEY_ALL),
                new ComponentPermissionTransformer()
            ),
        ];
    }
}