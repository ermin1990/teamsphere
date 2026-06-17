<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bezbjedno dodavanje indeksa za 'matches' tabelu
        if (Schema::hasTable('matches')) {
            DB::statement('CREATE INDEX IF NOT EXISTS matches_league_status_critical ON matches (league_id, status)');
            DB::statement('CREATE INDEX IF NOT EXISTS matches_status_scheduled_critical ON matches (status, scheduled_at)');
            DB::statement('CREATE INDEX IF NOT EXISTS matches_dates_critical ON matches (scheduled_at, played_at)');
        }

        // 2. Bezbjedno dodavanje indeksa za 'standings' tabelu
        if (Schema::hasTable('standings')) {
            DB::statement('CREATE INDEX IF NOT EXISTS standings_league_points_critical ON standings (league_id, points)');
            DB::statement('CREATE INDEX IF NOT EXISTS standings_league_played_critical ON standings (league_id, played)');
        }

        // 3. Bezbjedno dodavanje indeksa za 'friendly_matches' tabelu
        if (Schema::hasTable('friendly_matches')) {
            DB::statement('CREATE INDEX IF NOT EXISTS friendly_home_player_status ON friendly_matches (home_player_id, status)');
            DB::statement('CREATE INDEX IF NOT EXISTS friendly_away_player_status ON friendly_matches (away_player_id, status)');
            DB::statement('CREATE INDEX IF NOT EXISTS friendly_scheduled_at ON friendly_matches (scheduled_at)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Bezbjedno brisanje indeksa ako postoje (podržano i u Postgresu i u SQLite-u)
        DB::statement('DROP INDEX IF EXISTS matches_league_status_critical');
        DB::statement('DROP INDEX IF EXISTS matches_status_scheduled_critical');
        DB::statement('DROP INDEX IF EXISTS matches_dates_critical');

        DB::statement('DROP INDEX IF EXISTS standings_league_points_critical');
        DB::statement('DROP INDEX IF EXISTS standings_league_played_critical');

        DB::statement('DROP INDEX IF EXISTS friendly_home_player_status');
        DB::statement('DROP INDEX IF EXISTS friendly_away_player_status');
        DB::statement('DROP INDEX IF EXISTS friendly_scheduled_at');
    }
};