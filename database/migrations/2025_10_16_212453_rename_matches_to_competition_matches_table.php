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
        // Rename matches table to competition_matches
        Schema::rename('matches', 'competition_matches');

        // Add tournament-specific fields
        Schema::table('competition_matches', function (Blueprint $table) {
            $table->enum('phase', ['group', 'round_of_16', 'quarter_final', 'semi_final', 'final', 'third_place'])->nullable()->after('status');
            $table->foreignId('tournament_group_id')->nullable()->constrained('tournament_groups')->onDelete('set null')->after('phase');
            $table->integer('round_number')->nullable()->after('tournament_group_id'); // For knockout rounds
            $table->integer('bracket_position')->nullable()->after('round_number'); // Position in bracket
            $table->boolean('is_bye')->default(false)->after('bracket_position'); // For odd number of players
        });
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
