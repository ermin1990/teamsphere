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
        Schema::create('bug_reports', function (Blueprint $table) {
            $table->id();
                if (DB::getDriverName() === 'pgsql') {
                    $table->string('type');
                } else {
                    $table->enum('type', ['bug', 'feature']);
                }
            $table->string('subject');
            $table->text('description');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            if (DB::getDriverName() === 'pgsql') {
                $table->string('status')->default('pending');
            } else {
                $table->enum('status', ['pending', 'in_review', 'resolved', 'closed'])->default('pending');
            }
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE bug_reports DROP CONSTRAINT IF EXISTS bug_reports_type_check");
            DB::statement("ALTER TABLE bug_reports ADD CONSTRAINT bug_reports_type_check CHECK (type IN ('bug','feature'))");
            DB::statement("ALTER TABLE bug_reports DROP CONSTRAINT IF EXISTS bug_reports_status_check");
            DB::statement("ALTER TABLE bug_reports ADD CONSTRAINT bug_reports_status_check CHECK (status IN ('pending','in_review','resolved','closed'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bug_reports');
    }
};
