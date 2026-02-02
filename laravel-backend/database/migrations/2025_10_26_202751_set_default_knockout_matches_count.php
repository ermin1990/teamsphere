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
        // Set default knockout_matches_count to 7 for all existing tournaments
        DB::table('competitions')
            ->whereNull('knockout_matches_count')
            ->where('type', 'tournament')
            ->update(['knockout_matches_count' => 7]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - it's just setting defaults
    }
};
