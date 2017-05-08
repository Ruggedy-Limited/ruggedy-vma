<?php

namespace App\Http\Controllers\Auth;

use App\Commands\ResetPassword;
use App\Http\Controllers\AbstractController;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;

/**
 * @Middleware("guest")
 */
class ResetPasswordController extends AbstractController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $command = new ResetPassword(
            $user->setFromArray([
                'password'       => bcrypt($password),
                'remember_token' => Str::random(60),
            ])
        );

        $result = $this->sendCommandToBusHelper($command);
        $this->isCommandError($result);
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationMessages(): array
    {
        return [];
    }
}
