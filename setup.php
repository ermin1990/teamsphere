<?php

// setup.php - Web-based setup script za TeamSphere deploy bez terminala

echo "<h1>TeamSphere Setup</h1>";
echo "<pre>";

// 1. Provjera PHP verzije
echo "PHP verzija: " . PHP_VERSION . "\n";
if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    echo "❌ PHP verzija mora biti 8.2 ili novija\n";
    exit;
} else {
    echo "✅ PHP verzija je OK\n";
}

// 2. Provjera da li postoji vendor folder
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "❌ Vendor folder nije pronađen. Trebate upload-ati vendor folder.\n";
    exit;
} else {
    echo "✅ Vendor folder pronađen\n";
}

// 3. Provjera .env fajla
if (!file_exists(__DIR__ . '/.env')) {
    echo "❌ .env fajl ne postoji. Kopirajte .env.example u .env\n";
    exit;
} else {
    echo "✅ .env fajl postoji\n";
}

// 4. Učitavanje .env fajla
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 5. Generisanje application key
echo "Generišem application key...\n";
$key = 'base64:' . base64_encode(random_bytes(32));
file_put_contents(__DIR__ . '/.env', preg_replace(
    '/^APP_KEY=.*/m',
    'APP_KEY=' . $key,
    file_get_contents(__DIR__ . '/.env')
));
echo "✅ Application key generisan\n";

// 6. Testiranje baze podataka
try {
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    echo "✅ Konekcija sa bazom podataka uspješna\n";
} catch (Exception $e) {
    echo "❌ Greška sa bazom podataka: " . $e->getMessage() . "\n";
}

// 7. Provjera storage permissions
$storagePaths = [
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($storagePaths as $path) {
    $fullPath = __DIR__ . '/' . $path;
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0755, true);
        echo "✅ Kreiran folder: $path\n";
    }

    if (is_writable($fullPath)) {
        echo "✅ Folder je writable: $path\n";
    } else {
        echo "⚠️  Folder možda nije writable: $path\n";
    }
}

// 8. Cache cleanup
$cacheFiles = glob(__DIR__ . '/bootstrap/cache/*.php');
foreach ($cacheFiles as $file) {
    if (is_file($file)) {
        unlink($file);
        echo "✅ Obrisana cache fajl: " . basename($file) . "\n";
    }
}

echo "\n🎉 Setup završen!\n";
echo "\nSljedeći koraci:\n";
echo "1. Obrišite ovaj setup.php fajl\n";
echo "2. Provjerite da li aplikacija radi\n";
echo "3. Ako imate migracije, pokrenite ih preko web baze ili pitajte hosting support\n";

?>