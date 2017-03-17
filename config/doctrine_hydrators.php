<?php

return [

    /*
     * Cache Path
     *
     * Directory to store the generated hydrator classes in. Default is cache/hydrators
     * in storage_path(). Any path will be auto-created within storage_path().
     *
     * Note: be sure to add this path to your composer.json autoload.classmap options
     * for consistent autoloading.
     */
    'cache_path' => 'cache/hydrators',

    /*
     * Entities
     *
     * The list of entity classes to generate hydrators for. Use the ::class pseudo
     * property e.g.: App\Entities\User::class.
     */
    'entities' => [
        App\Entities\User::class
    ],

];
