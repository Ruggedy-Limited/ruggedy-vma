<?php
use App\Models\MessagingModel;

return [
    MessagingModel::ERROR_DEFAULT => "Sorry, there was a problem we can't yet explain."
        . " Please try again or contact support.",
    MessagingModel::ERROR_INVALID_INPUT => "Sorry, you did not provide some required information in your request."
        . " Please try again.",
    MessagingModel::ERROR_SENDING_INVITE_GENERAL => "Sorry, there was a problem sending your invitation."
        . " Please try again or report the problem to support.",
    MessagingModel::ERROR_SENDING_INVITE_INVALID_EMAIL => "Sorry, we could not send the invitation because"
        ." we couldn't find a valid email in your request.",
    MessagingModel::ERROR_SENDING_INVITE_INVALID_TEAM => "Sorry, we could not send the invitation because"
        ." we couldn't find a valid team in your request.",

    MessagingModel::ERROR_TEAM_DOES_NOT_EXIST => "Sorry, that team does not exist.",
    MessagingModel::ERROR_TEAM_MEMBER_DOES_NOT_EXIST => "That person is not in that team."
        . " Perhaps they were already removed?",
    MessagingModel::ERROR_USER_DOES_NOT_EXIST => "Sorry, that user does not exist.",
];
