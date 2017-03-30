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
            'name'     => 'bail|required|min:1',
            'email'    => 'bail|required|email|unique:'. User::class .',email',
            'password' => 'required|min:8',
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