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
        if (Schema::hasColumn('matches', 'league_id')) {
            Schema::table('matches', function (Blueprint $table) {
                // Check if foreign key exists before dropping
                // $table->dropForeign(['league_id']); // This might fail if it doesn't have a name or doesn't exist
                $table->renameColumn('league_id', 'competition_id');
                // $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
            });
        }

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

        // Create new indexes
        Schema::table('matches', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('matches'))->pluck('name')->toArray();
            if (!in_array('matches_competition_status_critical', $indexes)) {
                $table->index(['competition_id', 'status'], 'matches_competition_status_critical');
            }
            if (!in_array('matches_status_scheduled_critical', $indexes)) {
                $table->index(['status', 'scheduled_at'], 'matches_status_scheduled_critical');
            }
            if (!in_array('matches_dates_critical', $indexes)) {
                $table->index(['scheduled_at', 'played_at'], 'matches_dates_critical');
            }
        });

        Schema::table('standings', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('standings'))->pluck('name')->toArray();
            if (!in_array('standings_competition_points_critical', $indexes)) {
                $table->index(['competition_id', 'points'], 'standings_competition_points_critical');
            }
            if (!in_array('standings_competition_played_critical', $indexes)) {
                $table->index(['competition_id', 'played'], 'standings_competition_played_critical');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new indexes
        Schema::table('matches', function (Blueprint $table) {
            try { $table->dropIndex('matches_competition_status_critical'); } catch (\Exception $e) {}
            try { $table->dropIndex('matches_status_scheduled_critical'); } catch (\Exception $e) {}
            try { $table->dropIndex('matches_dates_critical'); } catch (\Exception $e) {}
        });

        Schema::table('standings', function (Blueprint $table) {
            try { $table->dropIndex('standings_competition_points_critical'); } catch (\Exception $e) {}
            try { $table->dropIndex('standings_competition_played_critical'); } catch (\Exception $e) {}
        });

        // Rename tables back
        Schema::rename('competitions', 'leagues');
        Schema::rename('competition_user', 'league_user');
        Schema::rename('competition_player', 'league_player');

        // Reverse column renames and foreign keys
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['competition_id']);
            $table->renameColumn('competition_id', 'league_id');
            $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['competition_id']);
            $table->renameColumn('competition_id', 'league_id');
            $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
        });

        Schema::table('standings', function (Blueprint $table) {
            $table->dropForeign(['competition_id']);
            $table->renameColumn('competition_id', 'league_id');
            $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
        });

        Schema::table('league_user', function (Blueprint $table) {
            $table->dropForeign(['competition_id']);
            $table->renameColumn('competition_id', 'league_id');
            $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
        });

        Schema::table('league_player', function (Blueprint $table) {
            $table->dropForeign(['competition_id']);
            $table->renameColumn('competition_id', 'league_id');
            $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
        });

        // Remove type field
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        // Recreate old indexes
        Schema::table('matches', function (Blueprint $table) {
            $table->index(['league_id', 'status'], 'matches_league_status_critical');
            $table->index(['status', 'scheduled_at'], 'matches_status_scheduled_critical');
            $table->index(['scheduled_at', 'played_at'], 'matches_dates_critical');
        });

        Schema::table('standings', function (Blueprint $table) {
            $table->index(['league_id', 'points'], 'standings_league_points_critical');
            $table->index(['league_id', 'played'], 'standings_league_played_critical');
        });
    }
};
