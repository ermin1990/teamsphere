<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('friendly_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('home_player_id');
            $table->unsignedBigInteger('away_player_id');
            $table->string('home_player_name');
            $table->string('away_player_name');
            $table->json('sets'); // Array of set scores
            $table->json('set_durations'); // Array of durations in seconds
            $table->string('winner_name');
            $table->timestamp('completed_at');
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('home_player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('away_player_id')->references('id')->on('players')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friendly_matches');
    }
};
