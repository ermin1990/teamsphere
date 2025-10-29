<?php
// Debug competition access
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\Organization;
use App\Models\Competition;
use App\Models\User;

echo "<h1>Competition Access Debug</h1>";

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

echo "<h2>User Access Check</h2>";
echo "User ID: {$user->id}<br>";
echo "User ID type: " . gettype($user->id) . "<br>";
echo "Organization user_id: {$organization->user_id}<br>";
echo "Organization user_id type: " . gettype($organization->user_id) . "<br>";
echo "Strict comparison (===): " . (($organization->user_id === $user->id) ? 'TRUE ✅' : 'FALSE ❌') . "<br>";
echo "Loose comparison (==): " . (($organization->user_id == $user->id) ? 'TRUE ✅' : 'FALSE ❌') . "<br>";
echo "Is Owner: " . ($organization->user_id === $user->id ? 'YES ✅' : 'NO ❌') . "<br>";

// Check membership
$isMember = $organization->users()->where('users.id', $user->id)->exists();
echo "Is Member (via users()): " . ($isMember ? 'YES ✅' : 'NO ❌') . "<br>";

// Check organization_user table directly
$membershipCheck = \Illuminate\Support\Facades\DB::table('organization_user')
    ->where('user_id', $user->id)
    ->where('organization_id', $organization->id)
    ->first();

echo "Membership in DB: ";
if ($membershipCheck) {
    echo "YES ✅ (Role: {$membershipCheck->role})<br>";
} else {
    echo "NO ❌<br>";
}

// Test policy manually
try {
    $policy = new \App\Policies\OrganizationPolicy();
    $canView = $policy->view($user, $organization);
    echo "<br><h2>Policy Check Result</h2>";
    echo "Policy->view(): " . ($canView ? 'ALLOWED ✅' : 'DENIED ❌') . "<br>";
} catch (\Exception $e) {
    echo "<br><h2>Policy Error</h2>";
    echo "<p style='color: red;'>Error: {$e->getMessage()}</p>";
    echo "<pre>{$e->getTraceAsString()}</pre>";
}

echo "<hr>";
echo "<h2>Try accessing:</h2>";
$url = "https://teamsphere.infinitycreative.agency/organizations/{$organization->slug}/competitions/{$competition->slug}";
echo "<a href='{$url}' target='_blank'>{$url}</a>";
