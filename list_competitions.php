<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competitions = App\Models\Competition::all();

echo "All competitions:\n";
foreach($competitions as $comp) {
    echo $comp->id . ': ' . $comp->name . ' - Slug: ' . $comp->slug . "\n";
}