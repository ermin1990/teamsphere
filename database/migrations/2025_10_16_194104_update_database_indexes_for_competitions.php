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
        // Drop old indexes that reference league_id
        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex('matches_league_status_critical');
        });

        Schema::table('standings', function (Blueprint $table) {
            $table->dropIndex('standings_league_points_critical');
            $table->dropIndex('standings_league_played_critical');
        });

        // Create new indexes with competition_id
        Schema::table('matches', function (Blueprint $table) {
            $table->index(['competition_id', 'status'], 'matches_competition_status_critical');
        });

        Schema::table('standings', function (Blueprint $table) {
            $table->index(['competition_id', 'points'], 'standings_competition_points_critical');
            $table->index(['competition_id', 'played'], 'standings_competition_played_critical');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new indexes
        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex('matches_competition_status_critical');
        });

        Schema::table('standings', function (Blueprint $table) {
            $table->dropIndex('standings_competition_points_critical');
            $table->dropIndex('standings_competition_played_critical');
        });

        // Recreate old indexes with league_id
        Schema::table('matches', function (Blueprint $table) {
            $table->index(['league_id', 'status'], 'matches_league_status_critical');
        });

        Schema::table('standings', function (Blueprint $table) {
            $table->index(['league_id', 'points'], 'standings_league_points_critical');
            $table->index(['league_id', 'played'], 'standings_league_played_critical');
        });
    }
};
