<?php

namespace App\Providers;

use App\Services\AiSimulationService;
use App\Services\Contracts\AiSimulationServiceInterface;
use App\Services\Contracts\ProductivityServiceInterface;
use App\Services\ProductivityService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            AiSimulationServiceInterface::class,
            AiSimulationService::class
        );

        $this->app->bind(
            ProductivityServiceInterface::class,
            ProductivityService::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
