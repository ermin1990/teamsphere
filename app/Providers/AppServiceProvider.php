<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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

        Event::listen(Registered::class, SendEmailVerificationNotification::class);

        // Admins bypass every policy check (view/update/delete organizations,
        // competitions, etc.) so the admin panel can inspect any user's data
        // without being blocked by ownership-based authorization.
        Gate::before(function ($user, string $ability) {
            return $user && $user->isAdmin() ? true : null;
        });

        $this->applyMailSettingsOverride();
    }

    /**
     * Lets the "from" address/name set in /admin/settings override the .env
     * default at runtime, without requiring a redeploy to change it. Guarded
     * behind hasTable() since this runs on every boot, including `artisan
     * migrate` itself before the settings table exists on a fresh install.
     */
    private function applyMailSettingsOverride(): void
    {
        try {
            if (!Schema::hasTable('settings')) {
                return;
            }
        } catch (\Exception $e) {
            return;
        }

        $fromAddress = Setting::get('mail_from_address');
        $fromName = Setting::get('mail_from_name');

        if ($fromAddress) {
            config(['mail.from.address' => $fromAddress]);
        }
        if ($fromName) {
            config(['mail.from.name' => $fromName]);
        }
    }
}
