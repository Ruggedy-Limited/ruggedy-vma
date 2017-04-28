<?php

namespace App\Models;

use App\Commands\AddRemoveVulnerabilityToFromFolder;
use App\Commands\Command;
use App\Commands\CreateAsset;
use App\Commands\CreateComment;
use App\Commands\CreateFolder;
use App\Commands\CreateVulnerability;
use App\Commands\CreateWorkspace;
use App\Commands\CreateWorkspaceApp;
use App\Commands\DeleteAsset;
use App\Commands\DeleteComment;
use App\Commands\DeleteFile;
use App\Commands\DeleteFolder;
use App\Commands\DeleteUser;
use App\Commands\DeleteVulnerability;
use App\Commands\DeleteWorkspace;
use App\Commands\DeleteWorkspaceApp;
use App\Commands\EditAsset;
use App\Commands\EditComment;
use App\Commands\EditFile;
use App\Commands\EditFolder;
use App\Commands\EditUserAccount;
use App\Commands\EditVulnerability;
use App\Commands\EditWorkspace;
use App\Commands\EditWorkspaceApp;
use App\Commands\GetAllUsers;
use App\Commands\GetAsset;
use App\Commands\GetAssets;
use App\Commands\GetAssetsInWorkspace;
use App\Commands\GetAssetsMasterList;
use App\Commands\GetFile;
use App\Commands\GetFolder;
use App\Commands\GetListOfPermissions;
use App\Commands\GetListOfUsersWorkspaces;
use App\Commands\GetUser;
use App\Commands\GetWorkspace;
use App\Commands\GetWorkspaceApp;
use App\Commands\RevokePermission;
use App\Commands\UploadScanOutput;
use App\Commands\UpsertPermission;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\AssetNotFoundException;
use App\Exceptions\CommentNotFoundException;
use App\Exceptions\ComponentNotFoundException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FolderNotFoundException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\InvalidPermissionException;
use App\Exceptions\ScannerAppNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\VulnerabilityNotFoundException;
use App\Exceptions\WorkspaceAppNotFoundException;
use App\Exceptions\WorkspaceNotFoundException;
use Doctrine\ORM\ORMException;
use Illuminate\Support\Collection;
use Exception;
use ProxyManager\Exception\FileNotWritableException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class MessagingModel
{
    /** General */
    const ERROR_DEFAULT       = 'error_default';
    const ERROR_INVALID_INPUT = 'error_invalid_input';
    const ERROR_INVALID_EMAIL = 'error_sending_invite_invalid_email';

    /** User Management */
    const ERROR_SENDING_INVITE_GENERAL            = 'error_sending_invite';
    const ERROR_USER_DOES_NOT_EXIST               = 'error_user_does_not_exist';
    const ERROR_CANNOT_EDIT_ACCOUNT               = 'error_cannot_edit_account';
    const ERROR_ACCOUNT_WITH_EMAIL_ALREADY_EXISTS = 'error_account_with_email_already_exists';
    const ERROR_FIELD_DOES_NOT_EXIST              = 'error_field_does_not_exist';
    /** User-related Permission */
    const ERROR_USER_DELETE_PERMISSION            = 'error_user_delete_permission';
    const ERROR_GET_ALL_USERS_PERMISSION          = 'error_get_all_users_permission';
    const ERROR_VIEW_USER_PERMISSION              = 'error_view_user_permission';

    /** File Management */
    const ERROR_FILE_DOES_NOT_EXIST         = 'error_file_does_not_exist';
    const ERROR_INVALID_OR_UNSUPPORTED_FILE = 'error_invalid_or_unsupported_file';
    const ERROR_FILE_COULD_NOT_BE_WRITTEN   = 'error_file_could_not_be_written';
    /** File-related permission */
    const ERROR_FILE_VIEW_PERMISSION        = 'error_file_view_permission';
    const ERROR_DELETE_FILE_PERMISSION      = 'error_delete_file_permission';
    const ERROR_EDIT_FILE_PERMISSION        = 'error_edit_file_permission';
    const ERROR_UPLOAD_FILE_PERMISSION      = 'error_upload_file_permission';

    /** Folder Management */
    const ERROR_FOLDER_DOES_NOT_EXIST    = 'error_folder_does_not_exist';
    /** Folder-related permission */
    const ERROR_FOLDER_VIEW_PERMISSION   = 'error_folder_view_permission';
    const ERROR_FOLDER_CREATE_PERMISSION = 'error_folder_create_permission';
    const ERROR_FOLDER_EDIT_PERMISSION   = 'error_folder_edit_permission';
    const ERROR_FOLDER_DELETE_PERMISSION = 'error_folder_delete_permission';

    /** Workspace Management */
    const ERROR_COULD_NOT_CREATE_WORKSPACE  = 'error_could_not_create_workspace';
    const ERROR_WORKSPACE_DOES_NOT_EXIST    = 'error_workspace_does_not_exist';
    const WARNING_DELETING_WORKSPACE        = 'warning_deleting_workspace';
    const ERROR_COULD_NOT_DELETE_WORKSPACE  = 'error_could_not_delete_workspace';
    /** Workspace-related permission */
    const ERROR_WORKSPACE_CREATE_PERMISSION = 'error_workspace_create_permission';
    const ERROR_DELETE_WORKSPACE_PERMISSION = 'error_delete_workspace_permission';
    const ERROR_EDIT_WORKSPACE_PERMISSION   = 'error_edit_workspace_permission';
    const ERROR_LIST_WORKSPACES_PERMISSION  = 'error_list_workspaces_permission';
    const ERROR_VIEW_WORKSPACE_PERMISSION   = 'error_view_workspace_permission';
    
    /** Asset Management */
    const ERROR_COULD_NOT_CREATE_ASSET      = 'error_could_not_create_asset';
    const ERROR_COULD_NOT_CREATE_ASSET_FILE = 'error_could_not_create_asset_file';
    const ERROR_ASSET_DOES_NOT_EXIST        = 'error_asset_does_not_exist';
    const WARNING_DELETING_ASSET            = 'warning_deleting_asset';
    const ERROR_COULD_NOT_DELETE_ASSET      = 'error_could_not_delete_asset';
    /** Asset-related permission */
    const ERROR_ASSET_CREATE_PERMISSION     = 'error_asset_create_permission';
    const ERROR_DELETE_ASSET_PERMISSION     = 'error_delete_asset_permission';
    const ERROR_EDIT_ASSET_PERMISSION       = 'error_asset_permission';
    const ERROR_LIST_ASSETS_PERMISSION      = 'error_list_assets_permission';
    const ERROR_VIEW_ASSET_PERMISSION       = 'error_view_asset_permission';
    
    /** Permission Management */
    const ERROR_AUTH_USER_NOT_OWNER       = 'error_auth_user_not_owner';
    const ERROR_AUTH_USER_NOT_OWNER_LIST  = 'error_auth_user_not_owner_list';
    const ERROR_COMPONENT_DOES_NOT_EXIST  = 'error_component_does_not_exist';
    const ERROR_PERMISSION_DOES_NOT_EXIST = 'error_permission_does_not_exist';

    /** Vulnerability Management */
    const ERROR_VULNERABILITY_DOES_NOT_EXIST        = 'error_vulnerability_does_not_exist';
    const ERROR_VULNERABILITY_FILE_DOES_NOT_EXIST   = 'error_vulnerability_file_does_not_exist';
    /** Vulnerability-related permission */
    const ERROR_ADD_REMOVE_VULNERABILITY_PERMISSION = 'error_add_remove_vulnerability_permission';
    const ERROR_CREATE_VULNERABILITY_PERMISSION     = 'error_create_vulnerability_permission';
    const ERROR_DELETE_VULNERABILITY_PERMISSION     = 'error_delete_vulnerability_permission';
    const ERROR_EDIT_VULNERABILITY_PERMISSION       = 'error_edit_vulnerability_permission';

    /** Comment Management */
    const ERROR_COMMENT_DOES_NOT_EXIST    = 'error_comment_does_not_exist';
    /** Comment-related permission */
    const ERROR_CREATE_COMMENT_PERMISSION = 'error_create_comment';
    const ERROR_DELETE_COMMENT_PERMISSION = 'error_delete_comment_permission';
    const ERROR_EDIT_COMMENT_PERMISSION   = 'error_edit_comment_permission';

    /** App Management */
    const ERROR_SCANNER_APP_DOES_NOT_EXIST      = 'error_scanner_app_does_not_exist';
    const ERROR_WORKSPACE_APP_DOES_NOT_EXIST    = 'error_workspace_app_does_not_exist';
    /** App-related permission */
    const ERROR_CREATE_WORKSPACE_APP_PERMISSION = 'error_create_workspace_app_permission';
    const ERROR_DELETE_WORKSPACE_APP_PERMISSION = 'error_delete_workspace_app_permission';
    const ERROR_EDIT_WORKSPACE_APP_PERMISSION   = 'error_edit_workspace_app_permission';
    const ERROR_VIEW_WORKSPACE_APP_PERMISSION   = 'error_view_workspace_app_permission';

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

            /** User-related commands */
            GetAllUsers::class                        => static::ERROR_GET_ALL_USERS_PERMISSION,
            GetUser::class                            => static::ERROR_VIEW_USER_PERMISSION,
            EditUserAccount::class                    => static::ERROR_CANNOT_EDIT_ACCOUNT,
            DeleteUser::class                         => static::ERROR_USER_DELETE_PERMISSION,

            /** Workspace-related commands */
            CreateWorkspace::class                    => static::ERROR_WORKSPACE_CREATE_PERMISSION,
            DeleteWorkspace::class                    => static::ERROR_DELETE_WORKSPACE_PERMISSION,
            EditWorkspace::class                      => static::ERROR_EDIT_WORKSPACE_PERMISSION,
            GetListOfUsersWorkspaces::class           => static::ERROR_LIST_WORKSPACES_PERMISSION,
            GetWorkspace::class                       => static::ERROR_VIEW_WORKSPACE_PERMISSION,

            /** Asset-related commands */
            GetAsset::class                           => static::ERROR_VIEW_ASSET_PERMISSION,
            GetAssets::class                          => static::ERROR_LIST_ASSETS_PERMISSION,
            CreateAsset::class                        => static::ERROR_ASSET_CREATE_PERMISSION,
            EditAsset::class                          => static::ERROR_EDIT_ASSET_PERMISSION,
            DeleteAsset::class                        => static::ERROR_DELETE_ASSET_PERMISSION,
            GetAssetsMasterList::class                => static::ERROR_LIST_ASSETS_PERMISSION,
            GetAssetsInWorkspace::class               => static::ERROR_LIST_ASSETS_PERMISSION,

            /** Permission-related commands */
            UpsertPermission::class                   => static::ERROR_AUTH_USER_NOT_OWNER,
            RevokePermission::class                   => static::ERROR_AUTH_USER_NOT_OWNER,
            GetListOfPermissions::class               => static::ERROR_AUTH_USER_NOT_OWNER_LIST,

            /** File-related commands */
            GetFile::class                            => static::ERROR_FILE_VIEW_PERMISSION,
            DeleteFile::class                         => static::ERROR_DELETE_FILE_PERMISSION,
            EditFile::class                           => static::ERROR_EDIT_FILE_PERMISSION,
            UploadScanOutput::class                   => static::ERROR_UPLOAD_FILE_PERMISSION,

            /** Folder-related commands */
            GetFolder::class                          => static::ERROR_FOLDER_VIEW_PERMISSION,
            CreateFolder::class                       => static::ERROR_FOLDER_CREATE_PERMISSION,
            EditFolder::class                         => static::ERROR_FOLDER_EDIT_PERMISSION,
            DeleteFolder::class                       => static::ERROR_FOLDER_DELETE_PERMISSION,
            AddRemoveVulnerabilityToFromFolder::class => static::ERROR_ADD_REMOVE_VULNERABILITY_PERMISSION,

            /** Comment-related commands */
            CreateComment::class                      => static::ERROR_CREATE_COMMENT_PERMISSION,
            DeleteComment::class                      => static::ERROR_DELETE_COMMENT_PERMISSION,
            EditComment::class                        => static::ERROR_EDIT_COMMENT_PERMISSION,

            /** Vulnerability-related commands */
            CreateVulnerability::class                => static::ERROR_CREATE_VULNERABILITY_PERMISSION,
            DeleteVulnerability::class                => static::ERROR_DELETE_VULNERABILITY_PERMISSION,
            EditVulnerability::class                  => static::ERROR_EDIT_VULNERABILITY_PERMISSION,

            /** App-related commands */
            CreateWorkspaceApp::class                 => static::ERROR_CREATE_WORKSPACE_APP_PERMISSION,
            DeleteWorkspaceApp::class                 => static::ERROR_DELETE_WORKSPACE_APP_PERMISSION,
            EditWorkspaceApp::class                   => static::ERROR_EDIT_WORKSPACE_APP_PERMISSION,
            GetWorkspaceApp::class                    => static::ERROR_VIEW_WORKSPACE_APP_PERMISSION,
        ]);

        $fileNotFoundMap = new Collection([
            /** File-related */
            GetFile::class             => static::ERROR_FILE_DOES_NOT_EXIST,
            DeleteFile::class          => static::ERROR_FILE_DOES_NOT_EXIST,
            EditFile::class            => static::ERROR_FILE_DOES_NOT_EXIST,

            /** Other */
            CreateAsset::class         => static::ERROR_COULD_NOT_CREATE_ASSET_FILE,
            CreateVulnerability::class => static::ERROR_VULNERABILITY_FILE_DOES_NOT_EXIST,
        ]);

        static::$commandMessageMap = new Collection([
            ActionNotPermittedException::class   => $actionNotPermittedMap,
            InvalidEmailException::class          => static::ERROR_INVALID_EMAIL,
            InvalidInputException::class          => static::ERROR_INVALID_INPUT,
            UserNotFoundException::class          => static::ERROR_USER_DOES_NOT_EXIST,
            ORMException::class                   => static::ERROR_ACCOUNT_WITH_EMAIL_ALREADY_EXISTS,
            FileNotFoundException::class          => $fileNotFoundMap,
            WorkspaceNotFoundException::class     => static::ERROR_WORKSPACE_DOES_NOT_EXIST,
            DeleteWorkspaceApp::class             => static::ERROR_WORKSPACE_APP_DOES_NOT_EXIST,
            AssetNotFoundException::class         => static::ERROR_ASSET_DOES_NOT_EXIST,
            ComponentNotFoundException::class     => static::ERROR_COMPONENT_DOES_NOT_EXIST,
            InvalidPermissionException::class     => static::ERROR_PERMISSION_DOES_NOT_EXIST,
            FolderNotFoundException::class        => static::ERROR_FOLDER_DOES_NOT_EXIST,
            WorkspaceAppNotFoundException::class  => static::ERROR_SCANNER_APP_DOES_NOT_EXIST,
            ScannerAppNotFoundException::class    => static::ERROR_SCANNER_APP_DOES_NOT_EXIST,
            CommentNotFoundException::class       => static::ERROR_COMMENT_DOES_NOT_EXIST,
            VulnerabilityNotFoundException::class => static::ERROR_VULNERABILITY_DOES_NOT_EXIST,
            FileException::class                  => static::ERROR_INVALID_OR_UNSUPPORTED_FILE,
            FileNotWritableException::class       => static::ERROR_FILE_COULD_NOT_BE_WRITTEN,
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