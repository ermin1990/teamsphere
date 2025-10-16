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
        // Leagues table - indeksi za performanse
        Schema::table('leagues', function (Blueprint $table) {
            $table->index(['status', 'is_active'], 'leagues_status_active_index');
            $table->index('start_date', 'leagues_start_date_index');
            $table->index('end_date', 'leagues_end_date_index');
            $table->index(['is_active', 'start_date'], 'leagues_active_start_index');
        });

        // Matches table - indeksi za performanse
        Schema::table('matches', function (Blueprint $table) {
            $table->index(['league_id', 'status'], 'matches_league_status_index');
            $table->index(['status', 'scheduled_at'], 'matches_status_scheduled_index');
            $table->index('scheduled_at', 'matches_scheduled_at_index');
            $table->index('played_at', 'matches_played_at_index');
            $table->index(['home_team_id', 'status'], 'matches_home_team_status_index');
            $table->index(['away_team_id', 'status'], 'matches_away_team_status_index');
        });

        // Organizations table - indeksi za performanse
        Schema::table('organizations', function (Blueprint $table) {
            $table->index(['user_id', 'is_active'], 'organizations_user_active_index');
            $table->index(['plan_id', 'is_active'], 'organizations_plan_active_index');
            $table->index('is_active', 'organizations_active_index');
        });

        // Players table - indeksi za performanse
        Schema::table('players', function (Blueprint $table) {
            $table->index('is_active', 'players_active_index');
        });

        // Teams table - indeksi za performanse
        Schema::table('teams', function (Blueprint $table) {
            $table->index(['league_id', 'is_active'], 'teams_league_active_index');
            $table->index('is_active', 'teams_active_index');
        });

        // Standings table - indeksi za performanse
        Schema::table('standings', function (Blueprint $table) {
            $table->index(['league_id', 'points'], 'standings_league_points_index');
            $table->index(['league_id', 'played'], 'standings_league_played_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Leagues table - ukloni indekse
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropIndex('leagues_status_active_index');
            $table->dropIndex('leagues_start_date_index');
            $table->dropIndex('leagues_end_date_index');
            $table->dropIndex('leagues_active_start_index');
        });

        // Matches table - ukloni indekse
        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex('matches_league_status_index');
            $table->dropIndex('matches_status_scheduled_index');
            $table->dropIndex('matches_scheduled_at_index');
            $table->dropIndex('matches_played_at_index');
            $table->dropIndex('matches_home_team_status_index');
            $table->dropIndex('matches_away_team_status_index');
        });

        // Organizations table - ukloni indekse
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropIndex('organizations_user_active_index');
            $table->dropIndex('organizations_plan_active_index');
            $table->dropIndex('organizations_active_index');
        });

        // Players table - ukloni indekse
        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex('players_active_index');
        });

        // Teams table - ukloni indekse
        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex('teams_league_active_index');
            $table->dropIndex('teams_active_index');
        });

        // Standings table - ukloni indekse
        Schema::table('standings', function (Blueprint $table) {
            $table->dropIndex('standings_league_points_index');
            $table->dropIndex('standings_league_played_index');
        });
    }
};
