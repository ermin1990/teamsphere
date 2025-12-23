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
            if (!Schema::hasColumn('matches', 'team_match_id')) {
                $table->foreignId('team_match_id')->nullable()->after('competition_id')->constrained('team_matches')->onDelete('cascade');
            }
            if (!Schema::hasColumn('matches', 'position_code')) {
                $table->string('position_code')->nullable()->after('team_match_id'); // A-X, B-Y, C-Z, Dubl, etc.
            }
            if (!Schema::hasColumn('matches', 'match_order')) {
                $table->integer('match_order')->default(0)->after('position_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['team_match_id']);
            $table->dropColumn(['team_match_id', 'position_code', 'match_order']);
        });
    }
};
