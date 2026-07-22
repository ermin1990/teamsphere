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
            $table->unsignedInteger('forfeit_winner_points')->nullable()->after('points_for_loss');
            $table->unsignedInteger('forfeit_loser_points')->nullable()->after('forfeit_winner_points');
            $table->boolean('forfeit_winner_counts_as_played')->default(true)->after('forfeit_loser_points');
            $table->boolean('forfeit_loser_counts_as_played')->default(false)->after('forfeit_winner_counts_as_played');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn([
                'forfeit_winner_points',
                'forfeit_loser_points',
                'forfeit_winner_counts_as_played',
                'forfeit_loser_counts_as_played',
            ]);
        });
    }
};
