<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportLegacyData extends Command
{
    /**
     * @var string
     */
    protected $signature = 'db:import-legacy
        {file=database/legacy_data.pgsql : Putanja do konvertovanog SQL fajla}
        {--force : Ponovo uvezi (očisti pa uvezi) čak i ako je uvoz već obavljen}';

    /**
     * @var string
     */
    protected $description = 'Jednokratno čisti test podatke i uvozi stare (MySQL->PostgreSQL) podatke';

    /** Laravel/framework tabele koje NIKAD ne diramo. */
    private const FRAMEWORK = [
        'migrations', 'cache', 'cache_locks', 'sessions', 'jobs',
        'job_batches', 'failed_jobs', 'password_reset_tokens',
        'legacy_data_imports',
    ];

    private const MARKER_TABLE = 'legacy_data_imports';
    private const LOCK_KEY = 918273645;

    public function handle(): int
    {
        $path = base_path($this->argument('file'));

        if (!is_file($path)) {
            $this->warn("Legacy fajl ne postoji: {$path} — preskačem uvoz.");
            return self::SUCCESS; // ne ruši deploy
        }

        $this->ensureMarkerTable();

        // Idempotentnost: uvoz je već obavljen -> ne diramo bazu.
        if (!$this->option('force') && $this->alreadyImported()) {
            $this->info('Stari podaci su već uvezeni ranije — preskačem.');
            return self::SUCCESS;
        }

        $sql = file_get_contents($path);
        if ($sql === false || trim($sql) === '') {
            $this->warn('Legacy fajl je prazan — preskačem.');
            return self::SUCCESS;
        }

        // Skini vanjski BEGIN/COMMIT i SET session_replication_role iz fajla —
        // transakciju i gašenje FK-a kontroliše ova komanda (da truncate + uvoz
        // budu jedna atomska operacija: ili prođe sve, ili se ništa ne mijenja).
        $sql = preg_replace('/^\s*(BEGIN|COMMIT)\s*;\s*$/mi', '', $sql);
        $sql = preg_replace('/^\s*SET\s+session_replication_role.*$/mi', '', $sql);

        // Advisory lock: app i queue container startuju istovremeno — samo jedan smije uvoziti.
        DB::select('SELECT pg_advisory_lock(?)', [self::LOCK_KEY]);

        try {
            if (!$this->option('force') && $this->alreadyImported()) {
                $this->info('Drugi proces je već uvezao podatke — preskačem.');
                return self::SUCCESS;
            }

            $domain = $this->domainTables();

            $this->info('Čistim test podatke i uvozim stare podatke...');

            DB::transaction(function () use ($sql, $domain) {
                // FK provjere se gase samo unutar ove transakcije.
                DB::statement('SET session_replication_role = replica');

                if (!empty($domain)) {
                    $list = implode(', ', array_map(fn ($t) => '"' . $t . '"', $domain));
                    DB::statement("TRUNCATE {$list} RESTART IDENTITY CASCADE");
                }

                DB::unprepared($sql); // INSERT-i + setval sekvenci

                DB::statement('SET session_replication_role = DEFAULT');

                DB::table(self::MARKER_TABLE)->insert(['imported_at' => now()]);
            });
        } catch (\Throwable $e) {
            $this->error('Uvoz nije uspio (baza je vraćena u prethodno stanje): ' . $e->getMessage());
            return self::FAILURE;
        } finally {
            DB::select('SELECT pg_advisory_unlock(?)', [self::LOCK_KEY]);
        }

        $users = Schema::hasTable('users') ? DB::table('users')->count() : 0;
        $this->info("Uvoz završen. Korisnika u bazi: {$users}");

        return self::SUCCESS;
    }

    private function ensureMarkerTable(): void
    {
        DB::statement(
            'CREATE TABLE IF NOT EXISTS "' . self::MARKER_TABLE . '" ' .
            '(id serial PRIMARY KEY, imported_at timestamp NULL)'
        );
    }

    private function alreadyImported(): bool
    {
        return Schema::hasTable(self::MARKER_TABLE)
            && DB::table(self::MARKER_TABLE)->exists();
    }

    /** Sve tabele u 'public' schemi osim framework/marker tabela. */
    private function domainTables(): array
    {
        $rows = DB::select(
            "SELECT tablename FROM pg_tables WHERE schemaname = 'public'"
        );

        $names = array_map(fn ($r) => $r->tablename, $rows);

        return array_values(array_filter(
            $names,
            fn ($t) => !in_array($t, self::FRAMEWORK, true)
        ));
    }
}
