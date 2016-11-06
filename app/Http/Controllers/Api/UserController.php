<?php

namespace App\Http\Controllers\Api;

use App;
use App\Commands\EditUserAccount;
use App\Commands\GetUserInformation;
use App\Commands\GetListOfUsersInTeam;
use App\Commands\InviteToTeam;
use App\Commands\RemoveFromTeam;
use App\Transformers\InvitationTransformer;
use App\Transformers\TeamTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;


/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class UserController extends AbstractController
{
    /**
     * Allow a team administrator to add a new user to their team
     *
     * @Post("/user/{teamId}", as="user.invite", where={"teamId": "[0-9]+"})
     *
     * @param $teamId
     * @return ResponseFactory|JsonResponse
     */
    public function inviteToTeam($teamId)
    {
        $command = new InviteToTeam($teamId, $this->getRequest()->json('email', null));
        return $this->sendCommandToBusHelper($command, InvitationTransformer::class, function ($invitation) {
            // Hide the team details and return the invitation
            /** @var Model $invitation */
            $invitation->setHidden(['team']);
            return $invitation;
        });
    }

    /**
     * Allow a team administrator to remove a user from their team
     *
     * @Delete("/user/{teamId}/{userId}", as="user.remove", where={"teamId": "[0-9]+", "userId": "[0-9]+"})
     *
     * @param $teamId
     * @param $userId
     * @return ResponseFactory|JsonResponse
     */
    public function removeFromTeam($teamId, $userId)
    {
        $command = new RemoveFromTeam($teamId, $userId);
        return $this->sendCommandToBusHelper($command, TeamTransformer::class);
    }

    /**
     * Request information about a person on your team
     *
     * @GET("/user/{teamId}/{userId}", as="user.info", where={"teamId": "[0-9]+", "userId": "[0-9]+"})
     *
     * @param $teamId
     * @param $userId
     * @return ResponseFactory|JsonResponse
     */
    public function getUserInformation($teamId, $userId)
    {
        $command = new GetUserInformation($teamId, $userId);
        return $this->sendCommandToBusHelper($command, UserTransformer::class);
    }

    /**
     * Get a list of team members in a particular team
     *
     * @GET("/users/{teamId}", as="users.info", where={"teamId": "[0-9]+"})
     *
     * @param $teamId
     * @return ResponseFactory|JsonResponse
     */
    public function getListOfUsersInTeam($teamId)
    {
        $command = new GetListOfUsersInTeam($teamId);
        return $this->sendCommandToBusHelper($command, UserTransformer::class);
    }

    /**
     * Edit some attributes of a user account
     *
     * @PUT("/user/{userId}", as="user.edit", where={"userId": "([0-9]+)"})
     *
     * @param $userId
     * @return ResponseFactory|JsonResponse
     */
    public function editUserAccount($userId)
    {
        $command = new EditUserAccount($userId, $this->getRequest()->json()->all());
        return $this->sendCommandToBusHelper($command, UserTransformer::class);
    }

    /**
     * @inheritdoc
     */
    protected function getValidationRules(): array
    {
        return [
            'name'                 => 'bail|filled|alpha',
            'email'                => 'bail|filled|email',
            'photo_url'            => 'bail|filled|string',
            'uses_two_factor_auth' => 'bail|filled|boolean',
        ];
    }
}