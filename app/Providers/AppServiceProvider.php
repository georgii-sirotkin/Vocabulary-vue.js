<?php

namespace App\Providers;

use App\Services\ThirdPartyAuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ThirdPartyAuthService::class, function ($app) {
            return new ThirdPartyAuthService(['facebook'], $app['Laravel\Socialite\Contracts\Factory'], $app['Illuminate\Contracts\Auth\Factory'], $app['App\Services\RegistrationService']);
        });
    }
}
