<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Whether players can find and apply to this competition on the
     * "Takmičenja" browse list. Separate from is_public (spectator
     * visibility) - a league is closed for applications by default and only
     * appears for registration when the organizer explicitly opts in.
     */
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->boolean('registration_open')->default(false)->after('is_public');
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('registration_open');
        });
    }
};
