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
        Schema::table('standings', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('standings', 'sets_won')) {
                $table->integer('sets_won')->default(0)->after('points');
            }
            if (!Schema::hasColumn('standings', 'sets_lost')) {
                $table->integer('sets_lost')->default(0)->after('sets_won');
            }
            if (!Schema::hasColumn('standings', 'points_won')) {
                $table->integer('points_won')->default(0)->after('sets_lost');
            }
            if (!Schema::hasColumn('standings', 'points_lost')) {
                $table->integer('points_lost')->default(0)->after('points_won');
            }
            if (!Schema::hasColumn('standings', 'tournament_group_id')) {
                $table->foreignId('tournament_group_id')->nullable()->after('competition_id')->constrained('tournament_groups')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standings', function (Blueprint $table) {
            if (Schema::hasColumn('standings', 'tournament_group_id')) {
                $table->dropForeign(['tournament_group_id']);
                $table->dropColumn('tournament_group_id');
            }
            if (Schema::hasColumn('standings', 'points_lost')) {
                $table->dropColumn('points_lost');
            }
            if (Schema::hasColumn('standings', 'points_won')) {
                $table->dropColumn('points_won');
            }
            if (Schema::hasColumn('standings', 'sets_lost')) {
                $table->dropColumn('sets_lost');
            }
            if (Schema::hasColumn('standings', 'sets_won')) {
                $table->dropColumn('sets_won');
            }
        });
    }
};
