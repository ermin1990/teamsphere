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
        // 1. COMPETITIONS - filtering by status and type
        if (Schema::hasTable('competitions')) {
            Schema::table('competitions', function (Blueprint $table) {
                if (Schema::hasColumn('competitions', 'status')) {
                    try { $table->index('status', 'idx_competitions_status'); } catch (\Exception $e) {}
                }
                if (Schema::hasColumn('competitions', 'type')) {
                    try { $table->index('type', 'idx_competitions_type'); } catch (\Exception $e) {}
                }
                if (Schema::hasColumn('competitions', 'organization_id') && Schema::hasColumn('competitions', 'status')) {
                    try { $table->index(['organization_id', 'status'], 'idx_competitions_org_status'); } catch (\Exception $e) {}
                }
            });
        }

        // 2. ORGANIZATIONS - user lookup
        if (Schema::hasTable('organizations')) {
            Schema::table('organizations', function (Blueprint $table) {
                if (Schema::hasColumn('organizations', 'user_id')) {
                    try { $table->index('user_id', 'idx_organizations_user_id'); } catch (\Exception $e) {}
                }
            });
        }

        // 3. ORGANIZATION_USER - permission checks
        if (Schema::hasTable('organization_user')) {
            Schema::table('organization_user', function (Blueprint $table) {
                if (Schema::hasColumn('organization_user', 'role')) {
                    try { $table->index('role', 'idx_organization_user_role'); } catch (\Exception $e) {}
                }
                if (Schema::hasColumn('organization_user', 'organization_id') && Schema::hasColumn('organization_user', 'role')) {
                    try { $table->index(['organization_id', 'role'], 'idx_organization_user_org_role'); } catch (\Exception $e) {}
                }
            });
        }

        // 4. TOURNAMENT_GROUPS
        if (Schema::hasTable('tournament_groups')) {
            $tgCols = Schema::getColumnListing('tournament_groups');
            if (in_array('competition_id', $tgCols) && in_array('round', $tgCols)) {
                try { 
                    DB::statement('CREATE INDEX IF NOT EXISTS idx_tournament_groups_comp_round ON tournament_groups (competition_id, round)'); 
                } catch (\Exception $e) {}
            }
        }

        // 5. TABLES
        if (Schema::hasTable('tables')) {
            Schema::table('tables', function (Blueprint $table) {
                if (Schema::hasColumn('tables', 'organization_id')) {
                    try { $table->index('organization_id', 'idx_tables_organization_id'); } catch (\Exception $e) {}
                }
            });
        }

        // 6. USER_PLANS - expiration checks
        if (Schema::hasTable('user_plans')) {
            Schema::table('user_plans', function (Blueprint $table) {
                if (Schema::hasColumn('user_plans', 'user_id')) {
                    try { $table->index('user_id', 'idx_user_plans_user_id'); } catch (\Exception $e) {}
                }
                // Ako kolona ends_at ne postoji, Laravel će je sada bezbjedno preskočiti
                if (Schema::hasColumn('user_plans', 'ends_at')) {
                    try { $table->index('ends_at', 'idx_user_plans_ends_at'); } catch (\Exception $e) {}
                }
            });
        }

        // 7. SPORTS
        if (Schema::hasTable('sports')) {
            Schema::table('sports', function (Blueprint $table) {
                if (Schema::hasColumn('sports', 'is_active')) {
                    try { $table->index('is_active', 'idx_sports_is_active'); } catch (\Exception $e) {}
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sports')) {
            try {
                Schema::table('sports', function (Blueprint $table) {
                    $table->dropIndex('idx_sports_is_active');
                });
            } catch (\Exception $e) {}
        }

        if (Schema::hasTable('user_plans')) {
            try {
                Schema::table('user_plans', function (Blueprint $table) {
                    $table->dropIndex('idx_user_plans_user_id');
                    $table->dropIndex('idx_user_plans_ends_at');
                });
            } catch (\Exception $e) {}
        }

        if (Schema::hasTable('tables')) {
            try {
                Schema::table('tables', function (Blueprint $table) {
                    $table->dropIndex('idx_tables_organization_id');
                });
            } catch (\Exception $e) {}
        }

        if (Schema::hasTable('tournament_groups')) {
            try { DB::statement('DROP INDEX IF EXISTS idx_tournament_groups_comp_round'); } catch (\Exception $e) {}
        }

        if (Schema::hasTable('organization_user')) {
            try {
                Schema::table('organization_user', function (Blueprint $table) {
                    $table->dropIndex('idx_organization_user_role');
                    $table->dropIndex('idx_organization_user_org_role');
                });
            } catch (\Exception $e) {}
        }

        if (Schema::hasTable('organizations')) {
            try {
                Schema::table('organizations', function (Blueprint $table) {
                    $table->dropIndex('idx_organizations_user_id');
                });
            } catch (\Exception $e) {}
        }

        if (Schema::hasTable('competitions')) {
            try {
                Schema::table('competitions', function (Blueprint $table) {
                    $table->dropIndex('idx_competitions_status');
                    $table->dropIndex('idx_competitions_type');
                    $table->dropIndex('idx_competitions_org_status');
                });
            } catch (\Exception $e) {}
        }
    }
};