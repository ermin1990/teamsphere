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
        Schema::create('team_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->foreignId('home_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('away_team_id')->constrained('teams')->onDelete('cascade');
            $table->integer('home_score')->default(0);
            $table->integer('away_score')->default(0);
            if (DB::getDriverName() === 'pgsql') {
                $table->string('status')->default('scheduled');
            } else {
                $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            }
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('played_at')->nullable();
            $table->integer('round')->default(1);
            $table->json('lineup')->nullable(); // Stores A, B, C, X, Y, Z assignments
            $table->timestamps();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE team_matches DROP CONSTRAINT IF EXISTS team_matches_status_check');
            DB::statement("ALTER TABLE team_matches ADD CONSTRAINT team_matches_status_check CHECK (status IN ('scheduled','in_progress','completed','cancelled'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_matches');
    }
};
