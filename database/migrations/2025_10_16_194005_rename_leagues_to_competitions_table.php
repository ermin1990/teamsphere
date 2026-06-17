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
        // Add type field to leagues table
        Schema::table('leagues', function (Blueprint $table) {
            if (DB::getDriverName() === 'pgsql') {
                $table->string('type')->default('league')->after('sport_id');
            } else {
                $table->enum('type', ['league', 'tournament'])->default('league')->after('sport_id');
            }
        });

        // Rename table from leagues to competitions
        Schema::rename('leagues', 'competitions');

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE competitions DROP CONSTRAINT IF EXISTS competitions_type_check");
            DB::statement("ALTER TABLE competitions ADD CONSTRAINT competitions_type_check CHECK (type IN ('league','tournament'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename table back from competitions to leagues
        Schema::rename('competitions', 'leagues');

        // Remove type field
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
