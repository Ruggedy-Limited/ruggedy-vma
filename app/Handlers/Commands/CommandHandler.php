<?php

namespace App\Handlers\Commands;

use App\Entities\User;
use Exception;
use Illuminate\Support\Facades\Auth;


/**
 * Abstract base class for command handlers
 */
abstract class CommandHandler
{
    /**
     * Check for an authenticated User and throw an exception if we couldn't get one. Return the instance of the User
     * if we did get one
     *
     * @return User
     * @throws Exception
     */
    protected function authenticate(): User
    {
        // Get the authenticated User
        /** @var User $requestingUser */
        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            throw new Exception("Could not get an authenticated User");
        }
        
        return $requestingUser;
    }
}