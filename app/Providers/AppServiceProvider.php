<?php

namespace App\Providers;

use App\Services\CurrencyService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/CurrencyHelper.php');
        require_once app_path('Helpers/MbstringFallback.php');

        $this->app->singleton(CurrencyService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
