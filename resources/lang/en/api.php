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

    MessagingModel::ERROR_COULD_NOT_CREATE_WORKSPACE        => "Sorry, we could not create your workspace.",

    MessagingModel::ERROR_WORKSPACE_CREATE_PERMISSION       => "Sorry, you don't have permission to create Workspaces"
        . " on that User account.",

    MessagingModel::ERROR_WORKSPACE_DOES_NOT_EXIST          => "Sorry, that Workspace does not exist.",

    MessagingModel::ERROR_DELETE_WORKSPACE_PERMISSION       => "Sorry, you don't have permission to delete that"
        . " Workspace.",

    MessagingModel::WARNING_DELETING_WORKSPACE              => "Deleting a workspace will delete all the data related"
        . " to that workspace. This is not reversible. Please confirm.",

    MessagingModel::ERROR_COULD_NOT_DELETE_WORKSPACE        => "Sorry, we could not delete that Workspace.",

    MessagingModel::ERROR_EDIT_WORKSPACE_PERMISSION         => "Sorry, you don't have permission to make changes to"
        . " that Workspace.",

    MessagingModel::ERROR_LIST_WORKSPACES_PERMISSION        => "Sorry, you don't have permission to list those"
        . " Workspaces.",

    MessagingModel::ERROR_VIEW_WORKSPACE_PERMISSION         => "Sorry, you don't have permission to view that"
        . " Workspace or anything in it.",

    MessagingModel::ERROR_AUTH_USER_NOT_OWNER               => "Sorry, we couldn't change/add those permissions."
        . " You can only modify permissions on your own things.",
    MessagingModel::ERROR_COMPONENT_DOES_NOT_EXIST          => "Sorry, that Component does not exist.",
    MessagingModel::ERROR_PERMISSION_DOES_NOT_EXIST         => "Sorry, there is no such permission option."
        . " Please use only 'r' or 'rw'.",
    MessagingModel::ERROR_AUTH_USER_NOT_OWNER_LIST          => "Sorry, you can only see permissions for things"
        . " that you own.",
    MessagingModel::ERROR_EDIT_ASSET_PERMISSION             => "Sorry, you don't have permission to make changes to"
        . " that Asset.",
    MessagingModel::ERROR_DELETE_ASSET_PERMISSION           => "Sorry, you don't have permission to delete that"
        . " Asset.",
    MessagingModel::ERROR_LIST_ASSETS_PERMISSION            => "Sorry, you don't have permission to list those"
        . " Assets.",
    MessagingModel::ERROR_ASSET_DOES_NOT_EXIST              => "Sorry, that Asset does not exist.",
    MessagingModel::WARNING_DELETING_ASSET                  => "Deleting an Asset will delete all the data related"
        . " to that Asset. This is not reversible. Please confirm.",
    MessagingModel::ERROR_ASSET_CREATE_PERMISSION           => "Sorry, you don't have permission to create Assets"
        . " in that Workspace.",
    MessagingModel::ERROR_COULD_NOT_CREATE_ASSET_FILE       => "Sorry, we could not create an Asset because there is a "
        . "problem with the related file.",
    MessagingModel::ERROR_FILE_DOES_NOT_EXIST               => "Sorry, that file does not exist.",
    MessagingModel::ERROR_FILE_VIEW_PERMISSION              => "Sorry, you don't have permission to view that file.",
    MessagingModel::ERROR_FOLDER_DOES_NOT_EXIST             => "Sorry, that folder does not exist.",
    MessagingModel::ERROR_FOLDER_CREATE_PERMISSION          => "Sorry, you do not have permission to create folders on "
        . "that Workspace.",
    MessagingModel::ERROR_FOLDER_DELETE_PERMISSION          => "Sorry, you do not have permission to delete that "
        . "folder.",
    MessagingModel::ERROR_FOLDER_EDIT_PERMISSION            => "Sorry, you do not have permission to edit that folder.",
    MessagingModel::ERROR_FOLDER_VIEW_PERMISSION            => "Sorry, you do not have permission to view that folder.",
];
