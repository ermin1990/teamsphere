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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('league_id');
            $table->foreignId('home_team_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->foreignId('away_team_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->foreignId('home_player_id')->nullable()->constrained('players')->onDelete('cascade');
            $table->foreignId('away_player_id')->nullable()->constrained('players')->onDelete('cascade');
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('played_at')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->integer('round')->default(1);
            $table->json('sets')->nullable(); // For detailed set scores
        });

        // Add foreign key only if referenced table exists to support multiple schemas
        if (Schema::hasTable('leagues')) {
            Schema::table('matches', function (Blueprint $table) {
                $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
            });
        } elseif (Schema::hasTable('competitions')) {
            Schema::table('matches', function (Blueprint $table) {
                $table->foreign('league_id')->references('id')->on('competitions')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
