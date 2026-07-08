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
        {file=database/legacy_data.pgsql : Putanja do konvertovanog SQL fajla}';

    /**
     * @var string
     */
    protected $description = 'Čisti sve podatke i uvozi stare (MySQL->PostgreSQL) podatke pri svakom deployu';

    /** Laravel/framework tabele koje NIKAD ne diramo. */
    private const FRAMEWORK = [
        'migrations', 'cache', 'cache_locks', 'sessions', 'jobs',
        'job_batches', 'failed_jobs', 'password_reset_tokens',
    ];

    private const LOCK_KEY = 918273645;

    public function handle(): int
    {
        $path = base_path($this->argument('file'));

        if (!is_file($path)) {
            $this->warn("Legacy fajl ne postoji: {$path} — preskačem uvoz.");
            return self::SUCCESS;
        }

        $sql = file_get_contents($path);
        if ($sql === false || trim($sql) === '') {
            $this->warn('Legacy fajl je prazan — preskačem.');
            return self::SUCCESS;
        }

        $sql = preg_replace('/^\s*(BEGIN|COMMIT)\s*;\s*$/mi', '', $sql);
        $sql = preg_replace('/^\s*SET\s+session_replication_role.*$/mi', '', $sql);

        DB::select('SELECT pg_advisory_lock(?)', [self::LOCK_KEY]);

        try {
            $domain = $this->domainTables();

            $this->info('Čistim sve podatke i uvozim iz legacy dump-a...');

            DB::transaction(function () use ($sql, $domain) {
                DB::statement('SET session_replication_role = replica');

                if (!empty($domain)) {
                    $list = implode(', ', array_map(fn ($t) => '"' . $t . '"', $domain));
                    DB::statement("TRUNCATE {$list} RESTART IDENTITY CASCADE");
                }

                DB::unprepared($sql);

                DB::statement('SET session_replication_role = DEFAULT');
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

    /** Sve tabele u 'public' schemi osim framework tabela. */
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
