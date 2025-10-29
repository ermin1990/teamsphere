<?php
// Test match access after authorization fix
require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\CompetitionMatch;
use App\Models\Organization;
use App\Models\Competition;
use Illuminate\Support\Facades\Auth;

// Simulate user login (replace with actual user ID)
$userId = 1; // Replace with actual user ID
Auth::loginUsingId($userId);

echo "=== TESTING MATCH ACCESS ===\n";
echo "User ID: " . Auth::id() . "\n\n";

// Find a competition with matches
$competition = Competition::with(['matches', 'organization'])->first();

if (!$competition) {
    echo "No competitions found!\n";
    exit;
}

echo "Testing competition: {$competition->name} (ID: {$competition->id})\n";
echo "Organization: {$competition->organization->name} (ID: {$competition->organization->id})\n\n";

// Test policy authorization
$organization = $competition->organization;
$canViewOrg = Auth::user()->can('view', $organization);
echo "Can view organization: " . ($canViewOrg ? 'YES' : 'NO') . "\n";

if (!$canViewOrg) {
    echo "User cannot access this organization!\n";
    exit;
}

// Test match access
$matches = $competition->matches()->limit(3)->get();

foreach ($matches as $match) {
    echo "\n--- Testing Match ID: {$match->id} ---\n";

    try {
        // Test the authorization logic that would be used in the controller
        $user = Auth::user();

        // Owner check
        $isOwner = $organization->user_id === $user->id;
        echo "Is organization owner: " . ($isOwner ? 'YES' : 'NO') . "\n";

        // Member check
        $isMember = $organization->users()->where('users.id', $user->id)->exists();
        echo "Is organization member: " . ($isMember ? 'YES' : 'NO') . "\n";

        $canAccess = $isOwner || $isMember;
        echo "Can access match: " . ($canAccess ? 'YES' : 'NO') . "\n";

        if ($canAccess) {
            echo "✅ SUCCESS: Match should be accessible\n";
        } else {
            echo "❌ FAILED: Match should not be accessible\n";
        }

    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";