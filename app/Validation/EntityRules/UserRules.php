<?php

namespace App\Validation\EntityRules;

use App\Entities\User;
use Somnambulist\EntityValidation\AbstractEntityRules;

class UserRules extends AbstractEntityRules
{
    /**
     * Return an array of rules for validating Users
     *
     * @param object $entity
     * @return array
     */
    protected function buildRules($entity)
    {
        return [
            'name'     => 'bail|required|min:2',
            'email'    => 'bail|required|email|unique:'. User::class .',email',
            'password' => 'required|min:8',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name'     => 'Name must be at least two characters long.',
            'email'    => 'A person with that email address is already registered.',
            'password' => 'Your password should be at least 8 characters long.',
        ];
    }

    /**
     * Define the entity that this validator supports
     *
     * @param object $entity
     * @return bool
     */
    public function supports($entity)
    {
        return $entity instanceof User;
    }
}