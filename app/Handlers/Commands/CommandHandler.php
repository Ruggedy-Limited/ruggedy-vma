<?php

namespace App\Handlers\Commands;

use App\Entities\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;


/**
 * Abstract base class for command handlers
 */
abstract class CommandHandler
{
    /** @var Collection */
    protected $validDetailAttributes;
    
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

    /**
     * Get valid details from the details array provided with the command
     *
     * @param array $details
     * @return array
     */
    protected function getValidDetails(array $details): array
    {
        return $this->getValidDetailAttributes()->map(function($validatorClass, $detailName) use ($details)
        {
            if (empty($details[$detailName])) {
                return null;
            }

            if (!$this->validateDetail($validatorClass, $details[$detailName])) {
                return null;
            }

            return $details[$detailName];
        })->toArray();
    }

    /**
     * Validate a value given as part of the command details
     *
     * @param $validatorClass
     * @param $detailValue
     * @return bool
     */
    protected function validateDetail($validatorClass, $detailValue)
    {
        if (!isset($validatorClass)) {
            return true;
        }

        if (!isset($detailValue)) {
            return false;
        }

        $validator = App::make($validatorClass);
        return $validator->validate($detailValue);
    }

    /**
     * @return Collection
     */
    public function getValidDetailAttributes()
    {
        return $this->validDetailAttributes;
    }

    /**
     * @param Collection $validDetailAttributes
     */
    public function setValidDetailAttributes($validDetailAttributes)
    {
        $this->validDetailAttributes = $validDetailAttributes;
    }
}