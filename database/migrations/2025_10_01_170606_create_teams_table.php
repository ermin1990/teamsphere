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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('league_id');
            $table->foreignId('captain_id')->nullable()->constrained('users')->onDelete('set null');
            if (DB::getDriverName() === 'pgsql') {
                $table->string('status')->default('active');
            } else {
                $table->enum('status', ['active', 'inactive', 'disqualified'])->default('active');
            }
            $table->timestamps();
        });

        if (Schema::hasTable('leagues')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
            });
        } elseif (Schema::hasTable('competitions')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->foreign('league_id')->references('id')->on('competitions')->onDelete('cascade');
            });
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE teams DROP CONSTRAINT IF EXISTS teams_status_check');
            DB::statement("ALTER TABLE teams ADD CONSTRAINT teams_status_check CHECK (status IN ('active','inactive','disqualified'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
