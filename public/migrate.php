<?php
/**
 * Web-based MySQL Migration for Shared Hosting
 *
 * Access this file via browser: https://yourdomain.com/migrate.php
 * After successful migration, DELETE this file for security!
 */

echo "<h1>TeamSphere MySQL Migration</h1>";
echo "<pre>";

// Check if .env.production exists
if (!file_exists(__DIR__ . '/../.env.production')) {
    die("❌ ERROR: .env.production file not found. Please upload it to the project root first.\n");
}

// Load production environment
$envContent = file_get_contents(__DIR__ . '/../.env.production');
$lines = explode("\n", $envContent);
$env = [];
foreach ($lines as $line) {
    if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
        list($key, $value) = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
}

echo "✅ Loaded environment configuration\n";

// Debug: Show loaded DB credentials
echo "🔍 Debug - Loaded credentials:\n";
echo "   DB_HOST: {$env['DB_HOST']}\n";
echo "   DB_DATABASE: {$env['DB_DATABASE']}\n";
echo "   DB_USERNAME: {$env['DB_USERNAME']}\n";
echo "   DB_PASSWORD: " . (empty($env['DB_PASSWORD']) ? '(empty)' : '(set)') . "\n\n";

// Database configuration
$mysqlConfig = [
    'host' => $env['DB_HOST'] ?? 'localhost',
    'port' => $env['DB_PORT'] ?? '3306',
    'database' => $env['DB_DATABASE'] ?? 'teamsphere_db',
    'username' => $env['DB_USERNAME'] ?? '',
    'password' => $env['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
];

echo "📋 MySQL Config: {$mysqlConfig['host']}:{$mysqlConfig['port']}/{$mysqlConfig['database']}\n";

// Check SQLite database exists
$sqlitePath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($sqlitePath)) {
    die("❌ ERROR: SQLite database not found at: $sqlitePath\n");
}

echo "✅ Found SQLite database\n";

// Create PDO connections
try {
    $sqlite = new PDO("sqlite:" . $sqlitePath);
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

    echo "✅ Connected to MySQL database\n";

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
        $columnType = convertSqliteTypeToMySQL($row['type'], $row['pk']);
        $columns[] = "`{$row['name']}` $columnType" .
                    ($row['notnull'] ? ' NOT NULL' : ' NULL') .
                    ($row['dflt_value'] !== null ? " DEFAULT {$row['dflt_value']}" : '') .
                    ($row['pk'] ? ' PRIMARY KEY' : '');
    }

    // Create table in MySQL
    $dropTableSQL = "DROP TABLE IF EXISTS `$table`";
    $mysql->exec($dropTableSQL);

    $createTableSQL = "CREATE TABLE `$table` (" . implode(', ', $columns) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
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
echo "<strong style='color: red;'>⚠️  SECURITY WARNING: Delete this file immediately after migration!</strong>\n";
echo "<strong style='color: green;'>✅ Next steps:</strong>\n";
echo "   1. Rename .env.production to .env\n";
echo "   2. Delete migrate.php file\n";
echo "   3. Test your application\n";

/**
 * Convert SQLite column types to MySQL equivalents
 */
function convertSqliteTypeToMySQL($sqliteType, $isPrimaryKey = false) {
    $type = strtolower($sqliteType);

    if (strpos($type, 'integer') !== false) {
        return $isPrimaryKey ? 'INT(11) AUTO_INCREMENT' : 'INT(11)';
    } elseif (strpos($type, 'varchar') !== false) {
        // Extract length if specified, e.g., varchar(255)
        if (preg_match('/varchar\((\d+)\)/', $type, $matches)) {
            return 'VARCHAR(' . $matches[1] . ')';
        }
        return $isPrimaryKey ? 'VARCHAR(255)' : 'TEXT';
    } elseif (strpos($type, 'text') !== false) {
        return $isPrimaryKey ? 'VARCHAR(255)' : 'TEXT';
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
        return $isPrimaryKey ? 'VARCHAR(255)' : 'VARCHAR(255)';
    }
}

echo "</pre>";
?>