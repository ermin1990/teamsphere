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
        $driver = DB::getDriverName();

        // 1. Ako je baza SQLite (Lokalni razvoj) - zadržavamo tvoj stari sistem rekonstrukcije tabele
        if ($driver === 'sqlite') {
            
            // !!! PAŽNJA: Ovdje unutar ovog IF bloka prekopiraj/zadrži SVE ono što je u tvom 
            // originalnom fajlu bilo unutar up() metode za SQLite (backup, drop i ponovno kreiranje).
            
        // 2. Ako je baza PostgreSQL (Tvoj VPS produkcioni server) - NE brišemo tabelu!
        } elseif ($driver === 'pgsql') {
            
            // Bezbjedno brišemo stari check constraint za 'type' kolonu u Postgresu
            DB::statement('ALTER TABLE competitions DROP CONSTRAINT IF EXISTS competitions_type_check');
            
            // ⚠️ OVDJE ZAMIJENI VRIJEDNOSTI: Unutar IN (...) stavi sve dozvoljene tipove (i stare i nove)
            // Na primjer: 'league', 'tournament', 'knockout', 'cup' itd. Šta god da tvoja aplikacija koristi.
            DB::statement("ALTER TABLE competitions ADD CONSTRAINT competitions_type_check CHECK (type IN ('league', 'tournament', 'cup', 'knockout'))");

        // 3. Za MySQL
        } elseif ($driver === 'mysql') {
            // Zamijeni vrijednosti enuma i ovdje ako koristiš MySQL negdje
            DB::statement("ALTER TABLE competitions MODIFY COLUMN type ENUM('league', 'tournament', 'cup', 'knockout')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // Ovdje zadrži svoj originalni down() kod za SQLite rekonstrukciju
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE competitions DROP CONSTRAINT IF EXISTS competitions_type_check');
            // Ovdje vrati samo STARE vrijednosti enuma (prije nego što si ih proširio ovom migracijom)
            DB::statement("ALTER TABLE competitions ADD CONSTRAINT competitions_type_check CHECK (type IN ('league', 'tournament'))");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE competitions MODIFY COLUMN type ENUM('league', 'tournament')");
        }
    }
};
