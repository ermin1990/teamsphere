<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table to change enum and add new column
        if (DB::getDriverName() === 'sqlite') {
            // First backup existing data
            DB::statement('CREATE TEMPORARY TABLE matches_backup AS SELECT * FROM matches');

            // Drop and recreate table
            Schema::drop('matches');

            Schema::create('matches', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->foreignId('league_id')->constrained()->onDelete('cascade');
                $table->foreignId('home_team_id')->nullable()->constrained('teams')->onDelete('cascade');
                $table->foreignId('away_team_id')->nullable()->constrained('teams')->onDelete('cascade');
                $table->foreignId('home_player_id')->nullable()->constrained('players')->onDelete('cascade');
                $table->foreignId('away_player_id')->nullable()->constrained('players')->onDelete('cascade');
                $table->integer('home_score')->nullable();
                $table->integer('away_score')->nullable();
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('played_at')->nullable();
                $table->enum('status', ['scheduled', 'in_progress', 'completed', 'forfeited', 'cancelled'])->default('scheduled');
                $table->enum('forfeited_by', ['home', 'away'])->nullable();
                $table->integer('round')->default(1);
                $table->json('sets')->nullable(); // For detailed set scores
            });

            // Restore data
            DB::statement('INSERT INTO matches (id, created_at, updated_at, league_id, home_team_id, away_team_id, home_player_id, away_player_id, home_score, away_score, scheduled_at, played_at, status, round, sets) SELECT id, created_at, updated_at, league_id, home_team_id, away_team_id, home_player_id, away_player_id, home_score, away_score, scheduled_at, played_at, status, round, sets FROM matches_backup');
            DB::statement('DROP TABLE matches_backup');
        } else {
            // For other databases, use ALTER TABLE
            Schema::table('matches', function (Blueprint $table) {
                if (!Schema::hasColumn('matches', 'forfeited_by')) {
                    $table->enum('forfeited_by', ['home', 'away'])->nullable()->after('status');
                }
            });
            DB::statement("ALTER TABLE matches MODIFY COLUMN status ENUM('scheduled', 'in_progress', 'completed', 'forfeited', 'cancelled') DEFAULT 'scheduled'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status enum (remove 'forfeited') and drop forfeited_by column
        if (DB::getDriverName() === 'sqlite') {
            // For SQLite, need to recreate table again
            DB::statement('CREATE TEMPORARY TABLE matches_backup AS SELECT id, created_at, updated_at, league_id, home_team_id, away_team_id, home_player_id, away_player_id, home_score, away_score, scheduled_at, played_at, CASE WHEN status = "forfeited" THEN "cancelled" ELSE status END as status, round, sets FROM matches');

            Schema::drop('matches');

            Schema::create('matches', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->foreignId('league_id')->constrained()->onDelete('cascade');
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
                $table->json('sets')->nullable();
            });

            DB::statement('INSERT INTO matches SELECT * FROM matches_backup');
            DB::statement('DROP TABLE matches_backup');
        } else {
            Schema::table('matches', function (Blueprint $table) {
                $table->dropColumn('forfeited_by');
            });
            DB::statement("ALTER TABLE matches MODIFY COLUMN status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled'");
        }
    }
};
