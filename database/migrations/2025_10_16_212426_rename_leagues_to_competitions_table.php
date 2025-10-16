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
        // Rename leagues table to competitions
        Schema::rename('leagues', 'competitions');

        // Add tournament-specific fields
        Schema::table('competitions', function (Blueprint $table) {
            $table->enum('type', ['league', 'tournament'])->default('league')->after('organization_id');
            $table->integer('max_participants')->nullable()->after('type');
            $table->integer('group_count')->nullable()->after('max_participants');
            $table->integer('players_per_group')->nullable()->after('group_count');
            $table->integer('players_advancing_per_group')->nullable()->after('players_per_group');
            $table->enum('advancement_method', ['automatic', 'manual'])->default('automatic')->after('players_advancing_per_group');
            $table->enum('current_phase', ['groups', 'knockout', 'completed'])->default('groups')->after('advancement_method');
            $table->json('knockout_bracket')->nullable()->after('current_phase');
            $table->timestamp('groups_completed_at')->nullable()->after('knockout_bracket');
            $table->timestamp('knockout_completed_at')->nullable()->after('groups_completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove tournament-specific fields
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'max_participants',
                'group_count',
                'players_per_group',
                'players_advancing_per_group',
                'advancement_method',
                'current_phase',
                'knockout_bracket',
                'groups_completed_at',
                'knockout_completed_at'
            ]);
        });

        // Rename competitions table back to leagues
        Schema::rename('competitions', 'leagues');
    }
};
