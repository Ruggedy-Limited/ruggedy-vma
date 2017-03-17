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
            'name'     => 'required|min:1',
            'email'    => 'required|email|unique:'. User::class .',email,' . ($entity->getId() ?: 'null'),
            'username' => 'required|alphanum|unique:' . User::class . ',username,' . ($entity->getId() ?: 'null'),
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