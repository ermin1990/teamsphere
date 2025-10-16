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
        Schema::table('matches', function (Blueprint $table) {
            // Add competition_id for tournaments (nullable, can be either league or competition)
            if (!Schema::hasColumn('matches', 'competition_id')) {
                $table->foreignId('competition_id')->nullable()->after('league_id')->constrained('competitions')->onDelete('cascade');
            }
            
            // Make league_id nullable since matches can belong to either league or competition
            $table->foreignId('league_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            if (Schema::hasColumn('matches', 'competition_id')) {
                $table->dropForeign(['competition_id']);
                $table->dropColumn('competition_id');
            }
        });
    }
};
