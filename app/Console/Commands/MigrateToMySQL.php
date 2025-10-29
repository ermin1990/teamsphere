<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;
use PDOException;

class MigrateToMySQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate-to-mysql {--backup : Create backup of SQLite database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from SQLite to MySQL database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting SQLite to MySQL migration...');

        // Check if we're already using MySQL
        if (config('database.default') === 'mysql') {
            $this->warn('⚠️  Already using MySQL as default database connection.');
            return;
        }

        // Backup SQLite database if requested
        if ($this->option('backup')) {
            $this->backupSqliteDatabase();
        }

        // Test MySQL connection
        if (!$this->testMySQLConnection()) {
            $this->error('❌ Cannot connect to MySQL. Please check your .env configuration.');
            return;
        }

        // Get all tables from SQLite
        $tables = $this->getSqliteTables();

        if (empty($tables)) {
            $this->warn('⚠️  No tables found in SQLite database.');
            return;
        }

        $this->info('📋 Found tables: ' . implode(', ', $tables));

        // Confirm migration
        if (!$this->confirm('This will migrate all data from SQLite to MySQL. Continue?')) {
            $this->info('Migration cancelled.');
            return;
        }

        // Run Laravel migrations first
        $this->info('🔧 Running Laravel migrations on MySQL...');
        $this->call('migrate', ['--force' => true]);

        // Migrate data
        $this->migrateData($tables);

        $this->info('🎉 Migration completed successfully!');
        $this->line('');
        $this->info('📝 Next steps:');
        $this->line('   1. Update your .env file: DB_CONNECTION=mysql');
        $this->line('   2. Run: php artisan config:clear');
        $this->line('   3. Run: php artisan cache:clear');
        $this->line('   4. Test your application');
        $this->line('   5. Optionally, remove the SQLite database file');
    }

    /**
     * Test MySQL connection
     */
    private function testMySQLConnection(): bool
    {
        try {
            $pdo = new PDO(
                "mysql:host=" . env('DB_HOST', '127.0.0.1') .
                ";port=" . env('DB_PORT', '3306') .
                ";charset=utf8mb4",
                env('DB_USERNAME', 'root'),
                env('DB_PASSWORD', '')
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch (PDOException $e) {
            $this->error('MySQL connection failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all tables from SQLite database
     */
    private function getSqliteTables(): array
    {
        try {
            $tables = DB::connection('sqlite')->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            return array_column($tables, 'name');
        } catch (\Exception $e) {
            $this->error('Failed to get SQLite tables: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Backup SQLite database
     */
    private function backupSqliteDatabase(): void
    {
        $sqlitePath = database_path('database.sqlite');
        $backupPath = database_path('database.sqlite.backup');

        if (file_exists($sqlitePath)) {
            copy($sqlitePath, $backupPath);
            $this->info('✅ SQLite database backed up to: ' . $backupPath);
        } else {
            $this->warn('⚠️  SQLite database file not found, skipping backup.');
        }
    }

    /**
     * Migrate data from SQLite to MySQL
     */
    private function migrateData(array $tables): void
    {
        $this->info('🔄 Migrating data...');

        $progressBar = $this->output->createProgressBar(count($tables));
        $progressBar->start();

        foreach ($tables as $table) {
            try {
                $this->migrateTable($table);
                $progressBar->advance();
            } catch (\Exception $e) {
                $this->error("Failed to migrate table '$table': " . $e->getMessage());
            }
        }

        $progressBar->finish();
        $this->line('');
    }

    /**
     * Migrate a single table
     */
    private function migrateTable(string $table): void
    {
        // Skip Laravel system tables
        if (in_array($table, ['migrations', 'failed_jobs', 'cache', 'sessions'])) {
            return;
        }

        // Get data from SQLite
        $data = DB::connection('sqlite')->table($table)->get();

        if ($data->isEmpty()) {
            return;
        }

        // Convert to array
        $dataArray = $data->map(function ($item) {
            return (array) $item;
        })->toArray();

        // Insert into MySQL in chunks
        $chunks = array_chunk($dataArray, 100);

        foreach ($chunks as $chunk) {
            DB::connection('mysql')->table($table)->insert($chunk);
        }
    }
}
