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
        // This migration is handled by the comprehensive competition migration
        // Skip to avoid conflicts
        return;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes with competition_id
        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex('matches_competition_status_critical');
            $table->dropIndex('matches_status_scheduled_critical');
            $table->dropIndex('matches_dates_critical');
        });

        // Reverse the column rename
        Schema::table('matches', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['competition_id']);

            // Rename the column back from competition_id to league_id
            $table->renameColumn('competition_id', 'league_id');

            // Add the old foreign key constraint
            $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
        });

        // Recreate indexes with league_id
        Schema::table('matches', function (Blueprint $table) {
            $table->index(['league_id', 'status'], 'matches_league_status_critical');
            $table->index(['status', 'scheduled_at'], 'matches_status_scheduled_critical');
            $table->index(['scheduled_at', 'played_at'], 'matches_dates_critical');
        });
    }
};
