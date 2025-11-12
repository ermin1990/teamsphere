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
        Schema::create('futsal_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->foreignId('home_team_id')->constrained('futsal_teams')->onDelete('cascade');
            $table->foreignId('away_team_id')->constrained('futsal_teams')->onDelete('cascade');
            $table->integer('home_score')->default(0);
            $table->integer('away_score')->default(0);
            $table->integer('home_score_ht')->nullable(); // Half-time score
            $table->integer('away_score_ht')->nullable();
            $table->enum('status', ['scheduled', 'live', 'completed', 'postponed', 'cancelled'])->default('scheduled');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('kickoff_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('round')->nullable(); // "Round 1", "Quarterfinal", etc.
            $table->enum('phase', ['group', 'knockout'])->default('group');
            $table->foreignId('tournament_group_id')->nullable()->constrained()->onDelete('set null');
            $table->string('venue')->nullable();
            $table->foreignId('referee_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['competition_id', 'status']);
            $table->index(['home_team_id', 'away_team_id']);
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('futsal_matches');
    }
};
