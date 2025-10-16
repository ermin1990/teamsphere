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
        // Add type field to leagues table
        Schema::table('leagues', function (Blueprint $table) {
            $table->enum('type', ['league', 'tournament'])->default('league')->after('sport_id');
        });

        // Rename table from leagues to competitions
        Schema::rename('leagues', 'competitions');
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
