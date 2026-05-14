<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Space;
use App\Observers\SpaceObserver;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Space::observe(SpaceObserver::class);

        if (class_exists(Fortify::class)) {
            config(['fortify.home' => '/']);
        }
    }
}
