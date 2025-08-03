<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AdminService;
use App\Services\UserService;
use App\Services\TrainService;
use App\Services\QRScannerService;
use App\Services\NewsletterService;
use App\Services\RefundService;
use App\Facades\AdminFacade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Admin Facade and Services
        $this->app->singleton('admin.facade', function ($app) {
            return new AdminFacade();
        });

        $this->app->singleton(AdminService::class, function ($app) {
            return new AdminService();
        });

        $this->app->singleton(UserService::class, function ($app) {
            return new UserService();
        });

        $this->app->singleton(TrainService::class, function ($app) {
            return new TrainService();
        });

        $this->app->singleton(QRScannerService::class, function ($app) {
            return new QRScannerService();
        });

        $this->app->singleton(NewsletterService::class, function ($app) {
            return new NewsletterService();
        });

        $this->app->singleton(RefundService::class, function ($app) {
            return new RefundService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
