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
        Schema::table('competitions', function (Blueprint $table) {
            // Tournament-specific fields (type field already exists from previous migration)
            if (!Schema::hasColumn('competitions', 'max_participants')) {
                $table->integer('max_participants')->nullable()->after('is_team_based');
            }
            if (!Schema::hasColumn('competitions', 'group_count')) {
                $table->integer('group_count')->nullable()->after('max_participants');
            }
            if (!Schema::hasColumn('competitions', 'players_per_group')) {
                $table->integer('players_per_group')->nullable()->after('group_count');
            }
            if (!Schema::hasColumn('competitions', 'players_advancing_per_group')) {
                $table->integer('players_advancing_per_group')->nullable()->after('players_per_group');
            }
            if (!Schema::hasColumn('competitions', 'advancement_method')) {
                if (DB::getDriverName() === 'pgsql') {
                    $table->string('advancement_method')->nullable()->after('players_advancing_per_group');
                } else {
                    $table->enum('advancement_method', ['automatic', 'manual'])->nullable()->after('players_advancing_per_group');
                }
            }
            if (!Schema::hasColumn('competitions', 'current_phase')) {
                if (DB::getDriverName() === 'pgsql') {
                    $table->string('current_phase')->nullable()->after('advancement_method');
                } else {
                    $table->enum('current_phase', ['groups', 'knockout', 'completed'])->nullable()->after('advancement_method');
                }
            }
            if (!Schema::hasColumn('competitions', 'knockout_bracket')) {
                $table->json('knockout_bracket')->nullable()->after('current_phase');
            }
            if (!Schema::hasColumn('competitions', 'groups_completed_at')) {
                $table->timestamp('groups_completed_at')->nullable()->after('knockout_bracket');
            }
            if (!Schema::hasColumn('competitions', 'knockout_completed_at')) {
                $table->timestamp('knockout_completed_at')->nullable()->after('groups_completed_at');
            }
        });

        // Add CHECK constraints for Postgres where appropriate
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE competitions DROP CONSTRAINT IF EXISTS competitions_advancement_method_check');
            DB::statement("ALTER TABLE competitions ADD CONSTRAINT competitions_advancement_method_check CHECK (advancement_method IN ('automatic','manual'))");
            DB::statement('ALTER TABLE competitions DROP CONSTRAINT IF EXISTS competitions_current_phase_check');
            DB::statement("ALTER TABLE competitions ADD CONSTRAINT competitions_current_phase_check CHECK (current_phase IN ('groups','knockout','completed'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            // Only drop columns that were added by this migration
            $columns = [];
            if (Schema::hasColumn('competitions', 'max_participants')) $columns[] = 'max_participants';
            if (Schema::hasColumn('competitions', 'group_count')) $columns[] = 'group_count';
            if (Schema::hasColumn('competitions', 'players_per_group')) $columns[] = 'players_per_group';
            if (Schema::hasColumn('competitions', 'players_advancing_per_group')) $columns[] = 'players_advancing_per_group';
            if (Schema::hasColumn('competitions', 'advancement_method')) $columns[] = 'advancement_method';
            if (Schema::hasColumn('competitions', 'current_phase')) $columns[] = 'current_phase';
            if (Schema::hasColumn('competitions', 'knockout_bracket')) $columns[] = 'knockout_bracket';
            if (Schema::hasColumn('competitions', 'groups_completed_at')) $columns[] = 'groups_completed_at';
            if (Schema::hasColumn('competitions', 'knockout_completed_at')) $columns[] = 'knockout_completed_at';
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
