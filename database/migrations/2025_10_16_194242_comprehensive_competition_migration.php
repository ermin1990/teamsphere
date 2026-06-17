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
        // First, drop all problematic indexes
        try {
            DB::statement('DROP INDEX IF EXISTS organizations_plan_id_is_active_index');
            DB::statement('DROP INDEX IF EXISTS matches_league_status_critical');
            DB::statement('DROP INDEX IF EXISTS matches_status_scheduled_critical');
            DB::statement('DROP INDEX IF EXISTS matches_dates_critical');
            DB::statement('DROP INDEX IF EXISTS standings_league_points_critical');
            DB::statement('DROP INDEX IF EXISTS standings_league_played_critical');
            // Drop friendly match indexes that reference non-existent columns
            DB::statement('DROP INDEX IF EXISTS friendly_home_player_status');
            DB::statement('DROP INDEX IF EXISTS friendly_away_player_status');
            DB::statement('DROP INDEX IF EXISTS friendly_scheduled_at');
        } catch (\Exception $e) {
            // Ignore if indexes don't exist
        }

        // Add type field to competitions table if it doesn't exist
        if (!Schema::hasColumn('competitions', 'type')) {
            Schema::table('competitions', function (Blueprint $table) {
                $table->enum('type', ['league', 'tournament'])->default('league')->after('sport_id');
            });
        }

        // Update all foreign key constraints and rename columns
        // Skip matches table as it may have been already modified or has different structure
        /*
        if (Schema::hasColumn('matches', 'league_id')) {
            Schema::table('matches', function (Blueprint $table) {
                $table->dropForeign(['league_id']);
                $table->renameColumn('league_id', 'competition_id');
                $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
            });
        }
        */

        if (Schema::hasColumn('teams', 'league_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropForeign(['league_id']);
                $table->renameColumn('league_id', 'competition_id');
                $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
            });
        }

        if (Schema::hasColumn('standings', 'league_id')) {
            Schema::table('standings', function (Blueprint $table) {
                $table->dropForeign(['league_id']);
                $table->renameColumn('league_id', 'competition_id');
                $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('league_user') && Schema::hasColumn('league_user', 'league_id')) {
            Schema::table('league_user', function (Blueprint $table) {
                $table->dropForeign(['league_id']);
                $table->renameColumn('league_id', 'competition_id');
                $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('league_player') && Schema::hasColumn('league_player', 'league_id')) {
            Schema::table('league_player', function (Blueprint $table) {
                $table->dropForeign(['league_id']);
                $table->renameColumn('league_id', 'competition_id');
                $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
            });
        }

        // Rename tables (only if they still exist)
        if (Schema::hasTable('leagues')) {
            Schema::rename('leagues', 'competitions');
        }
        if (Schema::hasTable('league_user')) {
            Schema::rename('league_user', 'competition_user');
        }
        if (Schema::hasTable('league_player')) {
            Schema::rename('league_player', 'competition_player');
        }

        // Create new indexes (guarded)
        if (Schema::hasTable('matches')) {
            $cols = Schema::getColumnListing('matches');
            if (in_array('competition_id', $cols) && in_array('status', $cols)) {
                DB::statement('CREATE INDEX IF NOT EXISTS matches_competition_status_critical ON matches (competition_id, status)');
            }
            if (in_array('status', $cols) && in_array('scheduled_at', $cols)) {
                DB::statement('CREATE INDEX IF NOT EXISTS matches_status_scheduled_critical ON matches (status, scheduled_at)');
            }
            if (in_array('scheduled_at', $cols) && in_array('played_at', $cols)) {
                DB::statement('CREATE INDEX IF NOT EXISTS matches_dates_critical ON matches (scheduled_at, played_at)');
            }
        }

        if (Schema::hasTable('standings')) {
            $scols = Schema::getColumnListing('standings');
            if (in_array('competition_id', $scols) && in_array('points', $scols)) {
                DB::statement('CREATE INDEX IF NOT EXISTS standings_competition_points_critical ON standings (competition_id, points)');
            }
            if (in_array('competition_id', $scols) && in_array('played', $scols)) {
                DB::statement('CREATE INDEX IF NOT EXISTS standings_competition_played_critical ON standings (competition_id, played)');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new indexes (guarded)
        if (Schema::hasTable('matches')) {
            $mcols = Schema::getColumnListing('matches');
            if (in_array('competition_id', $mcols) && in_array('status', $mcols)) {
                try { DB::statement('DROP INDEX IF EXISTS matches_competition_status_critical'); } catch (\Exception $e) {}
            }
            if (in_array('status', $mcols) && in_array('scheduled_at', $mcols)) {
                try { DB::statement('DROP INDEX IF EXISTS matches_status_scheduled_critical'); } catch (\Exception $e) {}
            }
            if (in_array('scheduled_at', $mcols) && in_array('played_at', $mcols)) {
                try { DB::statement('DROP INDEX IF EXISTS matches_dates_critical'); } catch (\Exception $e) {}
            }
        }

        if (Schema::hasTable('standings')) {
            $scols = Schema::getColumnListing('standings');
            if (in_array('competition_id', $scols) && in_array('points', $scols)) {
                try { DB::statement('DROP INDEX IF EXISTS standings_competition_points_critical'); } catch (\Exception $e) {}
            }
            if (in_array('competition_id', $scols) && in_array('played', $scols)) {
                try { DB::statement('DROP INDEX IF EXISTS standings_competition_played_critical'); } catch (\Exception $e) {}
            }
        }

        // Rename tables back (guarded)
        if (Schema::hasTable('competitions')) {
            Schema::rename('competitions', 'leagues');
        }
        if (Schema::hasTable('competition_user')) {
            Schema::rename('competition_user', 'league_user');
        }
        if (Schema::hasTable('competition_player')) {
            Schema::rename('competition_player', 'league_player');
        }

        // Reverse column renames and foreign keys (guarded)
        if (Schema::hasTable('matches') && Schema::hasColumn('matches', 'competition_id')) {
            Schema::table('matches', function (Blueprint $table) {
                try { $table->dropForeign(['competition_id']); } catch (\Exception $e) {}
                $table->renameColumn('competition_id', 'league_id');
                try { $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade'); } catch (\Exception $e) {}
            });
        }

        if (Schema::hasTable('teams') && Schema::hasColumn('teams', 'competition_id')) {
            Schema::table('teams', function (Blueprint $table) {
                try { $table->dropForeign(['competition_id']); } catch (\Exception $e) {}
                $table->renameColumn('competition_id', 'league_id');
                try { $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade'); } catch (\Exception $e) {}
            });
        }

        if (Schema::hasTable('standings') && Schema::hasColumn('standings', 'competition_id')) {
            Schema::table('standings', function (Blueprint $table) {
                try { $table->dropForeign(['competition_id']); } catch (\Exception $e) {}
                $table->renameColumn('competition_id', 'league_id');
                try { $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade'); } catch (\Exception $e) {}
            });
        }

        if (Schema::hasTable('league_user') && Schema::hasColumn('league_user', 'competition_id')) {
            Schema::table('league_user', function (Blueprint $table) {
                try { $table->dropForeign(['competition_id']); } catch (\Exception $e) {}
                $table->renameColumn('competition_id', 'league_id');
                try { $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade'); } catch (\Exception $e) {}
            });
        }

        if (Schema::hasTable('league_player') && Schema::hasColumn('league_player', 'competition_id')) {
            Schema::table('league_player', function (Blueprint $table) {
                try { $table->dropForeign(['competition_id']); } catch (\Exception $e) {}
                $table->renameColumn('competition_id', 'league_id');
                try { $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade'); } catch (\Exception $e) {}
            });
        }

        // Remove type field if exists
        if (Schema::hasTable('leagues') && Schema::hasColumn('leagues', 'type')) {
            Schema::table('leagues', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        // Recreate old indexes (guarded)
        if (Schema::hasTable('matches')) {
            $mcols = Schema::getColumnListing('matches');
            if (in_array('league_id', $mcols) && in_array('status', $mcols)) {
                DB::statement('CREATE INDEX IF NOT EXISTS matches_league_status_critical ON matches (league_id, status)');
            }
            if (in_array('status', $mcols) && in_array('scheduled_at', $mcols)) {
                DB::statement('CREATE INDEX IF NOT EXISTS matches_status_scheduled_critical ON matches (status, scheduled_at)');
            }
            if (in_array('scheduled_at', $mcols) && in_array('played_at', $mcols)) {
                DB::statement('CREATE INDEX IF NOT EXISTS matches_dates_critical ON matches (scheduled_at, played_at)');
            }
        }

        if (Schema::hasTable('standings')) {
            $scols = Schema::getColumnListing('standings');
            if (in_array('league_id', $scols) && in_array('points', $scols)) {
                DB::statement('CREATE INDEX IF NOT EXISTS standings_league_points_critical ON standings (league_id, points)');
            }
            if (in_array('league_id', $scols) && in_array('played', $scols)) {
                DB::statement('CREATE INDEX IF NOT EXISTS standings_league_played_critical ON standings (league_id, played)');
            }
        }
    }
};
