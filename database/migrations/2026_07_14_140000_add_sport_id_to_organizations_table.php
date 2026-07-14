<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jedan sport po organizaciji: sva takmicenja unutar organizacije nasljedjuju
     * njen sport umjesto da ga svako takmicenje bira posebno. Postojece organizacije
     * (provjereno prije ove migracije) sve vec imaju tacno jedan sport medju svojim
     * takmicenjima, pa se sport_id sigurno moze popuniti iz njih i uciniti obaveznim.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('organizations', 'sport_id')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->foreignId('sport_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            });
        }

        DB::statement('
            UPDATE organizations o
            SET sport_id = (
                SELECT c.sport_id FROM competitions c
                WHERE c.organization_id = o.id
                ORDER BY c.created_at ASC
                LIMIT 1
            )
            WHERE sport_id IS NULL
        ');

        // Organizacije bez ijednog takmicenja (nema odakle nasljediti sport) - zadrzi na
        // default prvom aktivnom sportu (Stoni Tenis) da kolona moze postati obavezna.
        $fallbackSportId = DB::table('sports')->where('is_active', true)->orderBy('id')->value('id');
        if ($fallbackSportId) {
            DB::table('organizations')->whereNull('sport_id')->update(['sport_id' => $fallbackSportId]);
        }

        // "SET NULL on delete" ne moze koegzistirati sa NOT NULL - sport koji je u
        // upotrebi ne treba ni brisati, pa mijenjamo na RESTRICT prije nego sto
        // kolona postane obavezna.
        DB::statement('ALTER TABLE organizations DROP FOREIGN KEY organizations_sport_id_foreign');
        DB::statement('ALTER TABLE organizations MODIFY sport_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE organizations ADD CONSTRAINT organizations_sport_id_foreign FOREIGN KEY (sport_id) REFERENCES sports(id)');
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropColumn('sport_id');
        });
    }
};
