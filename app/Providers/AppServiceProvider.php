<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PointLedgerService;
use App\Services\RuleEngineService;
use App\Services\TokenInterceptorService;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ... binding yang sudah ada ...

        $this->app->singleton(PointLedgerService::class);
        $this->app->singleton(RuleEngineService::class);
        $this->app->singleton(TokenInterceptorService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
