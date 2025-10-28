<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Competition;
use App\Models\TournamentGroup;

// Pronađi JOOLA turnir
$competition = Competition::where('name', 'like', '%Joola Kup 2006 godište%')->first();
if (!$competition) {
    echo 'JOOLA turnir nije pronađen' . PHP_EOL;
    exit;
}

echo 'Pronađen turnir: ' . $competition->name . PHP_EOL;

$groups = $competition->tournamentGroups;
echo 'Broj grupa: ' . $groups->count() . PHP_EOL;

foreach ($groups as $group) {
    echo 'Grupa ' . $group->name . ': is_completed = ' . ($group->is_completed ? 'true' : 'false') . PHP_EOL;

    $totalPlayers = count($group->player_ids ?? []);
    $requiredMatches = ($totalPlayers * ($totalPlayers - 1)) / 2;
    $completedMatches = $group->matches()->where('status', 'completed')->count();

    echo '  Igrači: ' . $totalPlayers . ', Potrebni mečevi: ' . $requiredMatches . ', Završeni mečevi: ' . $completedMatches . PHP_EOL;

    if ($completedMatches >= $requiredMatches && !$group->is_completed) {
        echo '  -> Ažuriram grupu na completed' . PHP_EOL;
        $group->update(['is_completed' => true, 'completed_at' => now()]);
    }
}

echo 'Gotovo!' . PHP_EOL;