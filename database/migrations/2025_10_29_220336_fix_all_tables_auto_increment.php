<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration fixes AUTO_INCREMENT issues on all tables in the database.
     * Some hosting providers or MySQL imports can lose AUTO_INCREMENT settings.
     */
    public function up(): void
    {
        // List of all tables that should have auto-increment id
        $tables = [
            'users',
            'organizations',
            'competitions',
            'leagues',
            'matches',
            'standings',
            'players',
            'teams',
            'tournament_groups',
            'tables',
            'organization_user',
            'competition_player',
            'friendly_matches',
            'bug_reports',
            'sports',
            'plans',
            'user_plans',
            'sessions',
            'password_reset_tokens',
            'failed_jobs',
            'jobs',
            'job_batches',
            'cache',
            'cache_locks',
        ];

        foreach ($tables as $table) {
            // Check if table exists
            if (!Schema::hasTable($table)) {
                continue;
            }

            // Check if id column exists
            if (!Schema::hasColumn($table, 'id')) {
                continue;
            }

            try {
                // Get column information
                $result = DB::select("SHOW COLUMNS FROM `{$table}` WHERE Field = 'id'");
                
                if (!empty($result)) {
                    $column = $result[0];
                    $extra = $column->Extra ?? '';
                    
                    // If auto_increment is missing, add it
                    if (stripos($extra, 'auto_increment') === false) {
                        echo "Fixing AUTO_INCREMENT on table: {$table}\n";
                        
                        // Determine the column type (usually BIGINT UNSIGNED for Laravel)
                        $type = strtoupper($column->Type ?? 'BIGINT');
                        
                        // For Laravel convention, id should be BIGINT UNSIGNED
                        if (stripos($type, 'BIGINT') !== false) {
                            DB::statement("ALTER TABLE `{$table}` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
                        } elseif (stripos($type, 'INT') !== false) {
                            DB::statement("ALTER TABLE `{$table}` MODIFY `id` INT UNSIGNED NOT NULL AUTO_INCREMENT");
                        }
                    }
                }
            } catch (\Exception $e) {
                // Log error but continue with other tables
                echo "Error fixing table {$table}: " . $e->getMessage() . "\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration should not be reversed as it fixes database integrity
        echo "This migration cannot be reversed as it fixes critical database issues.\n";
    }
};
