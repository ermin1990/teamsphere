<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixAutoIncrement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fix-auto-increment {table=users} {--dry-run : Show what would be executed without changing anything}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix AUTO_INCREMENT pointer for a table by setting it to MAX(id)+1';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $table = $this->argument('table');

        if (!Schema::hasTable($table)) {
            $this->error("Table '{$table}' does not exist.");
            return 1;
        }
        if (!Schema::hasColumn($table, 'id')) {
            $this->error("Table '{$table}' has no 'id' column.");
            return 1;
        }

        $maxId = DB::table($table)->max('id');
        $next = is_null($maxId) ? 1 : ($maxId + 1);

        $this->line("Table: {$table}");
        $this->line("MAX(id): " . ($maxId === null ? 'null' : $maxId));
        $this->line("Setting AUTO_INCREMENT to: {$next}");

        if ($this->option('dry-run')) {
            $this->info('[DRY RUN] No changes applied.');
            return 0;
        }

        // MySQL/MariaDB specific
        DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = {$next}");

        $this->info("AUTO_INCREMENT updated successfully for '{$table}'.");
        return 0;
    }
}
