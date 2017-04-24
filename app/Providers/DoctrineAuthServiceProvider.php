<?php

namespace App\Providers;

use App\Auth\CustomUserProvider;
use App\Entities\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\ServiceProvider;


class DoctrineAuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->extend('custom', function()
        {
            // Extend the apps authentication to use the Doctrine UserRepository
            $hasher     = App::make(Hasher::class);
            $repository = App::make(UserRepository::class);
            return new CustomUserProvider(new User(), $hasher, $repository);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}