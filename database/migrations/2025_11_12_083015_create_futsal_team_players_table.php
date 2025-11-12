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
        Schema::create('futsal_team_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('futsal_team_id')->constrained()->onDelete('cascade');
            $table->string('player_name');
            $table->integer('jersey_number')->nullable();
            $table->enum('position', ['goalkeeper', 'defender', 'midfielder', 'forward'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->boolean('is_captain')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['futsal_team_id', 'jersey_number']);
            $table->index(['futsal_team_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('futsal_team_players');
    }
};
