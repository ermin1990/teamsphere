<?php

namespace App\Providers;

use App\Models\League;
use App\Models\Organization;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Clear organization cache when organization is updated
        Event::listen('eloquent.updated: App\Models\Organization', function ($organization) {
            Cache::forget('active_organizations');
            Cache::forget("user_{$organization->user_id}_organizations");
        });

        Event::listen('eloquent.created: App\Models\Organization', function ($organization) {
            Cache::forget('active_organizations');
            Cache::forget("user_{$organization->user_id}_organizations");
        });

        Event::listen('eloquent.deleted: App\Models\Organization', function ($organization) {
            Cache::forget('active_organizations');
            Cache::forget("user_{$organization->user_id}_organizations");
        });

        // Clear league cache when league is updated
        Event::listen('eloquent.updated: App\Models\League', function ($league) {
            Cache::forget('active_leagues');
            Cache::forget("organization_{$league->organization_id}_leagues");
        });

        Event::listen('eloquent.created: App\Models\League', function ($league) {
            Cache::forget('active_leagues');
            Cache::forget("organization_{$league->organization_id}_leagues");
        });

        Event::listen('eloquent.deleted: App\Models\League', function ($league) {
            Cache::forget('active_leagues');
            Cache::forget("organization_{$league->organization_id}_leagues");
        });
    }
}
