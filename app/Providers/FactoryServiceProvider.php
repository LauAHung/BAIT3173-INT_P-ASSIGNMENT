<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Factories\UserFactoryManager;
use App\Factories\AuthFactoryManager;
use App\Factories\MailFactoryManager;

class FactoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(UserFactoryManager::class, function ($app) {
            return new UserFactoryManager();
        });

        $this->app->singleton(AuthFactoryManager::class, function ($app) {
            return new AuthFactoryManager();
        });

        $this->app->singleton(MailFactoryManager::class, function ($app) {
            return new MailFactoryManager();
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
