<?php

use App\Models\Competition;
use App\Models\Team;
use App\Models\Player;
use App\Models\Standing;
use App\Models\TeamMatch;
use App\Models\CompetitionMatch;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$competitionId = 4;
$organizationId = 4; // Force organization ID to 4
$competition = Competition::find($competitionId);

if (!$competition) {
    die("Competition not found");
}

$sql = "-- SQL Export for Competition: {$competition->name}\n";
$sql .= "-- Organization ID: {$organizationId}\n\n";

// Disable foreign key checks
$sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

// Helper function to escape values and wrap in quotes
function escape($v) {
    if (is_null($v)) return 'NULL';
    // Use PDO to quote the string properly for the database
    return DB::getPdo()->quote($v);
}

// Helper function to wrap column names in backticks
function wrap($k) {
    return "`$k`";
}

// Helper function to normalize player names
function normalizePlayerName($name, $id) {
    if ($id <= 66) {
        $parts = explode(' ', trim($name));
        if (count($parts) == 2) {
            $newName = $parts[1] . ' ' . $parts[0];
            // Fix specific typo if found
            if ($newName === 'Kamera Maglić') return 'Amer Maglić';
            return $newName;
        }
    }
    return $name;
}

// 0. Competition
$sql .= "-- Competition\n";
$compAttrs = $competition->getAttributes();
$compAttrs['organization_id'] = $organizationId;
$columns = array_map('wrap', array_keys($compAttrs));
$values = array_map('escape', array_values($compAttrs));
$updateParts = [];
foreach (array_keys($compAttrs) as $key) {
    $updateParts[] = "`$key` = VALUES(`$key`)";
}
$sql .= "INSERT INTO competitions (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $updateParts) . ";\n\n";

// 1. Teams
$teams = Team::where('competition_id', $competitionId)->get();
if ($teams->count() > 0) {
    $sql .= "-- Teams\n";
    foreach ($teams as $team) {
        $attrs = $team->getAttributes();
        $attrs['organization_id'] = $organizationId;
        $columns = array_map('wrap', array_keys($attrs));
        $values = array_map('escape', array_values($attrs));
        $updateParts = [];
        foreach (array_keys($attrs) as $key) {
            $updateParts[] = "`$key` = VALUES(`$key`)";
        }
        $sql .= "INSERT INTO teams (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $updateParts) . ";\n";
    }
    $sql .= "\n";
}

// 2. Players (Get all players involved in these teams or the competition)
$teamIds = $teams->pluck('id')->toArray();
$playerIdsFromTeams = DB::table('team_player')->whereIn('team_id', $teamIds)->pluck('player_id')->toArray();
$playerIdsFromComp = DB::table('competition_player')->where('competition_id', $competitionId)->pluck('player_id')->toArray();
$allPlayerIds = array_unique(array_merge($playerIdsFromTeams, $playerIdsFromComp));

if (count($allPlayerIds) > 0) {
    $players = Player::whereIn('id', $allPlayerIds)->get();
    $sql .= "-- Players\n";
    foreach ($players as $player) {
        $attrs = $player->getAttributes();
        $attrs['organization_id'] = $organizationId;
        $attrs['name'] = normalizePlayerName($attrs['name'], $player->id);
        $columns = array_map('wrap', array_keys($attrs));
        $values = array_map('escape', array_values($attrs));
        $updateParts = [];
        foreach (array_keys($attrs) as $key) {
            $updateParts[] = "`$key` = VALUES(`$key`)";
        }
        $sql .= "INSERT INTO players (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $updateParts) . ";\n";
    }
    $sql .= "\n";
}

// 3. team_player
$teamPlayers = DB::table('team_player')->whereIn('team_id', $teamIds)->get();
if ($teamPlayers->count() > 0) {
    $sql .= "-- Team Player Relationships\n";
    foreach ($teamPlayers as $tp) {
        $tpArray = (array)$tp;
        $columns = array_map('wrap', array_keys($tpArray));
        $values = array_map('escape', array_values($tpArray));
        $updateParts = [];
        foreach (array_keys($tpArray) as $key) {
            $updateParts[] = "`$key` = VALUES(`$key`)";
        }
        $sql .= "INSERT INTO team_player (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $updateParts) . ";\n";
    }
    $sql .= "\n";
}

// 4. competition_player
$compPlayers = DB::table('competition_player')->where('competition_id', $competitionId)->get();
if ($compPlayers->count() > 0) {
    $sql .= "-- Competition Player Relationships\n";
    foreach ($compPlayers as $cp) {
        $cpArray = (array)$cp;
        $columns = array_map('wrap', array_keys($cpArray));
        $values = array_map('escape', array_values($cpArray));
        $updateParts = [];
        foreach (array_keys($cpArray) as $key) {
            $updateParts[] = "`$key` = VALUES(`$key`)";
        }
        $sql .= "INSERT INTO competition_player (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $updateParts) . ";\n";
    }
    $sql .= "\n";
}

// 5. Standings
$standings = Standing::where('competition_id', $competitionId)->get();
if ($standings->count() > 0) {
    $sql .= "-- Standings\n";
    foreach ($standings as $standing) {
        $attrs = $standing->getAttributes();
        $columns = array_map('wrap', array_keys($attrs));
        $values = array_map('escape', array_values($attrs));
        $updateParts = [];
        foreach (array_keys($attrs) as $key) {
            $updateParts[] = "`$key` = VALUES(`$key`)";
        }
        $sql .= "INSERT INTO standings (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $updateParts) . ";\n";
    }
    $sql .= "\n";
}

// 6. Team Matches
$teamMatches = TeamMatch::where('competition_id', $competitionId)->get();
if ($teamMatches->count() > 0) {
    $sql .= "-- Team Matches\n";
    foreach ($teamMatches as $tm) {
        $attrs = $tm->getAttributes();
        $columns = array_map('wrap', array_keys($attrs));
        $values = array_map('escape', array_values($attrs));
        $updateParts = [];
        foreach (array_keys($attrs) as $key) {
            $updateParts[] = "`$key` = VALUES(`$key`)";
        }
        $sql .= "INSERT INTO team_matches (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $updateParts) . ";\n";
    }
    $sql .= "\n";
}

// 7. Individual Matches
$matches = CompetitionMatch::where('competition_id', $competitionId)->get();
if ($matches->count() > 0) {
    $sql .= "-- Individual Matches\n";
    foreach ($matches as $match) {
        $attrs = $match->getAttributes();
        $columns = array_map('wrap', array_keys($attrs));
        $values = array_map('escape', array_values($attrs));
        $updateParts = [];
        foreach (array_keys($attrs) as $key) {
            $updateParts[] = "`$key` = VALUES(`$key`)";
        }
        $sql .= "INSERT INTO matches (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $updateParts) . ";\n";
    }
    $sql .= "\n";
}

// 8. Final Fixes for Organization ID (in case IDs didn't match but names do)
$sql .= "-- Final Fixes for Organization ID and Names\n";
$sql .= "UPDATE teams SET organization_id = {$organizationId} WHERE competition_id = {$competitionId};\n";
$sql .= "UPDATE players SET organization_id = {$organizationId} WHERE id IN (SELECT player_id FROM team_player WHERE team_id IN (SELECT id FROM teams WHERE competition_id = {$competitionId}));\n\n";

$sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

file_put_contents('full_competition_export.sql', $sql);
echo "Export completed: full_competition_export.sql\n";
