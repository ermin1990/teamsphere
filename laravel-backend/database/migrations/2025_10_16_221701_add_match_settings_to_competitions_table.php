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
        Schema::table('competitions', function (Blueprint $table) {
            // Match settings
            if (!Schema::hasColumn('competitions', 'sets_to_win')) {
                $table->integer('sets_to_win')->default(2)->after('advancement_method')->comment('Number of sets needed to win a match');
            }
            if (!Schema::hasColumn('competitions', 'points_per_set')) {
                $table->integer('points_per_set')->default(11)->after('sets_to_win')->comment('Points needed to win a set');
            }
            if (!Schema::hasColumn('competitions', 'deuce_at')) {
                $table->integer('deuce_at')->nullable()->after('points_per_set')->comment('Score at which deuce rule applies (e.g., 10 for 11-point game)');
            }
            if (!Schema::hasColumn('competitions', 'must_win_by_two')) {
                $table->boolean('must_win_by_two')->default(true)->after('deuce_at')->comment('Must win by 2 points at deuce');
            }
            
            // Group stage scoring
            if (!Schema::hasColumn('competitions', 'points_for_win')) {
                $table->integer('points_for_win')->default(2)->after('must_win_by_two')->comment('Points awarded for a win in group stage');
            }
            if (!Schema::hasColumn('competitions', 'points_for_draw')) {
                $table->integer('points_for_draw')->default(1)->after('points_for_win')->comment('Points awarded for a draw');
            }
            if (!Schema::hasColumn('competitions', 'points_for_loss')) {
                $table->integer('points_for_loss')->default(0)->after('points_for_draw')->comment('Points awarded for a loss');
            }
            
            // Tiebreak settings
            if (!Schema::hasColumn('competitions', 'has_tiebreak')) {
                $table->boolean('has_tiebreak')->default(false)->after('points_for_loss')->comment('Whether to use tiebreak in final set');
            }
            if (!Schema::hasColumn('competitions', 'tiebreak_points')) {
                $table->integer('tiebreak_points')->nullable()->after('has_tiebreak')->comment('Points needed to win tiebreak');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn([
                'sets_to_win',
                'points_per_set',
                'deuce_at',
                'must_win_by_two',
                'points_for_win',
                'points_for_draw',
                'points_for_loss',
                'has_tiebreak',
                'tiebreak_points'
            ]);
        });
    }
};
