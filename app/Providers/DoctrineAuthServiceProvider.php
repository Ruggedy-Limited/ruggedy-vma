<?php

namespace App\Providers;

use App\Auth\DoctrineUserProvider;
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
        $this->app['auth']->extend('doctrine', function()
        {
            $hasher     = App::make(Hasher::class);
            $repository = App::make(UserRepository::class);
            return new DoctrineUserProvider(new User(), $hasher, $repository);
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