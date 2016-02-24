<?php

namespace App\Providers;

use App\Services\ImageService;
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
            return new ThirdPartyAuthService(config('settings.authentication_services'), $app['Laravel\Socialite\Contracts\Factory'], $app['Illuminate\Contracts\Auth\Factory'], $app['App\Services\RegistrationService'], $app['App\Repositories\UserRepository']);
        });

        $this->app->singleton(ImageService::class, function ($app) {
            return new ImageService(config('settings.image.max_width'), config('settings.image.max_height'), config('settings.image.max_filesize'), config('settings.image.mime_types'), config('settings.image.folder'), $app['Intervention\Image\ImageManager'], $app['\Illuminate\Contracts\Filesystem\Filesystem']);
        });
    }
}
