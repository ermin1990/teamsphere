<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->foreignId('season_id')->nullable()->after('category_id')->constrained('seasons')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('season_id')->constrained('cities')->nullOnDelete();
            $table->dateTime('registration_deadline')->nullable()->after('city_id');
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('season_id');
            $table->dropConstrainedForeignId('city_id');
            $table->dropColumn('registration_deadline');
        });
    }
};
