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
        Schema::create('futsal_match_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('futsal_match_id')->constrained()->onDelete('cascade');
            $table->foreignId('futsal_team_id')->constrained()->onDelete('cascade'); // Which team
            $table->foreignId('player_id')->nullable()->constrained('futsal_team_players')->onDelete('cascade'); // Player involved
            $table->foreignId('assist_player_id')->nullable()->constrained('futsal_team_players')->onDelete('set null'); // For goals
            $table->enum('event_type', ['goal', 'yellow_card', 'red_card', 'substitution_in', 'substitution_out']); 
            $table->integer('minute')->nullable(); // Minute of the event
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['futsal_match_id', 'event_type']);
            $table->index('player_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('futsal_match_events');
    }
};
