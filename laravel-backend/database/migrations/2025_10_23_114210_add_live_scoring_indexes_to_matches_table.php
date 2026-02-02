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
        Schema::table('matches', function (Blueprint $table) {
            // Index for live scoring - quickly find active matches
            $table->index(['status', 'updated_at'], 'matches_status_updated_live_scoring');
            
            // Index for cache invalidation - find recently updated matches
            $table->index('updated_at', 'matches_updated_at_live_scoring');
            
            // Composite index for competition live matches
            $table->index(['competition_id', 'status', 'updated_at'], 'matches_competition_status_updated_live');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex('matches_status_updated_live_scoring');
            $table->dropIndex('matches_updated_at_live_scoring');
            $table->dropIndex('matches_competition_status_updated_live');
        });
    }
};
