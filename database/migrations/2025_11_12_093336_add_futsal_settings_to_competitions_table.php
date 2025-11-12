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
            // Futsal match settings
            $table->integer('match_duration')->default(40)->after('tiebreak_points'); // Total match minutes
            $table->integer('halftime_duration')->default(10)->after('match_duration'); // Halftime minutes
            $table->integer('max_players_on_pitch')->default(5)->after('halftime_duration'); // Including goalkeeper
            $table->integer('min_players_on_pitch')->default(3)->after('max_players_on_pitch'); // Minimum to continue
            $table->integer('max_substitutes')->default(7)->after('min_players_on_pitch'); // Bench size
            $table->integer('unlimited_substitutions')->default(1)->after('max_substitutes'); // 1=yes, 0=no
            $table->integer('yellow_card_limit')->default(2)->after('unlimited_substitutions'); // 2 yellows = red
            $table->integer('red_card_suspension')->default(1)->after('yellow_card_limit'); // Matches suspended
            $table->boolean('has_overtime')->default(false)->after('red_card_suspension'); // Extra time
            $table->integer('overtime_duration')->nullable()->after('has_overtime'); // Extra time minutes
            $table->boolean('has_penalty_shootout')->default(true)->after('overtime_duration'); // Penalty shootout
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn([
                'match_duration',
                'halftime_duration',
                'max_players_on_pitch',
                'min_players_on_pitch',
                'max_substitutes',
                'unlimited_substitutions',
                'yellow_card_limit',
                'red_card_suspension',
                'has_overtime',
                'overtime_duration',
                'has_penalty_shootout',
            ]);
        });
    }
};
