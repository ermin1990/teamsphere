<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Organization;
use App\Models\Competition;

$user = User::where('email', 'admin123@teamsphere.com')->first();

if (!$user) {
    echo "User not found\n";
    exit;
}

echo "User: {$user->name} (ID: {$user->id})\n";

$ownedOrganizations = Organization::where('user_id', $user->id)->get();
echo "Owned Organizations: " . $ownedOrganizations->count() . "\n";
foreach ($ownedOrganizations as $org) {
    echo "- {$org->name} (ID: {$org->id})\n";
    $comps = Competition::where('organization_id', $org->id)->get();
    echo "  Competitions: " . $comps->count() . "\n";
    foreach ($comps as $comp) {
        echo "    * {$comp->name} (ID: {$comp->id}, Type: {$comp->type})\n";
    }
}

$playerOrganizations = $user->organizations;
echo "Player Organizations: " . $playerOrganizations->count() . "\n";
foreach ($playerOrganizations as $org) {
    echo "- {$org->name} (ID: {$org->id})\n";
}
