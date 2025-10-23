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
            $table->foreignId('table_id')->nullable()->after('forfeited_by')->constrained('tables')->onDelete('set null');
            $table->foreignId('referee_user_id')->nullable()->after('table_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['table_id']);
            $table->dropForeign(['referee_user_id']);
            $table->dropColumn(['table_id', 'referee_user_id']);
        });
    }
};
