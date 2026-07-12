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
        $driver = DB::getDriverName();

        // 1. Specijalni uslov za SQLite (Lokalni razvoj)
        if ($driver === 'sqlite') {
            // First backup existing data
            DB::statement('CREATE TEMPORARY TABLE matches_backup AS SELECT * FROM matches');
            
            // Drop and recreate table
            Schema::drop('matches');
            
            Schema::create('matches', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->unsignedBigInteger('league_id');
                $table->unsignedBigInteger('home_team_id')->nullable();
                $table->unsignedBigInteger('away_team_id')->nullable();
                $table->unsignedBigInteger('home_player_id')->nullable();
                $table->unsignedBigInteger('away_player_id')->nullable();
                $table->integer('home_score')->nullable();
                $table->integer('away_score')->nullable();
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('played_at')->nullable();
                $table->string('status')->default('scheduled');
                $table->string('forfeited_by')->nullable();
                $table->integer('round')->default(1);
                $table->json('sets')->nullable(); // For detailed set scores
            });
            // Add FK if the referenced table exists
            if (Schema::hasTable('leagues')) {
                Schema::table('matches', function (Blueprint $table) {
                    $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
                    $table->foreign('home_team_id')->references('id')->on('teams')->onDelete('cascade');
                    $table->foreign('away_team_id')->references('id')->on('teams')->onDelete('cascade');
                    $table->foreign('home_player_id')->references('id')->on('players')->onDelete('cascade');
                    $table->foreign('away_player_id')->references('id')->on('players')->onDelete('cascade');
                });
            } elseif (Schema::hasTable('competitions')) {
                Schema::table('matches', function (Blueprint $table) {
                    $table->foreign('league_id')->references('id')->on('competitions')->onDelete('cascade');
                });
            }
            
            // Restore data
            DB::statement('INSERT INTO matches (id, created_at, updated_at, league_id, home_team_id, away_team_id, home_player_id, away_player_id, home_score, away_score, scheduled_at, played_at, status, round, sets) SELECT id, created_at, updated_at, league_id, home_team_id, away_team_id, home_player_id, away_player_id, home_score, away_score, scheduled_at, played_at, status, round, sets FROM matches_backup');
            DB::statement('DROP TABLE matches_backup');

        // 2. Specijalni uslov za PostgreSQL (Tvoj VPS server)
        } elseif ($driver === 'pgsql') {
            // Kolona 'forfeited_by' je već kreirana u prošloj migraciji, pa je ovdje preskačemo.
            // Bezbjedno ažuriramo Postgres check constraint za status kolonu da podržava 'forfeited'
            DB::statement('ALTER TABLE matches DROP CONSTRAINT IF EXISTS matches_status_check');
            DB::statement("ALTER TABLE matches ADD CONSTRAINT matches_status_check CHECK (status IN ('scheduled', 'in_progress', 'completed', 'forfeited', 'cancelled'))");

        // 3. Uslov za MySQL
        } elseif ($driver === 'mysql') {
            // Kolona 'forfeited_by' je već kreirana u prošloj migraciji (2025_10_01_201342),
            // pa je ovdje preskačemo ako već postoji (isto kao pgsql grana iznad).
            if (! Schema::hasColumn('matches', 'forfeited_by')) {
                Schema::table('matches', function (Blueprint $table) {
                    $table->enum('forfeited_by', ['home', 'away'])->nullable()->after('status');
                });
            }
            DB::statement("ALTER TABLE matches MODIFY COLUMN status ENUM('scheduled', 'in_progress', 'completed', 'forfeited', 'cancelled') DEFAULT 'scheduled'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // For SQLite, need to recreate table again
            DB::statement('CREATE TEMPORARY TABLE matches_backup AS SELECT id, created_at, updated_at, league_id, home_team_id, away_team_id, home_player_id, away_player_id, home_score, away_score, scheduled_at, played_at, CASE WHEN status = "forfeited" THEN "cancelled" ELSE status END as status, round, sets FROM matches');
            
            Schema::drop('matches');
            
            Schema::create('matches', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->unsignedBigInteger('league_id');
                $table->unsignedBigInteger('home_team_id')->nullable();
                $table->unsignedBigInteger('away_team_id')->nullable();
                $table->unsignedBigInteger('home_player_id')->nullable();
                $table->unsignedBigInteger('away_player_id')->nullable();
                $table->integer('home_score')->nullable();
                $table->integer('away_score')->nullable();
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('played_at')->nullable();
                $table->string('status')->default('scheduled');
                $table->integer('round')->default(1);
                $table->json('sets')->nullable();
            });
            // Restore foreign keys where possible
            if (Schema::hasTable('leagues')) {
                Schema::table('matches', function (Blueprint $table) {
                    $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
                    $table->foreign('home_team_id')->references('id')->on('teams')->onDelete('cascade');
                    $table->foreign('away_team_id')->references('id')->on('teams')->onDelete('cascade');
                    $table->foreign('home_player_id')->references('id')->on('players')->onDelete('cascade');
                    $table->foreign('away_player_id')->references('id')->on('players')->onDelete('cascade');
                });
            } elseif (Schema::hasTable('competitions')) {
                Schema::table('matches', function (Blueprint $table) {
                    $table->foreign('league_id')->references('id')->on('competitions')->onDelete('cascade');
                });
            }
            
            DB::statement('INSERT INTO matches SELECT * FROM matches_backup');
            DB::statement('DROP TABLE matches_backup');

        } elseif ($driver === 'pgsql') {
            // Vraćamo stari check constraint na Postgresu bez diranja 'forfeited_by' kolone (nju briše prošla migracija)
            DB::statement('ALTER TABLE matches DROP CONSTRAINT IF EXISTS matches_status_check');
            DB::statement("ALTER TABLE matches ADD CONSTRAINT matches_status_check CHECK (status IN ('scheduled', 'in_progress', 'completed', 'cancelled'))");

        } elseif ($driver === 'mysql') {
            Schema::table('matches', function (Blueprint $table) {
                $table->dropColumn('forfeited_by');
            });
            DB::statement("ALTER TABLE matches MODIFY COLUMN status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled'");
        }
    }
};