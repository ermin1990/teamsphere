<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A player who self-registers (before applying to join any
     * organization's competition) has no organization yet. There's no FK
     * constraint on this column in the live schema to worry about (plain
     * `int NOT NULL`) - just relax the not-null.
     */
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->integer('organization_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->integer('organization_id')->nullable(false)->change();
        });
    }
};
