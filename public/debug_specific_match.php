<?php
// Debug specific match 769
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\User;

echo "<h1>Debug Match 769</h1>";

// Get match 769
$match = CompetitionMatch::find(769);

if (!$match) {
    echo "<p style='color: red;'>❌ Match 769 not found in database!</p>";
    exit;
}

echo "<h2>Match Details</h2>";
echo "Match ID: {$match->id}<br>";
echo "Competition ID: {$match->competition_id}<br>";
echo "Home Player ID: {$match->home_player_id}<br>";
echo "Away Player ID: {$match->away_player_id}<br>";
echo "Phase: {$match->phase}<br>";
echo "Status: {$match->status}<br>";

// Get competition
$competition = $match->competition;
if (!$competition) {
    echo "<p style='color: red;'>❌ Competition not found for this match!</p>";
    exit;
}

echo "<h2>Competition Details</h2>";
echo "ID: {$competition->id}<br>";
echo "Name: {$competition->name}<br>";
echo "Slug: {$competition->slug}<br>";
echo "Organization ID: {$competition->organization_id}<br>";
echo "Organization ID type: " . gettype($competition->organization_id) . "<br>";

// Get organization
$organization = $competition->organization;
if (!$organization) {
    echo "<p style='color: red;'>❌ Organization not found for this competition!</p>";
    exit;
}

echo "<h2>Organization Details</h2>";
echo "ID: {$organization->id}<br>";
echo "Name: {$organization->name}<br>";
echo "Slug: {$organization->slug}<br>";

// Check if organization slug matches URL
echo "<h2>URL Matching Check</h2>";
echo "Expected organization slug: asee-doo<br>";
echo "Actual organization slug: {$organization->slug}<br>";
echo "Match: " . ($organization->slug === 'asee-doo' ? '✅ YES' : '❌ NO') . "<br><br>";

echo "Expected competition slug: joola-kup-2025-juniorke-1761344299<br>";
echo "Actual competition slug: {$competition->slug}<br>";
echo "Match: " . ($competition->slug === 'joola-kup-2025-juniorke-1761344299' ? '✅ YES' : '❌ NO') . "<br><br>";

// Check if match belongs to competition
echo "<h2>Match-Competition Relationship Check</h2>";
echo "Match competition_id: {$match->competition_id} (type: " . gettype($match->competition_id) . ")<br>";
echo "Competition id: {$competition->id} (type: " . gettype($competition->id) . ")<br>";
echo "Strict comparison (===): " . ($match->competition_id === $competition->id ? '✅ TRUE' : '❌ FALSE') . "<br>";
echo "Loose comparison (==): " . ($match->competition_id == $competition->id ? '✅ TRUE' : '❌ FALSE') . "<br>";

// Get user for auth check
$user = Auth::user();
if (!$user) {
    $user = User::find(1);
    echo "<p style='color: orange;'>⚠️ Not logged in, using User ID=1 for testing</p>";
}

// Test authorization
echo "<h2>Authorization Check</h2>";
echo "User ID: {$user->id}<br>";
echo "Organization Owner ID: {$organization->user_id}<br>";
echo "Is Owner: " . ($organization->user_id === $user->id ? '✅ YES' : '❌ NO') . "<br>";

try {
    $policy = new \App\Policies\OrganizationPolicy();
    $canView = $policy->view($user, $organization);
    echo "Policy->view(organization): " . ($canView ? '✅ ALLOWED' : '❌ DENIED') . "<br>";
} catch (\Exception $e) {
    echo "<p style='color: red;'>Policy Error: {$e->getMessage()}</p>";
}

// Test route matching
echo "<h2>Route Information</h2>";
$matchUrl = "https://teamsphere.infinitycreative.agency/organizations/{$organization->slug}/competitions/{$competition->slug}/matches/{$match->id}";
echo "Full URL: <a href='{$matchUrl}' target='_blank'>{$matchUrl}</a><br>";

// Check route definition
echo "<h2>Route Pattern Expected</h2>";
echo "organizations/{organization}/competitions/{competition}/matches/{match}<br>";
echo "This should match:<br>";
echo "organizations/" . htmlspecialchars($organization->slug) . "/competitions/" . htmlspecialchars($competition->slug) . "/matches/" . $match->id . "<br>";

echo "<hr>";
echo "<h2>Potential Issues:</h2>";
echo "<ul>";

// Check if route model binding is working
if ($organization->getRouteKeyName() !== 'slug') {
    echo "<li style='color: red;'>⚠️ Organization route key is not 'slug', it's: " . $organization->getRouteKeyName() . "</li>";
}

if ($competition->getRouteKeyName() !== 'slug') {
    echo "<li style='color: red;'>⚠️ Competition route key is not 'slug', it's: " . $competition->getRouteKeyName() . "</li>";
}

// Check CompetitionMatch model for route key
$matchRouteKey = (new CompetitionMatch())->getRouteKeyName();
if ($matchRouteKey !== 'id') {
    echo "<li style='color: red;'>⚠️ Match route key is not 'id', it's: {$matchRouteKey}</li>";
}

echo "</ul>";
