<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Admins bypass every policy check (view/update/delete organizations,
        // competitions, etc.) so the admin panel can inspect any user's data
        // without being blocked by ownership-based authorization.
        Gate::before(function ($user, string $ability) {
            return $user && $user->isAdmin() ? true : null;
        });
    }
}
