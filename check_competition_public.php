<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::where('slug', 'joola-kup-2025-juniorke-1761344299')->first();

if (!$competition) {
    echo "Competition not found\n";
    exit(1);
}

echo 'Competition: ' . $competition->name . "\n";
echo 'Is public: ' . ($competition->is_public ? 'Yes' : 'No') . "\n";
echo 'Status: ' . $competition->status . "\n";