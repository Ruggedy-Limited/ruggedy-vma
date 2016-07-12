<?php

namespace App\Http\Controllers\Api;

use App;
use App\Commands\CreateProject;
use App\Commands\DeleteProject;
use App\Commands\EditProject;
use App\Commands\GetListOfUsersProjects;
use App\Team as EloquentTeam;
use App\User as EloquentUser;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Laravel\Spark\Interactions\Settings\Teams\SendInvitation;


/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class ProjectController extends AbstractController
{
    /**
     * Create a project in the given user account or in the authenticated user's account if no userId is given
     *
     * @POST("/project/{userId?}", as="project.create", where={"userId":"[0-9]+"})
     *
     * @param $userId
     * @return ResponseFactory|JsonResponse
     */
    public function createProject($userId)
    {
        $command = new CreateProject($userId, $this->getRequest()->json()->all());
        return $this->sendCommandToBusHelper($command);
    }

    /**
     * Delete a project
     *
     * @DELETE("/project/{projectId}/{confirm?}", as="project.delete", where={"projectId":"[0-9]+", "confirm":"^confirm$"})
     *
     * @param $projectId
     * @param null $confirm
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function deleteProject($projectId, $confirm = null)
    {
        $command = new DeleteProject(intval($projectId), boolval($confirm));
        return $this->sendCommandToBusHelper($command);
    }

    /**
     * Edit Project Details
     *
     * @PUT("/project/{projectId}", as="project.edit", where={"projectId":"[0-9]+"})
     *
     * @param $projectId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function editProject($projectId)
    {
        $command = new EditProject($projectId, $this->getRequest()->json()->all());
        return $this->sendCommandToBusHelper($command);
    }

    /**
     * Get a list of projects on a particular person's account
     *
     * @GET("/projects/{userId}", as="projects.list", where={"userId":"[0-9]+"})
     *
     * @param $userId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function getProjectsForUser($userId)
    {
        $command  = new GetListOfUsersProjects(intval($userId));
        return $this->sendCommandToBusHelper($command);
    }

    /**
     * @inheritdoc
     */
    protected function getValidationRules(): array
    {
        return [
            'name' => 'bail|filled|alpha_num',
        ];
    }
}