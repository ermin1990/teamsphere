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
        Schema::table('matches', function (Blueprint $table) {
            if (DB::getDriverName() === 'pgsql') {
                $table->string('first_server')->nullable()->after('forfeited_by');
                $table->string('current_server')->nullable()->after('first_server');
            } else {
                $table->enum('first_server', ['home', 'away'])->nullable()->after('forfeited_by');
                $table->enum('current_server', ['home', 'away'])->nullable()->after('first_server');
            }
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE matches DROP CONSTRAINT IF EXISTS matches_first_server_check');
            DB::statement("ALTER TABLE matches ADD CONSTRAINT matches_first_server_check CHECK (first_server IN ('home','away'))");
            DB::statement('ALTER TABLE matches DROP CONSTRAINT IF EXISTS matches_current_server_check');
            DB::statement("ALTER TABLE matches ADD CONSTRAINT matches_current_server_check CHECK (current_server IN ('home','away'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['first_server', 'current_server']);
        });
    }
};