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
        // Live point/game tally of whichever set is currently being played -
        // separate from home_score/away_score, which hold the sets-won tally.
        // Without this, the public match page has no way to show the live
        // in-progress set score (it only ever sees completed sets).
        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedInteger('current_set_home_score')->default(0)->after('current_set_started_at');
            $table->unsignedInteger('current_set_away_score')->default(0)->after('current_set_home_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['current_set_home_score', 'current_set_away_score']);
        });
    }
};
