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
                $table->unsignedBigInteger('competition_id')->nullable()->after('league_id');
            }

            // Make league_id nullable since matches can belong to either league or competition
            $table->unsignedBigInteger('league_id')->nullable()->change();
        });

        // Add foreign key to competition_id if the competitions table exists
        if (Schema::hasTable('competitions')) {
            Schema::table('matches', function (Blueprint $table) {
                $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
            });
        }
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
