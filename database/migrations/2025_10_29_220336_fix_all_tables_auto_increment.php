<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * * This migration fixes AUTO_INCREMENT issues on all tables in the database.
     * Some hosting providers or MySQL imports can lose AUTO_INCREMENT settings.
     */
    public function up(): void
    {
        // List of all tables that should have auto-increment id
        $tables = [
            'users',
            'organizations',
            'competitions',
            'leagues',
            'matches',
            'standings',
            'players',
            'teams',
            'tournament_groups',
            'tables', // Rezervisana riječ, pažljivo rukovati!
            'organization_user',
            'competition_player',
            'friendly_matches',
            'bug_reports',
            'sports',
            'plans',
            'user_plans',
            'sessions',
            'password_reset_tokens',
            'failed_jobs',
            'jobs',
            'job_batches',
            'cache',
            'cache_locks',
        ];

        $driver = DB::connection()->getDriverName();

        // Preskačemo SQLite jer on automatski mapira INTEGER PRIMARY KEY kao auto-increment
        if ($driver === 'sqlite') {
            return;
        }

        foreach ($tables as $table) {
            // Provjera da li tabela i kolona uopšte postoje
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'id')) {
                continue;
            }

            try {
                // 1. LOGIKA ZA MYSQL / MARIADB
                if ($driver === 'mysql') {
                    $result = DB::select("SHOW COLUMNS FROM `{$table}` WHERE Field = 'id'");
                    
                    if (!empty($result)) {
                        $column = $result[0];
                        $extra = $column->Extra ?? '';
                        
                        if (stripos($extra, 'auto_increment') === false) {
                            echo "Fixing AUTO_INCREMENT on MySQL table: {$table}\n";
                            
                            $type = strtoupper($column->Type ?? 'BIGINT');
                            if (stripos($type, 'BIGINT') !== false) {
                                DB::statement("ALTER TABLE `{$table}` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
                            } elseif (stripos($type, 'INT') !== false) {
                                DB::statement("ALTER TABLE `{$table}` MODIFY `id` INT UNSIGNED NOT NULL AUTO_INCREMENT");
                            }
                        }
                    }
                }
                
                // 2. LOGIKA ZA POSTGRESQL (Tvoj VPS server)
                elseif ($driver === 'pgsql') {
                    // Provjeravamo u sistemskoj tabeli da li kolona 'id' već ima dodijeljen default nextval (sekvencu)
                    $result = DB::select("
                        SELECT column_default 
                        FROM information_schema.columns 
                        WHERE table_name = ? AND column_name = 'id'
                    ", [$table]);

                    if (!empty($result)) {
                        $default = $result[0]->column_default ?? '';
                        
                        // Ako nema 'nextval', znači da mu fali sekvenca (auto-increment ponašanje)
                        if (stripos($default, 'nextval') === false) {
                            echo "Fixing sequence (AUTO_INCREMENT) on PostgreSQL table: {$table}\n";
                            
                            $seqName = "{$table}_id_seq";
                            
                            // Kreiramo sekvencu ako već ne postoji pod tim imenom
                            DB::statement("CREATE SEQUENCE IF NOT EXISTS \"{$seqName}\"");

                            // Postavljamo sekvencu kao podrazumijevanu vrijednost za ID kolonu
                            DB::statement("ALTER TABLE \"{$table}\" ALTER COLUMN \"id\" SET DEFAULT nextval('{$seqName}')");
                            DB::statement("ALTER SEQUENCE \"{$seqName}\" OWNED BY \"{$table}\".\"id\"");

                            // Sinhronizujemo brojač sekvence sa trenutno najvećim ID-jem u tabeli da ne bude dupliranja
                            // Prvo provjerimo tip kolone 'id' i koristimo setval samo ako je numeričkog tipa
                            $colType = DB::selectOne("SELECT data_type FROM information_schema.columns WHERE table_name = ? AND column_name = 'id'", [$table]);
                            $numericTypes = ['smallint','integer','bigint'];
                            if ($colType && in_array($colType->data_type, $numericTypes, true)) {
                                // Cast na bigint za sigurnost
                                DB::statement("SELECT setval('{$seqName}', COALESCE((SELECT MAX(id)::bigint FROM \"{$table}\"), 1))");
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Logujemo grešku za specifičnu tabelu ali nastavljamo dalje petlju
                echo "Error fixing table {$table}: " . $e->getMessage() . "\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ova migracija se ne vraća unazad jer popravlja integritet baze podataka
        echo "This migration cannot be reversed as it fixes critical database issues.\n";
    }
};