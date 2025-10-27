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
        Schema::table('competitions', function (Blueprint $table) {
            // Make tournament fields nullable for more flexibility
            $table->integer('max_participants')->nullable()->change();
            $table->integer('group_count')->nullable()->change();
            $table->integer('players_per_group')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            // Revert back to NOT NULL (with default values)
            $table->integer('max_participants')->nullable(false)->default(16)->change();
            $table->integer('group_count')->nullable(false)->default(4)->change();
            $table->integer('players_per_group')->nullable(false)->default(4)->change();
        });
    }
};
