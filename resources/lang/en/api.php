<?php
use App\Models\MessagingModel;

return [
    MessagingModel::ERROR_DEFAULT                           => "Sorry, there was a problem we can't yet explain."
        . " Please try again or contact support.",

    MessagingModel::ERROR_INVALID_INPUT                     => "Sorry, you did not provide some required information in"
        . " your request. Please try again.",

    MessagingModel::ERROR_SENDING_INVITE_GENERAL            => "Sorry, there was a problem sending your invitation."
        . " Please try again or report the problem to support.",

    MessagingModel::ERROR_INVALID_EMAIL                     => "Sorry, the email address you provided is invalid.",

    MessagingModel::ERROR_TEAM_DOES_NOT_EXIST               => "Sorry, that team does not exist.",

    MessagingModel::ERROR_TEAM_MEMBER_DOES_NOT_EXIST        => "Sorry, that person is not part of that team.",

    MessagingModel::ERROR_USER_DOES_NOT_EXIST               => "Sorry, that person does not exist.",

    MessagingModel::ERROR_USER_NOT_TEAM_OWNER               => "Sorry, you don't own that team.",

    MessagingModel::ERROR_CANNOT_EDIT_ACCOUNT               => "Sorry, you don't have permission to edit that account.",

    MessagingModel::ERROR_ACCOUNT_WITH_EMAIL_ALREADY_EXISTS => "Sorry, an account with that email already exists.",

    MessagingModel::ERROR_FIELD_DOES_NOT_EXIST              => "Sorry, one or more of the fields you tried to update do"
        . " not exist. No changes were saved.",

    MessagingModel::ERROR_COULD_NOT_CREATE_PROJECT          => "Sorry, we could not create your project.",

    MessagingModel::ERROR_PROJECT_CREATE_PERMISSION         => "Sorry, you don't have permission to create Projects on"
        . " that account.",

    MessagingModel::ERROR_PROJECT_DOES_NOT_EXIST            => "Sorry, that Project does not exist.",

    MessagingModel::ERROR_DELETE_PROJECT_PERMISSION         => "Sorry, you don't have permission to delete that"
        . " Project.",

    MessagingModel::WARNING_DELETING_PROJECT                => "Deleting a project will delete all the data related to"
        . " that project. This is not reversable. Please confirm.",

    MessagingModel::ERROR_COULD_NOT_DELETE_PROJECT          => "Sorry, we could not delete that Project.",

    MessagingModel::ERROR_EDIT_PROJECT_PERMISSION           => "Sorry, you don't have permission to make changes to"
        . " that Project.",

    MessagingModel::ERROR_LIST_PROJECTS_PERMISSION          => "Sorry, you don't have permission to list those"
        . " Projects.",

    MessagingModel::ERROR_COULD_NOT_CREATE_WORKSPACE        => "Sorry, we could not create your workspace.",

    MessagingModel::ERROR_WORKSPACE_CREATE_PERMISSION       => "Sorry, you don't have permission to create Workspaces"
        . " on that account.",

    MessagingModel::ERROR_WORKSPACE_DOES_NOT_EXIST          => "Sorry, that Workspace does not exist.",

    MessagingModel::ERROR_DELETE_WORKSPACE_PERMISSION       => "Sorry, you don't have permission to delete that"
        . " Workspace.",

    MessagingModel::WARNING_DELETING_WORKSPACE              => "Deleting a workspace will delete all the data related"
        . " to that workspace. This is not reversable. Please confirm.",

    MessagingModel::ERROR_COULD_NOT_DELETE_WORKSPACE        => "Sorry, we could not delete that Workspace.",

    MessagingModel::ERROR_EDIT_WORKSPACE_PERMISSION         => "Sorry, you don't have permission to make changes to"
        . " that Workspace.",

    MessagingModel::ERROR_LIST_WORKSPACES_PERMISSION        => "Sorry, you don't have permission to list those"
        . " Workspaces.",
    
];
