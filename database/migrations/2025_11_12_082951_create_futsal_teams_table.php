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
        Schema::create('futsal_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('competition_id')->nullable()->constrained('competitions')->onDelete('set null');
            $table->string('name');
            $table->string('short_name', 10)->nullable(); // TUZ, SAR, MOS
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('primary_color', 7)->default('#1E40AF'); // Hex color
            $table->string('secondary_color', 7)->default('#FFFFFF');
            $table->string('captain_name')->nullable();
            $table->string('coach_name')->nullable();
            $table->string('home_venue')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['organization_id', 'is_active']);
            $table->index('competition_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('futsal_teams');
    }
};
