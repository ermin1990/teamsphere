<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Other drivers (mysql/sqlite) store role as a plain varchar with no
        // DB-level enum/check constraint, so there is nothing to relax there -
        // only Postgres enforces a CHECK constraint on this column.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE organization_user DROP CONSTRAINT IF EXISTS organization_user_role_check');
            DB::statement("ALTER TABLE organization_user ADD CONSTRAINT organization_user_role_check CHECK (role IN ('owner','referee','moderator'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE organization_user DROP CONSTRAINT IF EXISTS organization_user_role_check');
            DB::statement("ALTER TABLE organization_user ADD CONSTRAINT organization_user_role_check CHECK (role IN ('owner','referee'))");
        }
    }
};
