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
        Schema::table('leagues', function (Blueprint $table) {
            // Indeksi za brže pretrage
            $table->index(['status', 'is_active']);
            $table->index(['organization_id', 'status']);
            $table->index(['sport_id', 'status']);
            $table->index('start_date');
            $table->index('end_date');
            $table->index(['is_active', 'start_date']);
        });

        // Indeksi za organizations tabelu
        Schema::table('organizations', function (Blueprint $table) {
            $table->index(['user_id', 'is_active']);
            $table->index(['plan_id', 'is_active']);
            $table->index('is_active');
        });

        // Indeksi za matches tabelu
        Schema::table('matches', function (Blueprint $table) {
            $table->index(['league_id', 'status']);
            $table->index(['status', 'scheduled_at']);
            $table->index('scheduled_at');
            $table->index('played_at');
            $table->index(['home_team_id', 'status']);
            $table->index(['away_team_id', 'status']);
        });

        // Indeksi za players tabelu
        Schema::table('players', function (Blueprint $table) {
            $table->index(['organization_id', 'is_active']);
            $table->index('is_active');
        });

        // Indeksi za teams tabelu
        Schema::table('teams', function (Blueprint $table) {
            $table->index(['league_id', 'is_active']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropIndex(['status', 'is_active']);
            $table->dropIndex(['organization_id', 'status']);
            $table->dropIndex(['sport_id', 'status']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
            $table->dropIndex(['is_active', 'start_date']);
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_active']);
            $table->dropIndex(['plan_id', 'is_active']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex(['league_id', 'status']);
            $table->dropIndex(['status', 'scheduled_at']);
            $table->dropIndex(['scheduled_at']);
            $table->dropIndex(['played_at']);
            $table->dropIndex(['home_team_id', 'status']);
            $table->dropIndex(['away_team_id', 'status']);
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex(['organization_id', 'is_active']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex(['league_id', 'is_active']);
            $table->dropIndex(['is_active']);
        });
    }
};
