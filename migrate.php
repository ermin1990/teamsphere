<?php

// migrate.php - Web-based migration script za TeamSphere

echo "<h1>TeamSphere Database Migration</h1>";
echo "<pre>";

// Provjera da li je setup.php već pokrenut
if (!file_exists(__DIR__ . '/.env')) {
    echo "❌ .env fajl ne postoji. Prvo pokrenite setup.php\n";
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

// Učitavanje .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Laravel bootstrap
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 Pokrećem migracije...\n";

try {
    // Pokretanje migracija
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');

    // Koristimo reflection da pristupimo migracijama
    $migrator = $app->make('migrator');

    if (!$migrator->repositoryExists()) {
        $migrator->getRepository()->createRepository();
        echo "✅ Kreirana migrations tabela\n";
    }

    $files = $migrator->getMigrationFiles(__DIR__ . '/database/migrations');

    $ran = $migrator->getRepository()->getRan();

    $migrations = array_diff(array_keys($files), $ran);

    if (empty($migrations)) {
        echo "✅ Sve migracije su već pokrenute\n";
    } else {
        echo "📋 Pronađene migracije za pokretanje:\n";
        foreach ($migrations as $migration) {
            echo "  - $migration\n";
        }

        foreach ($migrations as $migration) {
            echo "🔄 Pokrećem: $migration\n";

            $migrator->runPending([$migration]);

            echo "✅ Završeno: $migration\n";
        }

        echo "\n🎉 Sve migracije uspješno pokrenute!\n";
    }

} catch (Exception $e) {
    echo "❌ Greška prilikom migracija: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n⚠️  Obrišite ovaj migrate.php fajl nakon što završite!\n";

?>