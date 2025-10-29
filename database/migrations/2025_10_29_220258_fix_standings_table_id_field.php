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
        // Check if id column exists and has proper auto_increment
        $hasId = Schema::hasColumn('standings', 'id');
        
        if (!$hasId) {
            // If id column doesn't exist, add it
            Schema::table('standings', function (Blueprint $table) {
                $table->id()->first();
            });
        } else {
            // Check if id has auto_increment
            $result = DB::select("SHOW COLUMNS FROM standings WHERE Field = 'id'");
            
            if (!empty($result)) {
                $extra = $result[0]->Extra ?? '';
                
                // If auto_increment is missing, fix it
                if (strpos($extra, 'auto_increment') === false) {
                    DB::statement('ALTER TABLE standings MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this fix
    }
};
