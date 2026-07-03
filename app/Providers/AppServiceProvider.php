<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App as LaravelApp;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        Schema::defaultStringLength(200);

        // Super Admin bypasse TOUTES les vérifications de permissions
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }
        });

        // Ensure human readable dates are in French for diffForHumans
        Carbon::setLocale('fr');

        // Push middleware that records user activity (online presence)
        if (isset($this->app['router'])) {
            $this->app['router']->pushMiddlewareToGroup('web', \App\Http\Middleware\RecordUserActivity::class);
        }
    }
}
