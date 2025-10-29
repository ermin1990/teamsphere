<?php

/**
 * SQLite to MySQL Migration Script
 *
 * This script helps migrate data from SQLite to MySQL database.
 * Run this after setting up MySQL connection in .env file.
 *
 * Usage: php migrate_to_mysql.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database configurations
$sqliteConfig = [
    'driver' => 'sqlite',
    'database' => database_path('database.sqlite'),
];

$mysqlConfig = [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'teamsphere'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];

echo "🚀 Starting SQLite to MySQL migration...\n\n";

// Create PDO connections
try {
    $sqlite = new PDO("sqlite:" . $sqliteConfig['database']);
    $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $mysql = new PDO(
        "mysql:host={$mysqlConfig['host']};port={$mysqlConfig['port']};charset={$mysqlConfig['charset']}",
        $mysqlConfig['username'],
        $mysqlConfig['password']
    );
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $mysql->exec("CREATE DATABASE IF NOT EXISTS `{$mysqlConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $mysql->exec("USE `{$mysqlConfig['database']}`");

    echo "✅ Connected to databases\n";

} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage() . "\n");
}

// Get all tables from SQLite
$tables = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN);

echo "📋 Found tables: " . implode(', ', $tables) . "\n\n";

foreach ($tables as $table) {
    echo "🔄 Migrating table: $table\n";

    // Get table structure
    $columns = [];
    $result = $sqlite->query("PRAGMA table_info($table)");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $columnType = convertSqliteTypeToMySQL($row['type']);
        $columns[] = "`{$row['name']}` $columnType" .
                    ($row['notnull'] ? ' NOT NULL' : ' NULL') .
                    ($row['dflt_value'] !== null ? " DEFAULT {$row['dflt_value']}" : '') .
                    ($row['pk'] ? ' AUTO_INCREMENT PRIMARY KEY' : '');
    }

    // Create table in MySQL
    $createTableSQL = "CREATE TABLE IF NOT EXISTS `$table` (" . implode(', ', $columns) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $mysql->exec($createTableSQL);

    // Get data from SQLite
    $data = $sqlite->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($data)) {
        // Prepare INSERT statement
        $columnsList = array_keys($data[0]);
        $placeholders = str_repeat('?,', count($columnsList) - 1) . '?';

        $insertSQL = "INSERT INTO `$table` (`" . implode('`, `', $columnsList) . "`) VALUES ($placeholders)";
        $stmt = $mysql->prepare($insertSQL);

        // Insert data in batches
        $batchSize = 100;
        $totalInserted = 0;

        for ($i = 0; $i < count($data); $i += $batchSize) {
            $batch = array_slice($data, $i, $batchSize);

            foreach ($batch as $row) {
                $values = array_values($row);
                $stmt->execute($values);
                $totalInserted++;
            }
        }

        echo "   ✅ Inserted $totalInserted rows\n";
    } else {
        echo "   ℹ️  No data to migrate\n";
    }

    echo "\n";
}

// Handle indexes and foreign keys (basic implementation)
echo "🔗 Creating indexes and constraints...\n";

foreach ($tables as $table) {
    // Get indexes
    $indexes = $sqlite->query("PRAGMA index_list($table)")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($indexes as $index) {
        if (strpos($index['name'], 'sqlite_autoindex') === false) {
            $indexInfo = $sqlite->query("PRAGMA index_info({$index['name']})")->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($indexInfo)) {
                $columns = array_column($indexInfo, 'name');
                $indexName = $index['name'];
                $unique = $index['unique'] ? 'UNIQUE' : '';

                $createIndexSQL = "CREATE $unique INDEX `$indexName` ON `$table` (`" . implode('`, `', $columns) . "`)";
                try {
                    $mysql->exec($createIndexSQL);
                    echo "   ✅ Created index: $indexName\n";
                } catch (PDOException $e) {
                    echo "   ⚠️  Failed to create index $indexName: " . $e->getMessage() . "\n";
                }
            }
        }
    }
}

echo "\n🎉 Migration completed successfully!\n";
echo "📝 Next steps:\n";
echo "   1. Update your .env file to use MySQL\n";
echo "   2. Run: php artisan config:clear\n";
echo "   3. Run: php artisan cache:clear\n";
echo "   4. Test your application\n";
echo "   5. Optionally, backup and remove the SQLite file\n";

/**
 * Convert SQLite column types to MySQL equivalents
 */
function convertSqliteTypeToMySQL($sqliteType) {
    $type = strtolower($sqliteType);

    if (strpos($type, 'integer') !== false) {
        return 'INT(11)';
    } elseif (strpos($type, 'varchar') !== false || strpos($type, 'text') !== false) {
        return 'TEXT';
    } elseif (strpos($type, 'real') !== false || strpos($type, 'float') !== false || strpos($type, 'double') !== false) {
        return 'DECIMAL(10,2)';
    } elseif (strpos($type, 'boolean') !== false) {
        return 'TINYINT(1)';
    } elseif (strpos($type, 'datetime') !== false) {
        return 'DATETIME';
    } elseif (strpos($type, 'date') !== false) {
        return 'DATE';
    } elseif (strpos($type, 'timestamp') !== false) {
        return 'TIMESTAMP';
    } elseif (strpos($type, 'blob') !== false) {
        return 'BLOB';
    } else {
        // Default to VARCHAR for unknown types
        return 'VARCHAR(255)';
    }
}