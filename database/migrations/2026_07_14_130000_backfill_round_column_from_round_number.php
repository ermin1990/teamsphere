<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * BergerScheduleService je do sada upisivao kolo SAMO u round_number, a 'round'
     * (kolonu koju league-content.blade.php grupise/prikazuje za pojedinacne lige)
     * je ostavljao na default vrijednosti (1) za svaki mec. Ovo backfilluje 'round'
     * iz vec ispravnog 'round_number' za sve postojece mecceve gdje se razlikuju.
     */
    public function up(): void
    {
        DB::statement('UPDATE matches SET round = round_number WHERE round_number IS NOT NULL AND round != round_number');
    }

    public function down(): void
    {
        // Nema smislenog rollback-a - stara 'round' vrijednost (pogresan default) se ne cuva.
    }
};
