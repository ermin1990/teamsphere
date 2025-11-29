<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Organization;
use App\Models\Competition;
use App\Models\TournamentGroup;
use App\Models\Player;
use App\Models\CompetitionMatch;
use App\Models\Standing;

// Pronađi admina ili prvog usera
$admin = User::where('email', 'admin@teamsphere.com')->first() ?? User::first();

if (!$admin) {
    echo "Nema usera u bazi!\n";
    exit;
}

// Pronađi ili kreiraj organizaciju
$organization = Organization::where('user_id', $admin->id)->first();
if (!$organization) {
    $organization = Organization::create([
        'user_id' => $admin->id,
        'name' => 'Test Organizacija',
        'slug' => 'test-org-' . time(),
        'description' => 'Test organizacija za provjeru sortiranja'
    ]);
}

echo "Kreiran test tournament...\n";

// Kreiraj takmičenje
$competition = Competition::create([
    'name' => 'Test Tournament - Grupa F',
    'slug' => 'test-tournament-grupa-f-' . time(),
    'organization_id' => $organization->id,
    'sport_id' => 1, // Table Tennis
    'type' => 'tournament',
    'format' => 'group_stage',
    'scoring_system' => 'table_tennis',
    'start_date' => now(),
    'end_date' => now()->addDays(7),
    'status' => 'in_progress',
    'points_for_win' => 2,
    'points_for_draw' => 1,
    'points_for_loss' => 0,
    'best_of_sets' => 5,
]);

echo "Competition ID: {$competition->id}\n";

// Kreiraj igrače
$players = [
    'SAVKOVIĆ HOSELITO' => Player::create([
        'name' => 'SAVKOVIĆ HOSELITO',
        'organization_id' => $organization->id,
    ]),
    'HODŽIĆ ADMIR' => Player::create([
        'name' => 'HODŽIĆ ADMIR',
        'organization_id' => $organization->id,
    ]),
    'MUSTAFIĆ MUJO' => Player::create([
        'name' => 'MUSTAFIĆ MUJO',
        'organization_id' => $organization->id,
    ]),
    'BEŠLAGIĆ SALIH' => Player::create([
        'name' => 'BEŠLAGIĆ SALIH',
        'organization_id' => $organization->id,
    ]),
];

echo "Kreirani igrači\n";

// Dodaj igrače u takmičenje
$competition->players()->attach($players['HODŽIĆ ADMIR']->id);
$competition->players()->attach($players['SAVKOVIĆ HOSELITO']->id);
$competition->players()->attach($players['MUSTAFIĆ MUJO']->id);
$competition->players()->attach($players['BEŠLAGIĆ SALIH']->id);

// Kreiraj grupu F sa player_ids
$group = TournamentGroup::create([
    'competition_id' => $competition->id,
    'name' => 'F',
    'group_number' => 6,
    'total_rounds' => 3,
    'player_ids' => [
        $players['HODŽIĆ ADMIR']->id,
        $players['SAVKOVIĆ HOSELITO']->id,
        $players['MUSTAFIĆ MUJO']->id,
        $players['BEŠLAGIĆ SALIH']->id,
    ],
]);

echo "Kreirana grupa F\n";

echo "Igrači dodati u takmičenje\n";

// Kreiraj standings
foreach ($players as $name => $player) {
    Standing::create([
        'competition_id' => $competition->id,
        'tournament_group_id' => $group->id,
        'player_id' => $player->id,
        'position' => 0,
        'played' => 0,
        'won' => 0,
        'lost' => 0,
        'draw' => 0,
        'points' => 0,
        'sets_won' => 0,
        'sets_lost' => 0,
        'points_won' => 0,
        'points_lost' => 0,
    ]);
}

echo "Standings kreirani\n";

// Kolo 1 - Meč 1: HODŽIĆ ADMIR (1) 3-0 BEŠLAGIĆ SALIH (4)
$match1 = CompetitionMatch::create([
    'competition_id' => $competition->id,
    'tournament_group_id' => $group->id,
    'home_player_id' => $players['HODŽIĆ ADMIR']->id,
    'away_player_id' => $players['BEŠLAGIĆ SALIH']->id,
    'round_number' => 1,
    'status' => 'completed',
    'home_score' => 3,
    'away_score' => 0,
    'winner_id' => $players['HODŽIĆ ADMIR']->id,
    'sets' => [
        ['home_score' => 11, 'away_score' => 6],
        ['home_score' => 11, 'away_score' => 3],
        ['home_score' => 11, 'away_score' => 6],
    ],
]);

// Kolo 1 - Meč 2: SAVKOVIĆ HOSELITO (2) 3-2 MUSTAFIĆ MUJO (3)
$match2 = CompetitionMatch::create([
    'competition_id' => $competition->id,
    'tournament_group_id' => $group->id,
    'home_player_id' => $players['SAVKOVIĆ HOSELITO']->id,
    'away_player_id' => $players['MUSTAFIĆ MUJO']->id,
    'round_number' => 1,
    'status' => 'completed',
    'home_score' => 3,
    'away_score' => 2,
    'winner_id' => $players['SAVKOVIĆ HOSELITO']->id,
    'sets' => [
        ['home_score' => 7, 'away_score' => 11],
        ['home_score' => 11, 'away_score' => 4],
        ['home_score' => 9, 'away_score' => 11],
        ['home_score' => 13, 'away_score' => 11],
        ['home_score' => 11, 'away_score' => 2],
    ],
]);

// Kolo 2 - Meč 1: HODŽIĆ ADMIR (1) 2-3 MUSTAFIĆ MUJO (3)
$match3 = CompetitionMatch::create([
    'competition_id' => $competition->id,
    'tournament_group_id' => $group->id,
    'home_player_id' => $players['HODŽIĆ ADMIR']->id,
    'away_player_id' => $players['MUSTAFIĆ MUJO']->id,
    'round_number' => 2,
    'status' => 'completed',
    'home_score' => 2,
    'away_score' => 3,
    'winner_id' => $players['MUSTAFIĆ MUJO']->id,
    'sets' => [
        ['home_score' => 1, 'away_score' => 11],
        ['home_score' => 11, 'away_score' => 5],
        ['home_score' => 4, 'away_score' => 11],
        ['home_score' => 11, 'away_score' => 7],
        ['home_score' => 7, 'away_score' => 11],
    ],
]);

// Kolo 2 - Meč 2: BEŠLAGIĆ SALIH (4) 0-3 SAVKOVIĆ HOSELITO (2)
$match4 = CompetitionMatch::create([
    'competition_id' => $competition->id,
    'tournament_group_id' => $group->id,
    'home_player_id' => $players['BEŠLAGIĆ SALIH']->id,
    'away_player_id' => $players['SAVKOVIĆ HOSELITO']->id,
    'round_number' => 2,
    'status' => 'completed',
    'home_score' => 0,
    'away_score' => 3,
    'winner_id' => $players['SAVKOVIĆ HOSELITO']->id,
    'sets' => [
        ['home_score' => 7, 'away_score' => 11],
        ['home_score' => 4, 'away_score' => 11],
        ['home_score' => 3, 'away_score' => 11],
    ],
]);

// Kolo 3 - Meč 1: HODŽIĆ ADMIR (1) 3-2 SAVKOVIĆ HOSELITO (2)
$match5 = CompetitionMatch::create([
    'competition_id' => $competition->id,
    'tournament_group_id' => $group->id,
    'home_player_id' => $players['HODŽIĆ ADMIR']->id,
    'away_player_id' => $players['SAVKOVIĆ HOSELITO']->id,
    'round_number' => 3,
    'status' => 'completed',
    'home_score' => 3,
    'away_score' => 2,
    'winner_id' => $players['HODŽIĆ ADMIR']->id,
    'sets' => [
        ['home_score' => 1, 'away_score' => 11],
        ['home_score' => 11, 'away_score' => 8],
        ['home_score' => 9, 'away_score' => 11],
        ['home_score' => 11, 'away_score' => 5],
        ['home_score' => 13, 'away_score' => 11],
    ],
]);

// Kolo 3 - Meč 2: MUSTAFIĆ MUJO (3) 3-0 BEŠLAGIĆ SALIH (4)
$match6 = CompetitionMatch::create([
    'competition_id' => $competition->id,
    'tournament_group_id' => $group->id,
    'home_player_id' => $players['MUSTAFIĆ MUJO']->id,
    'away_player_id' => $players['BEŠLAGIĆ SALIH']->id,
    'round_number' => 3,
    'status' => 'completed',
    'home_score' => 3,
    'away_score' => 0,
    'winner_id' => $players['MUSTAFIĆ MUJO']->id,
    'sets' => [
        ['home_score' => 11, 'away_score' => 0],
        ['home_score' => 11, 'away_score' => 0],
        ['home_score' => 11, 'away_score' => 0],
    ],
]);

echo "Mečevi kreirani\n";

// Sada pozovi recalculate standings
app(\App\Services\TournamentGroupService::class)->recalculateGroupStandings($group);

echo "\nStandings preračunati!\n";
echo "Competition ID: {$competition->id}\n";
echo "URL: " . url("/competitions/{$competition->id}") . "\n";

// Prikaži rezultate
$standings = Standing::where('competition_id', $competition->id)
    ->where('tournament_group_id', $group->id)
    ->orderByDesc('points')
    ->orderByRaw('(sets_won - sets_lost) DESC')
    ->orderByRaw('(points_won - points_lost) DESC')
    ->orderByDesc('points_won')
    ->orderByDesc('sets_won')
    ->orderByDesc('won')
    ->orderBy('id')
    ->with('player')
    ->get();

echo "\nRezultati:\n";
echo str_pad('#', 5) . str_pad('Igrač', 25) . str_pad('M', 5) . str_pad('P', 5) . str_pad('I', 5) . str_pad('S', 10) . str_pad('G', 10) . "Bod\n";
echo str_repeat('-', 75) . "\n";

foreach ($standings as $index => $standing) {
    $setDiff = $standing->sets_won - $standing->sets_lost;
    $gemDiff = $standing->points_won - $standing->points_lost;
    
    echo str_pad($index + 1, 5) 
        . str_pad($standing->player->name, 25) 
        . str_pad($standing->played, 5) 
        . str_pad($standing->won, 5) 
        . str_pad($standing->lost, 5) 
        . str_pad($standing->sets_won . '-' . $standing->sets_lost . ' (' . ($setDiff >= 0 ? '+' : '') . $setDiff . ')', 10)
        . str_pad($standing->points_won . '-' . $standing->points_lost . ' (' . ($gemDiff >= 0 ? '+' : '') . $gemDiff . ')', 10)
        . $standing->points . "\n";
}

echo "\nTest takmičenje kreirano!\n";
