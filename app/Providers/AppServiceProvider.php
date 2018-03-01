<?php

namespace App\Providers;

use App\Repositories\WordRepository;
use App\Services\CheckAnswerService;
use App\Services\ImageService;
use App\Services\RandomWordService;
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
            return new ImageService(config('settings.image.max_width'), config('settings.image.max_height'), config('settings.image.folder'), $app['Illuminate\Contracts\Filesystem\Factory']);
        });

        $this->app->singleton(RandomWordService::class, function ($app) {
            return new RandomWordService($app['App\Repositories\WordRepository'], $app['Illuminate\Session\SessionManager'], config('settings.number_of_words_to_remember'));
        });

        $this->app->singleton(WordRepository::class, function ($app) {
            return new WordRepository(config('settings.min_number_of_chars_per_one_mistake_in_search'));
        });

        $this->app->singleton(CheckAnswerService::class, function ($app) {
            return new CheckAnswerService($app['Illuminate\Session\SessionManager'], config('settings.min_number_of_chars_per_one_mistake'));
        });

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
