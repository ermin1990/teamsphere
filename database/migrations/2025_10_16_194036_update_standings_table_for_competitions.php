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
            // Drop the old foreign key constraint
            $table->dropForeign(['league_id']);

            // Rename the column from league_id to competition_id
            $table->renameColumn('league_id', 'competition_id');

            // Add the new foreign key constraint
            $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standings', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['competition_id']);

            // Rename the column back from competition_id to league_id
            $table->renameColumn('competition_id', 'league_id');

            // Add the old foreign key constraint
            $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
        });
    }
};
