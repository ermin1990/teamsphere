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
        // This migration is handled by the comprehensive competition migration
        return;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove tournament-specific fields
        Schema::table('competition_matches', function (Blueprint $table) {
            $table->dropForeign(['tournament_group_id']);
            $table->dropColumn([
                'phase',
                'tournament_group_id',
                'round_number',
                'bracket_position',
                'is_bye'
            ]);
        });

        // Rename competition_matches table back to matches
        Schema::rename('competition_matches', 'matches');
    }
};
