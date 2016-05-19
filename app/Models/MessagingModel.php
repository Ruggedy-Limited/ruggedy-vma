<?php

namespace App\Models;


class MessagingModel
{
    /** API General */
    const ERROR_DEFAULT       = "error_default";
    const ERROR_INVALID_INPUT = "error_invalid_input";
    /** API User Management */
    const ERROR_SENDING_INVITE_GENERAL            = "error_sending_invite";
    const ERROR_SENDING_INVITE_INVALID_TEAM       = "error_sending_invite_invalid_team";
    const ERROR_SENDING_INVITE_INVALID_EMAIL      = "error_sending_invite_invalid_email";
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
    
}