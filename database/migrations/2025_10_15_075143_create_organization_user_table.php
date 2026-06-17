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
        Schema::create('organization_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            if (DB::getDriverName() === 'pgsql') {
                $table->string('role')->default('referee');
            } else {
                $table->enum('role', ['owner', 'referee'])->default('referee');
            }
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['organization_id', 'user_id']); // Prevent duplicate entries
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE organization_user DROP CONSTRAINT IF EXISTS organization_user_role_check');
            DB::statement("ALTER TABLE organization_user ADD CONSTRAINT organization_user_role_check CHECK (role IN ('owner','referee'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_user');
    }
};
