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
        // Add only missing indexes that will improve performance
        
        // COMPETITIONS - filtering by status and type
        Schema::table('competitions', function (Blueprint $table) {
            $table->index('status', 'idx_competitions_status');
            $table->index('type', 'idx_competitions_type');
            $table->index(['organization_id', 'status'], 'idx_competitions_org_status');
        });

        // ORGANIZATIONS - user lookup
        Schema::table('organizations', function (Blueprint $table) {
            $table->index('user_id', 'idx_organizations_user_id');
        });

        // ORGANIZATION_USER - permission checks
        Schema::table('organization_user', function (Blueprint $table) {
            $table->index('role', 'idx_organization_user_role');
            $table->index(['organization_id', 'role'], 'idx_organization_user_org_role');
        });

        // TOURNAMENT_GROUPS
        Schema::table('tournament_groups', function (Blueprint $table) {
            $table->index(['competition_id', 'round'], 'idx_tournament_groups_comp_round');
        });

        // TABLES
        Schema::table('tables', function (Blueprint $table) {
            $table->index('organization_id', 'idx_tables_organization_id');
        });

        // USER_PLANS - expiration checks
        Schema::table('user_plans', function (Blueprint $table) {
            $table->index('user_id', 'idx_user_plans_user_id');
            $table->index('ends_at', 'idx_user_plans_ends_at');
        });

        // SPORTS
        Schema::table('sports', function (Blueprint $table) {
            $table->index('is_active', 'idx_sports_is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sports', function (Blueprint $table) {
            $table->dropIndex('idx_sports_is_active');
        });

        Schema::table('user_plans', function (Blueprint $table) {
            $table->dropIndex('idx_user_plans_user_id');
            $table->dropIndex('idx_user_plans_ends_at');
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->dropIndex('idx_tables_organization_id');
        });

        Schema::table('tournament_groups', function (Blueprint $table) {
            $table->dropIndex('idx_tournament_groups_comp_round');
        });

        Schema::table('organization_user', function (Blueprint $table) {
            $table->dropIndex('idx_organization_user_role');
            $table->dropIndex('idx_organization_user_org_role');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropIndex('idx_organizations_user_id');
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->dropIndex('idx_competitions_status');
            $table->dropIndex('idx_competitions_type');
            $table->dropIndex('idx_competitions_org_status');
        });
    }
};
