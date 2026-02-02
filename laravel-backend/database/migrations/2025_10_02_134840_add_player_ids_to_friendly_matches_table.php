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
        // This migration is redundant as the columns already exist in the create_friendly_matches_table migration
        // Schema::table('friendly_matches', function (Blueprint $table) {
        //     $table->unsignedBigInteger('home_player_id')->nullable()->after('organization_id');
        //     $table->unsignedBigInteger('away_player_id')->nullable()->after('home_player_id');

        //     $table->foreign('home_player_id')->references('id')->on('players')->onDelete('cascade');
        //     $table->foreign('away_player_id')->references('id')->on('players')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('friendly_matches', function (Blueprint $table) {
            //
        });
    }
};
