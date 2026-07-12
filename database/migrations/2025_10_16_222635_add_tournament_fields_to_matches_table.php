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
            if (!Schema::hasColumn('matches', 'phase')) {
                $table->string('phase')->nullable()->after('status')->comment('Tournament phase: groups, knockout');
            }
            if (!Schema::hasColumn('matches', 'tournament_group_id')) {
                // Positioned after `league_id`, not `competition_id`: on a fresh install this
                // migration runs before 2025_10_16_222823 adds `competition_id` to `matches`.
                $table->foreignId('tournament_group_id')->nullable()->after('league_id')->constrained('tournament_groups')->onDelete('cascade');
            }
            if (!Schema::hasColumn('matches', 'round_number')) {
                $table->integer('round_number')->nullable()->after('phase')->comment('Round number in knockout phase');
            }
            if (!Schema::hasColumn('matches', 'bracket_position')) {
                $table->integer('bracket_position')->nullable()->after('round_number')->comment('Position in knockout bracket');
            }
            if (!Schema::hasColumn('matches', 'is_bye')) {
                $table->boolean('is_bye')->default(false)->after('bracket_position')->comment('Whether this is a bye match');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            if (Schema::hasColumn('matches', 'tournament_group_id')) {
                $table->dropForeign(['tournament_group_id']);
            }
            $table->dropColumn([
                'phase',
                'tournament_group_id',
                'round_number',
                'bracket_position',
                'is_bye'
            ]);
        });
    }
};
