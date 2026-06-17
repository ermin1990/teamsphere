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
        Schema::create('league_player', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('league_id');
            $table->unsignedBigInteger('player_id');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['league_id', 'player_id']); // Prevent duplicate entries
        });

        if (Schema::hasTable('leagues')) {
            Schema::table('league_player', function (Blueprint $table) {
                $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
                $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            });
        } elseif (Schema::hasTable('competitions')) {
            Schema::table('league_player', function (Blueprint $table) {
                $table->foreign('league_id')->references('id')->on('competitions')->onDelete('cascade');
                $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league_player');
    }
};
