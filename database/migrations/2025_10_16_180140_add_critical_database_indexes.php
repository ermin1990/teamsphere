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
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('CREATE INDEX IF NOT EXISTS matches_league_status_critical ON matches (league_id, status)');
                DB::statement('CREATE INDEX IF NOT EXISTS matches_status_scheduled_critical ON matches (status, scheduled_at)');
                DB::statement('CREATE INDEX IF NOT EXISTS matches_dates_critical ON matches (scheduled_at, played_at)');
            } else {
                try {
                    Schema::table('matches', function (Blueprint $table) {
                        $table->index(['league_id', 'status'], 'matches_league_status_critical');
                        $table->index(['status', 'scheduled_at'], 'matches_status_scheduled_critical');
                        $table->index(['scheduled_at', 'played_at'], 'matches_dates_critical');
                    });
                } catch (\Exception $e) {
                    // ignore if index exists or unsupported
                }
            }
        }

        // 2. Bezbjedno dodavanje indeksa za 'standings' tabelu
        if (Schema::hasTable('standings')) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('CREATE INDEX IF NOT EXISTS standings_league_points_critical ON standings (league_id, points)');
                DB::statement('CREATE INDEX IF NOT EXISTS standings_league_played_critical ON standings (league_id, played)');
            } else {
                try {
                    Schema::table('standings', function (Blueprint $table) {
                        $table->index(['league_id', 'points'], 'standings_league_points_critical');
                        $table->index(['league_id', 'played'], 'standings_league_played_critical');
                    });
                } catch (\Exception $e) {
                    // ignore
                }
            }
        }

        // 3. Bezbjedno dodavanje indeksa za 'friendly_matches' tabelu
        if (Schema::hasTable('friendly_matches')) {
            if (DB::getDriverName() === 'pgsql') {
                if (Schema::hasColumn('friendly_matches', 'home_player_id') && Schema::hasColumn('friendly_matches', 'status')) {
                    DB::statement('CREATE INDEX IF NOT EXISTS friendly_home_player_status ON friendly_matches (home_player_id, status)');
                }
                if (Schema::hasColumn('friendly_matches', 'away_player_id') && Schema::hasColumn('friendly_matches', 'status')) {
                    DB::statement('CREATE INDEX IF NOT EXISTS friendly_away_player_status ON friendly_matches (away_player_id, status)');
                }
                if (Schema::hasColumn('friendly_matches', 'scheduled_at')) {
                    DB::statement('CREATE INDEX IF NOT EXISTS friendly_scheduled_at ON friendly_matches (scheduled_at)');
                }
            } else {
                try {
                    Schema::table('friendly_matches', function (Blueprint $table) {
                        if (Schema::hasColumn('friendly_matches', 'home_player_id') && Schema::hasColumn('friendly_matches', 'status')) {
                            $table->index(['home_player_id', 'status'], 'friendly_home_player_status');
                        }
                        if (Schema::hasColumn('friendly_matches', 'away_player_id') && Schema::hasColumn('friendly_matches', 'status')) {
                            $table->index(['away_player_id', 'status'], 'friendly_away_player_status');
                        }
                        if (Schema::hasColumn('friendly_matches', 'scheduled_at')) {
                            $table->index(['scheduled_at'], 'friendly_scheduled_at');
                        }
                    });
                } catch (\Exception $e) {
                    // ignore
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Bezbjedno brisanje indeksa ako postoje (podržano i u Postgresu i u SQLite-u)
        // Drop indexes in a driver-aware way
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS matches_league_status_critical');
            DB::statement('DROP INDEX IF EXISTS matches_status_scheduled_critical');
            DB::statement('DROP INDEX IF EXISTS matches_dates_critical');

            DB::statement('DROP INDEX IF EXISTS standings_league_points_critical');
            DB::statement('DROP INDEX IF EXISTS standings_league_played_critical');

            DB::statement('DROP INDEX IF EXISTS friendly_home_player_status');
            DB::statement('DROP INDEX IF EXISTS friendly_away_player_status');
            DB::statement('DROP INDEX IF EXISTS friendly_scheduled_at');
        } else {
            try {
                Schema::table('matches', function (Blueprint $table) {
                    $table->dropIndex('matches_league_status_critical');
                    $table->dropIndex('matches_status_scheduled_critical');
                    $table->dropIndex('matches_dates_critical');
                });
            } catch (\Exception $e) {}

            try {
                Schema::table('standings', function (Blueprint $table) {
                    $table->dropIndex('standings_league_points_critical');
                    $table->dropIndex('standings_league_played_critical');
                });
            } catch (\Exception $e) {}

            try {
                Schema::table('friendly_matches', function (Blueprint $table) {
                    $table->dropIndex('friendly_home_player_status');
                    $table->dropIndex('friendly_away_player_status');
                    $table->dropIndex('friendly_scheduled_at');
                });
            } catch (\Exception $e) {}
        }
    }
};