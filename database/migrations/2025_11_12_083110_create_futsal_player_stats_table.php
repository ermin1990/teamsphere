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
        Schema::create('futsal_player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->foreignId('futsal_team_player_id')->constrained()->onDelete('cascade');
            $table->foreignId('futsal_team_id')->constrained()->onDelete('cascade');
            $table->integer('matches_played')->default(0);
            $table->integer('goals')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('yellow_cards')->default(0);
            $table->integer('red_cards')->default(0);
            $table->integer('minutes_played')->default(0);
            $table->timestamps();
            
            $table->unique(['competition_id', 'futsal_team_player_id']);
            $table->index(['competition_id', 'goals']);
            $table->index('futsal_team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('futsal_player_stats');
    }
};
