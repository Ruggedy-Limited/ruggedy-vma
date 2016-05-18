<?php

namespace App\Models;


class MessagingModel
{
    const ERROR_DEFAULT = "error_default";
    const ERROR_INVALID_INPUT = "error_invalid_input";

    const ERROR_SENDING_INVITE_GENERAL = "error_sending_invite";
    const ERROR_SENDING_INVITE_INVALID_TEAM = "error_sending_invite_invalid_team";
    const ERROR_SENDING_INVITE_INVALID_EMAIL = "error_sending_invite_invalid_email";

    const ERROR_TEAM_DOES_NOT_EXIST = "error_team_does_not_exist";
    const ERROR_TEAM_MEMBER_DOES_NOT_EXIST = "error_team_member_does_not_exist";
    const ERROR_USER_DOES_NOT_EXIST = "error_user_does_not_exist";
    const ERROR_USER_NOT_TEAM_OWNER = "error_user_not_team_owner";
    const ERROR_CANNOT_EDIT_ACCOUNT = "error_cannot_edit_account";

    const ERROR_ACCOUNT_WITH_EMAIL_ALREADY_EXISTS = "error_account_with_email_already_exists";
    const ERROR_FIELD_DOES_NOT_EXIST = "error_field_does_not_exist";
}