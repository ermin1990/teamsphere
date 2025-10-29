<?php
// Emergency script to check and fix organization access
// Upload this to public/ folder and access via browser

echo "<h1>Organization Access Checker & Fixer</h1>";
echo "<p style='color: #666;'>This script runs with full database access, no login required.</p>";

// Load Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Organization;

// Get user ID 1 (the first registered user, likely the admin)
echo "<h2>1. Identifying User</h2>";
$user = User::find(1);
if (!$user) {
    $user = User::first();
}
if ($user) {
    echo "✅ Working with user: ID={$user->id}, Email={$user->email}<br>";
} else {
    echo "❌ No users found in database!<br>";
    exit;
}

// Check organizations
echo "<h2>2. Checking Organizations</h2>";
$organizations = Organization::all();
echo "Found {$organizations->count()} organizations:<br>";
foreach ($organizations as $org) {
    echo "- ID={$org->id}, Slug={$org->slug}, Name={$org->name}, Owner ID={$org->user_id}<br>";
}

// Check organization_user table exists
echo "<h2>3. Checking organization_user Table</h2>";
try {
    $tableExists = DB::select("SHOW TABLES LIKE 'organization_user'");
    if (empty($tableExists)) {
        echo "❌ Table 'organization_user' does NOT exist!<br>";
        echo "<strong style='color: red;'>This is the problem! Creating table now...</strong><br>";
        
        // Create the table
        DB::statement("
            CREATE TABLE organization_user (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                organization_id BIGINT UNSIGNED NOT NULL,
                role VARCHAR(50) DEFAULT 'member',
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY unique_user_org (user_id, organization_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
            )
        ");
        
        echo "✅ Table created successfully!<br>";
    } else {
        echo "✅ Table 'organization_user' exists<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking table: " . $e->getMessage() . "<br>";
}

// Check current memberships
echo "<h2>4. Checking Current Memberships</h2>";
try {
    $memberships = DB::table('organization_user')->get();
    echo "Found {$memberships->count()} membership records:<br>";
    foreach ($memberships as $m) {
        echo "- User ID={$m->user_id}, Org ID={$m->organization_id}, Role={$m->role}<br>";
    }
    
    if ($memberships->count() === 0) {
        echo "<strong style='color: orange;'>⚠️ No memberships found! This is likely the problem.</strong><br>";
    }
} catch (Exception $e) {
    echo "❌ Error reading memberships: " . $e->getMessage() . "<br>";
}

// Check if current user has access to any organization
echo "<h2>5. Checking Your Organization Access</h2>";
$userMemberships = DB::table('organization_user')
    ->where('user_id', $user->id)
    ->get();

echo "You have {$userMemberships->count()} organization memberships:<br>";
foreach ($userMemberships as $m) {
    $org = Organization::find($m->organization_id);
    echo "- Organization: {$org->name} (ID={$org->id}), Role: {$m->role}<br>";
}

if ($userMemberships->count() === 0) {
    echo "<strong style='color: red;'>❌ You are not a member of ANY organization!</strong><br>";
}

// Fix section
echo "<h2>6. Auto-Fix (Add you to all organizations as admin)</h2>";

if (isset($_GET['fix']) && $_GET['fix'] === 'yes') {
    echo "<strong>Running fix...</strong><br>";
    
    foreach ($organizations as $org) {
        // Check if already exists
        $exists = DB::table('organization_user')
            ->where('user_id', $user->id)
            ->where('organization_id', $org->id)
            ->exists();
        
        if (!$exists) {
            DB::table('organization_user')->insert([
                'user_id' => $user->id,
                'organization_id' => $org->id,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✅ Added you to organization: {$org->name}<br>";
        } else {
            echo "ℹ️ Already member of: {$org->name}<br>";
        }
    }
    
    echo "<br><strong style='color: green;'>✅ Fix completed!</strong><br>";
    echo "<a href='/organizations/asee-doo'>Test access to ASEE d.o.o.</a><br>";
    echo "<a href='check_and_fix_access.php'>Refresh this page</a><br>";
} else {
    echo "<a href='check_and_fix_access.php?fix=yes' style='background: green; color: white; padding: 10px 20px; text-decoration: none; display: inline-block;'>
        🔧 Click Here to Fix (Add me to all organizations)
    </a><br>";
}

echo "<hr>";
echo "<h2>7. Organization Model Relationship Check</h2>";
try {
    $testOrg = Organization::first();
    if ($testOrg) {
        echo "Testing organization: {$testOrg->name}<br>";
        echo "Owner ID: {$testOrg->user_id}<br>";
        
        // Check if organizationUsers relationship exists
        $orgUsers = $testOrg->organizationUsers()->get();
        echo "Members via organizationUsers() relationship: {$orgUsers->count()}<br>";
        foreach ($orgUsers as $ou) {
            echo "- User ID: {$ou->id}, Email: {$ou->email}<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error testing relationship: " . $e->getMessage() . "<br>";
    echo "This might indicate the organizationUsers() relationship is not defined in Organization model.<br>";
}
