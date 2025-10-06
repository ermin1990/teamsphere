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
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->foreignId('player_id')->nullable()->constrained('players')->onDelete('cascade');
            $table->integer('position')->default(1);
            $table->integer('played')->default(0);
            $table->integer('won')->default(0);
            $table->integer('drawn')->default(0);
            $table->integer('lost')->default(0);
            $table->integer('goals_for')->default(0);
            $table->integer('goals_against')->default(0);
            $table->integer('goal_difference')->default(0);
            $table->integer('points')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standings', function (Blueprint $table) {
            $table->dropForeign(['league_id']);
            $table->dropForeign(['team_id']);
            $table->dropForeign(['player_id']);
            $table->dropColumn([
                'league_id',
                'team_id',
                'player_id',
                'position',
                'played',
                'won',
                'drawn',
                'lost',
                'goals_for',
                'goals_against',
                'goal_difference',
                'points'
            ]);
        });
    }
};
