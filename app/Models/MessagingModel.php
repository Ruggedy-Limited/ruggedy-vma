<?php

namespace App\Models;

use App\Commands\Command;
use App\Commands\CreateWorkspace;
use App\Commands\DeleteProject;
use App\Commands\DeleteWorkspace;
use App\Commands\EditProject;
use App\Commands\EditUserAccount;
use App\Commands\EditWorkspace;
use App\Commands\GetListOfUsersInTeam;
use App\Commands\GetListOfUsersProjects;
use App\Commands\GetUserInformation;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\ProjectNotFoundException;
use App\Exceptions\TeamNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNotInTeamException;
use App\Exceptions\WorkspaceNotFoundException;
use Doctrine\ORM\ORMException;
use Illuminate\Support\Collection;
use Exception;


class MessagingModel
{
    /** API General */
    const ERROR_DEFAULT       = "error_default";
    const ERROR_INVALID_INPUT = "error_invalid_input";
    const ERROR_INVALID_EMAIL = "error_sending_invite_invalid_email";

    /** API User Management */
    const ERROR_SENDING_INVITE_GENERAL            = "error_sending_invite";
    const ERROR_TEAM_DOES_NOT_EXIST               = "error_team_does_not_exist";
    const ERROR_TEAM_MEMBER_DOES_NOT_EXIST        = "error_team_member_does_not_exist";
    const ERROR_USER_DOES_NOT_EXIST               = "error_user_does_not_exist";
    const ERROR_USER_NOT_TEAM_OWNER               = "error_user_not_team_owner";
    const ERROR_CANNOT_EDIT_ACCOUNT               = "error_cannot_edit_account";
    const ERROR_ACCOUNT_WITH_EMAIL_ALREADY_EXISTS = "error_account_with_email_already_exists";
    const ERROR_FIELD_DOES_NOT_EXIST              = "error_field_does_not_exist";

    /** API Project Management */
    const ERROR_COULD_NOT_CREATE_PROJECT  = "error_could_not_create_project";
    const ERROR_PROJECT_CREATE_PERMISSION = "error_project_create_permission";
    const ERROR_PROJECT_DOES_NOT_EXIST    = "error_project_does_not_exist";
    const ERROR_DELETE_PROJECT_PERMISSION = "error_delete_project_permission";
    const WARNING_DELETING_PROJECT        = "warning_deleting_project";
    const ERROR_COULD_NOT_DELETE_PROJECT  = "error_could_not_delete_project";
    const ERROR_EDIT_PROJECT_PERMISSION   = "error_project_permission";
    const ERROR_LIST_PROJECTS_PERMISSION  = "error_list_projects_permission";

    /** API Workspace Management */
    const ERROR_COULD_NOT_CREATE_WORKSPACE  = "error_could_not_create_workspace";
    const ERROR_WORKSPACE_CREATE_PERMISSION = "error_workspace_create_permission";
    const ERROR_WORKSPACE_DOES_NOT_EXIST    = "error_workspace_does_not_exist";
    const ERROR_DELETE_WORKSPACE_PERMISSION = "error_delete_workspace_permission";
    const WARNING_DELETING_WORKSPACE        = "warning_deleting_workspace";
    const ERROR_COULD_NOT_DELETE_WORKSPACE  = "error_could_not_delete_workspace";
    const ERROR_EDIT_WORKSPACE_PERMISSION   = "error_workspace_permission";
    const ERROR_LIST_WORKSPACES_PERMISSION  = "error_list_workspaces_permission";
    
    /** @var Collection */
    public static $commandMessageMap;

    /**
     * Initialise the command message map
     */
    public static function initialise()
    {
        // If the command map is already initialised, no need to initialise it again
        if (static::$commandMessageMap instanceof Collection && !static::$commandMessageMap->isEmpty()) {
            return;
        }

        // Initialise the command message map as a Collection
        $actionNotPermittedMap = new Collection([
            GetUserInformation::class     => static::ERROR_USER_NOT_TEAM_OWNER,
            GetListOfUsersInTeam::class   => static::ERROR_USER_NOT_TEAM_OWNER,
            EditUserAccount::class        => static::ERROR_CANNOT_EDIT_ACCOUNT,
            DeleteProject::class          => static::ERROR_DELETE_PROJECT_PERMISSION,
            EditProject::class            => static::ERROR_EDIT_PROJECT_PERMISSION,
            GetListOfUsersProjects::class => static::ERROR_LIST_PROJECTS_PERMISSION,
            CreateWorkspace::class        => static::ERROR_WORKSPACE_CREATE_PERMISSION,
            DeleteWorkspace::class        => static::ERROR_DELETE_WORKSPACE_PERMISSION,
            EditWorkspace::class          => static::ERROR_EDIT_WORKSPACE_PERMISSION,
        ]);

        static::$commandMessageMap = new Collection([
            ActionNotPermittedException::class => $actionNotPermittedMap,
            InvalidEmailException::class       => static::ERROR_INVALID_EMAIL,
            InvalidInputException::class       => static::ERROR_INVALID_INPUT,
            ProjectNotFoundException::class    => static::ERROR_PROJECT_DOES_NOT_EXIST,
            TeamNotFoundException::class       => static::ERROR_TEAM_DOES_NOT_EXIST,
            UserNotFoundException::class       => static::ERROR_USER_DOES_NOT_EXIST,
            UserNotInTeamException::class      => static::ERROR_TEAM_MEMBER_DOES_NOT_EXIST,
            ORMException::class                => static::ERROR_ACCOUNT_WITH_EMAIL_ALREADY_EXISTS,
            WorkspaceNotFoundException::class  => static::ERROR_WORKSPACE_DOES_NOT_EXIST,
        ]);
    }

    /**
     * Get the message key for the translator by the exception and command class
     *
     * @param Exception $exception
     * @param Command $command
     * @return string
     */
    public static function getMessageKeyByExceptionAndCommand($exception, $command)
    {
        try {

            static::initialise();

            // Make sure we got an Exception and Command as arguments.
            // Not using type hinting to avoid Exceptions being thrown in a catch statement, where this
            // method will usually be called from
            if (!($exception instanceof Exception) || !($command instanceof Command)) {
                return static::ERROR_DEFAULT;
            }

            $exceptionClass = get_class($exception);
            $commandClass   = get_class($command);

            // Check that the there is a key for this Exception's class in the map
            if (!static::$commandMessageMap->has($exceptionClass)) {
                return static::ERROR_DEFAULT;
            }

            // If the exception key is a scalar value return it
            if (is_scalar(static::$commandMessageMap->get($exceptionClass))) {
                return static::$commandMessageMap->get($exceptionClass);
            }

            // Extra defensiveness to make sure we don't call the get() method on some other object
            if (!(static::$commandMessageMap->get($exceptionClass) instanceof Collection)) {
                return static::ERROR_DEFAULT;
            }

            // Check that there is a key for this Command's class in the map
            if (!static::$commandMessageMap->get($exceptionClass)->has($commandClass)) {
                return static::ERROR_DEFAULT;
            }

            // We found the message key constant, return it
            return static::$commandMessageMap->get($exceptionClass)->get($commandClass);

        } catch (Exception $e) {
            // This is just being extra defensive, catching any exceptions and returning the default error because
            // this method will usually be called within a catch statement
            return static::ERROR_DEFAULT;
        }
    }
}