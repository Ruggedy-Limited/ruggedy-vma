<?php

return [

    /*
     * Entities Validation Rules
     *
     * Define the mapping between an entity and the domain rules to apply to this
     * entity. The domain rules are the most basic validation requirements that
     * ensure your entity is "valid". These can then be added to FormRequests and
     * used in other validation contexts.
     */
    'mappings' => [
        App\Entities\User::class => App\Validation\EntityRules\UserRules::class,
    ],

];
