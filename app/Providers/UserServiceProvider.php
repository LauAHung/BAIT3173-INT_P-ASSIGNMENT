<?php

namespace App\Providers;

use App\Builders\UserBuilder;
use App\Services\UserRegistrationService;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(UserBuilder::class, function ($app) {
            return new UserBuilder();
        });

        $this->app->singleton(UserRegistrationService::class, function ($app) {
            return new UserRegistrationService($app->make(UserBuilder::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 