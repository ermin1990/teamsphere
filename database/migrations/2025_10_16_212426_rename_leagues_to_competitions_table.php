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
        // This table was already renamed by an earlier migration
        return;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove tournament-specific fields
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'max_participants',
                'group_count',
                'players_per_group',
                'players_advancing_per_group',
                'advancement_method',
                'current_phase',
                'knockout_bracket',
                'groups_completed_at',
                'knockout_completed_at'
            ]);
        });

        // Rename competitions table back to leagues
        Schema::rename('competitions', 'leagues');
    }
};
