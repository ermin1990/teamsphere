<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::find(17);
$competition->generateGroupMatches();

echo "Matches generated successfully!\n";