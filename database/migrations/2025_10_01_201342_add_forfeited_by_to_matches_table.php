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
                $table->string('forfeited_by')->nullable()->after('sets');
            } else {
                $table->enum('forfeited_by', ['home', 'away'])->nullable()->after('sets');
            }
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE matches DROP CONSTRAINT IF EXISTS matches_forfeited_by_check');
            DB::statement("ALTER TABLE matches ADD CONSTRAINT matches_forfeited_by_check CHECK (forfeited_by IN ('home','away'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn('forfeited_by');
        });
    }
};
