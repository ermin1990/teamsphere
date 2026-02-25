<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Critical composite indexes for matches table
        Schema::table('matches', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('matches'))->pluck('name')->toArray();

            if (!in_array('matches_league_status_critical', $indexes)) {
                $table->index(['league_id', 'status'], 'matches_league_status_critical');
            }
            if (!in_array('matches_status_scheduled_critical', $indexes)) {
                $table->index(['status', 'scheduled_at'], 'matches_status_scheduled_critical');
            }
            if (!in_array('matches_dates_critical', $indexes)) {
                $table->index(['scheduled_at', 'played_at'], 'matches_dates_critical');
            }
        });

        // Critical indexes for standings table
        Schema::table('standings', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('standings'))->pluck('name')->toArray();

            if (!in_array('standings_league_points_critical', $indexes)) {
                $table->index(['league_id', 'points'], 'standings_league_points_critical');
            }
            if (!in_array('standings_league_played_critical', $indexes)) {
                $table->index(['league_id', 'played'], 'standings_league_played_critical');
            }
        });

        // Critical indexes for friendly matches
        Schema::table('friendly_matches', function (Blueprint $table) {
            if (Schema::hasColumn('friendly_matches', 'status')) {
                $table->index(['home_player_id', 'status'], 'friendly_home_player_status');
                $table->index(['away_player_id', 'status'], 'friendly_away_player_status');
            }
            if (Schema::hasColumn('friendly_matches', 'scheduled_at')) {
                $table->index('scheduled_at', 'friendly_scheduled_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex('matches_league_status_critical');
            $table->dropIndex('matches_status_scheduled_critical');
            $table->dropIndex('matches_dates_critical');
        });

        Schema::table('standings', function (Blueprint $table) {
            $table->dropIndex('standings_league_points_critical');
            $table->dropIndex('standings_league_played_critical');
        });

        Schema::table('friendly_matches', function (Blueprint $table) {
            $table->dropIndex('friendly_home_player_status');
            $table->dropIndex('friendly_away_player_status');
            $table->dropIndex('friendly_scheduled_at');
        });
    }
};
