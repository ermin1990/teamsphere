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
        Schema::table('team_matches', function (Blueprint $table) {
            $table->foreignId('home_captain_id')->nullable()->constrained('players')->onDelete('set null');
            $table->foreignId('away_captain_id')->nullable()->constrained('players')->onDelete('set null');
            $table->string('referee_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_matches', function (Blueprint $table) {
            $table->dropForeign(['home_captain_id']);
            $table->dropForeign(['away_captain_id']);
            $table->dropColumn(['home_captain_id', 'away_captain_id', 'referee_name']);
        });
    }
};
