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
        Schema::create('tournament_group_futsal_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('futsal_team_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['tournament_group_id', 'futsal_team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_group_futsal_team');
    }
};
