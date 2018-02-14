<?php

namespace App\Providers;

use App\Word;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('Illuminate\Auth\Events\Login', function ($event) {
            session()->flash('showHelloMessage', true);
            session()->flash('numberOfWords', Word::count());
        });
    }
}
