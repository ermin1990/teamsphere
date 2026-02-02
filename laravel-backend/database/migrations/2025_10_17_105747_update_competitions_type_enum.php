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
        // For SQLite, we need to recreate the table to change enum values
        // First, backup existing data
        $competitions = DB::table('competitions')->get();

        // Drop and recreate table with new enum values
        Schema::dropIfExists('competitions');

        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('sport_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['league', 'tournament', 'knockout'])->default('league');
            $table->date('start_date')->default(now()->toDateString());
            $table->date('end_date')->nullable();
            $table->integer('max_teams')->default(8);
            $table->boolean('is_team_based')->default(false);
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);

            // Tournament fields
            $table->integer('max_participants')->default(16);
            $table->integer('group_count')->default(4);
            $table->integer('players_per_group')->default(4);
            $table->integer('players_advancing_per_group')->default(2);
            $table->enum('advancement_method', ['automatic', 'manual'])->default('automatic');
            $table->enum('current_phase', ['groups', 'knockout', 'completed'])->default('groups');
            $table->json('knockout_bracket')->nullable();
            $table->timestamp('groups_completed_at')->nullable();
            $table->timestamp('knockout_completed_at')->nullable();

            // Match settings
            $table->integer('sets_to_win')->default(2);
            $table->integer('points_per_set')->default(21);
            $table->integer('deuce_at')->default(20);
            $table->boolean('must_win_by_two')->default(true);
            $table->integer('points_for_win')->default(3);
            $table->integer('points_for_draw')->default(1);
            $table->integer('points_for_loss')->default(0);
            $table->boolean('has_tiebreak')->default(true);
            $table->integer('tiebreak_points')->default(7);

            $table->timestamps();
        });

        // Restore data
        foreach ($competitions as $competition) {
            DB::table('competitions')->insert((array) $competition);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For SQLite, we need to recreate the table to change enum values back
        $competitions = DB::table('competitions')->get();

        Schema::dropIfExists('competitions');

        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('sport_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['league', 'tournament'])->default('league'); // Back to original
            $table->date('start_date')->default(now()->toDateString());
            $table->date('end_date')->nullable();
            $table->integer('max_teams')->default(8);
            $table->boolean('is_team_based')->default(false);
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);

            // Tournament fields
            $table->integer('max_participants')->default(16);
            $table->integer('group_count')->default(4);
            $table->integer('players_per_group')->default(4);
            $table->integer('players_advancing_per_group')->default(2);
            $table->enum('advancement_method', ['automatic', 'manual'])->default('automatic');
            $table->enum('current_phase', ['groups', 'knockout'])->default('groups');
            $table->json('knockout_bracket')->nullable();
            $table->timestamp('groups_completed_at')->nullable();
            $table->timestamp('knockout_completed_at')->nullable();

            // Match settings
            $table->integer('sets_to_win')->default(2);
            $table->integer('points_per_set')->default(21);
            $table->integer('deuce_at')->default(20);
            $table->boolean('must_win_by_two')->default(true);
            $table->integer('points_for_win')->default(3);
            $table->integer('points_for_draw')->default(1);
            $table->integer('points_for_loss')->default(0);
            $table->boolean('has_tiebreak')->default(true);
            $table->integer('tiebreak_points')->default(7);

            $table->timestamps();
        });

        // Restore data
        foreach ($competitions as $competition) {
            DB::table('competitions')->insert((array) $competition);
        }
    }
};
