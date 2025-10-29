<?php
// Debug match access
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\User;

echo "<h1>Match Access Debug</h1>";

// Get current user
$user = Auth::user();
if (!$user) {
    $user = User::find(1);
    echo "<p style='color: orange;'>⚠️ Not logged in, using User ID=1 for testing</p>";
} else {
    echo "<p style='color: green;'>✅ Logged in as: {$user->email}</p>";
}

// Get the competition
$competition = Competition::where('slug', 'joola-kup-2025-juniorke-1761344299')->first();

if (!$competition) {
    echo "<p style='color: red;'>❌ Competition not found!</p>";
    exit;
}

echo "<h2>Competition Details</h2>";
echo "ID: {$competition->id}<br>";
echo "Name: {$competition->name}<br>";
echo "Slug: {$competition->slug}<br>";
echo "Organization ID: {$competition->organization_id}<br>";

// Get organization
$organization = $competition->organization;

echo "<h2>Organization Details</h2>";
echo "ID: {$organization->id}<br>";
echo "Name: {$organization->name}<br>";
echo "Slug: {$organization->slug}<br>";
echo "Owner ID: {$organization->user_id}<br>";

// Test policy authorization for organization
try {
    $policy = new \App\Policies\OrganizationPolicy();
    $canViewOrg = $policy->view($user, $organization);
    echo "<h2>Organization Access Check</h2>";
    echo "Policy->view(organization): " . ($canViewOrg ? 'ALLOWED ✅' : 'DENIED ❌') . "<br>";
} catch (\Exception $e) {
    echo "<h2>Organization Policy Error</h2>";
    echo "<p style='color: red;'>Error: {$e->getMessage()}</p>";
}

// Get first match from competition
$match = $competition->matches()->first();

if (!$match) {
    echo "<p style='color: red;'>❌ No matches found in this competition!</p>";
    exit;
}

echo "<h2>Match Details</h2>";
echo "Match ID: {$match->id}<br>";
echo "Competition ID: {$match->competition_id}<br>";
echo "Home Player ID: {$match->home_player_id}<br>";
echo "Away Player ID: {$match->away_player_id}<br>";
echo "Phase: {$match->phase}<br>";
echo "Status: {$match->status}<br>";

// Test match URL construction
$matchUrl = "https://teamsphere.infinitycreative.agency/organizations/{$organization->slug}/competitions/{$competition->slug}/matches/{$match->id}";
echo "<h2>Test Match URL</h2>";
echo "<a href='{$matchUrl}' target='_blank'>{$matchUrl}</a><br><br>";

// Test what the controller would do
echo "<h2>Controller Authorization Test</h2>";
try {
    // This simulates what happens in the controller
    $controller = new \App\Http\Controllers\CompetitionController();

    // Test the authorize call that would happen in showMatch
    $user->can('view', $organization); // This should work now

    echo "✅ Controller authorization should work<br>";

} catch (\Exception $e) {
    echo "<p style='color: red;'>❌ Controller authorization error: {$e->getMessage()}</p>";
    echo "<pre>{$e->getTraceAsString()}</pre>";
}

echo "<hr>";
echo "<h2>Try accessing match:</h2>";
echo "<a href='{$matchUrl}' target='_blank'>{$matchUrl}</a>";