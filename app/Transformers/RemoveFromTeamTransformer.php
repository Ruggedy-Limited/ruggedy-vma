<?php

namespace App\Transformers;

use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

class RemoveFromTeamTransformer extends TransformerAbstract
{
    /**
     * Transform a response from the RemoveFromTeam command for the API
     *
     * @param Collection $removeFromTeam
     * @return array
     */
    public function transform(Collection $removeFromTeam)
    {
        return [
            'user' => fractal()->item($removeFromTeam->get('user'), new UserTransformer()),
            'team' => fractal()->item($removeFromTeam->get('team'), new TeamTransformer()),
        ];
    }
}