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
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('sport_id')->constrained()->onDelete('cascade');
            if (DB::getDriverName() === 'pgsql') {
                $table->string('status')->default('draft');
            } else {
                $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            }
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('max_teams')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE leagues DROP CONSTRAINT IF EXISTS leagues_status_check');
            DB::statement("ALTER TABLE leagues ADD CONSTRAINT leagues_status_check CHECK (status IN ('draft','active','completed','cancelled'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leagues');
    }
};
