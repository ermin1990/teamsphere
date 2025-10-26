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
            $table->string('home_player_group')->nullable();
            $table->integer('home_player_position')->nullable();
            $table->string('away_player_group')->nullable();
            $table->integer('away_player_position')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['home_player_group', 'home_player_position', 'away_player_group', 'away_player_position']);
        });
    }
};
