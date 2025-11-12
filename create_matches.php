<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = \App\Models\Competition::find(38);

// Definisanje rasporeda po grupama
$schedule = [
    'A' => [
        'date' => '2025-11-12',
        'matches' => [
            ['home' => 'MZ BRČANSKA MALTA', 'away' => 'MZ BATVA', 'time' => '18:00'],
            ['home' => 'MZ GORNJA TUZLA', 'away' => 'MZ D. OBODNICA', 'time' => '18:20'],
            ['home' => 'MZ BRČANSKA MALTA', 'away' => 'MZ GORNJA TUZLA', 'time' => '18:40'],
            ['home' => 'MZ BATVA', 'away' => 'MZ D. OBODNICA', 'time' => '19:00'],
            ['home' => 'MZ BRČANSKA MALTA', 'away' => 'MZ D. OBODNICA', 'time' => '19:20'],
            ['home' => 'MZ BATVA', 'away' => 'MZ GORNJA TUZLA', 'time' => '19:40']
        ]
    ],
    'B' => [
        'date' => '2025-11-13',
        'matches' => [
            ['home' => 'MZ KREKA', 'away' => 'MZ ŠIĆKI BROD', 'time' => '18:00'],
            ['home' => 'MZ DOBRNJA', 'away' => 'MZ SJENJAK', 'time' => '18:20'],
            ['home' => 'MZ KREKA', 'away' => 'MZ DOBRNJA', 'time' => '18:40'],
            ['home' => 'MZ ŠIĆKI BROD', 'away' => 'MZ SJENJAK', 'time' => '19:00'],
            ['home' => 'MZ KREKA', 'away' => 'MZ SJENJAK', 'time' => '19:20'],
            ['home' => 'MZ ŠIĆKI BROD', 'away' => 'MZ DOBRNJA', 'time' => '19:40']
        ]
    ],
    'C' => [
        'date' => '2025-11-17',
        'matches' => [
            ['home' => 'MZ KISELJAK', 'away' => 'MZ PASCI', 'time' => '18:00'],
            ['home' => 'MZ SIMIN HAN', 'away' => 'MZ SOLINA', 'time' => '18:20'],
            ['home' => 'MZ KISELJAK', 'away' => 'MZ SIMIN HAN', 'time' => '18:40'],
            ['home' => 'MZ PASCI', 'away' => 'MZ SOLINA', 'time' => '19:00'],
            ['home' => 'MZ KISELJAK', 'away' => 'MZ SOLINA', 'time' => '19:20'],
            ['home' => 'MZ PASCI', 'away' => 'MZ SIMIN HAN', 'time' => '19:40']
        ]
    ],
    'D' => [
        'date' => '2025-11-20',
        'matches' => [
            ['home' => 'MZ MOSNIK', 'away' => 'MZ ŠI SELO', 'time' => '18:00'],
            ['home' => 'MZ MRAMOR', 'away' => 'MZ SOLANA', 'time' => '18:20'],
            ['home' => 'MZ MOSNIK', 'away' => 'MZ MRAMOR', 'time' => '18:40'],
            ['home' => 'MZ ŠI SELO', 'away' => 'MZ SOLANA', 'time' => '19:00'],
            ['home' => 'MZ MOSNIK', 'away' => 'MZ SOLANA', 'time' => '19:20'],
            ['home' => 'MZ ŠI SELO', 'away' => 'MZ MRAMOR', 'time' => '19:40']
        ]
    ]
];

$totalMatches = 0;
foreach($schedule as $groupName => $groupData) {
    $group = \App\Models\TournamentGroup::where('competition_id', $competition->id)
        ->where('name', $groupName)
        ->first();

    if(!$group) {
        echo "Group {$groupName} not found!\n";
        continue;
    }

    echo "Creating matches for Group {$groupName} on {$groupData['date']}:\n";

    foreach($groupData['matches'] as $matchData) {
        $homeTeam = \App\Models\FutsalTeam::where('competition_id', $competition->id)
            ->where('name', $matchData['home'])
            ->first();
        $awayTeam = \App\Models\FutsalTeam::where('competition_id', $competition->id)
            ->where('name', $matchData['away'])
            ->first();

        if(!$homeTeam || !$awayTeam) {
            echo "  ERROR: Team not found - Home: {$matchData['home']}, Away: {$matchData['away']}\n";
            continue;
        }

        $matchDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $groupData['date'] . ' ' . $matchData['time']);

        \App\Models\FutsalMatch::create([
            'competition_id' => $competition->id,
            'tournament_group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'scheduled_at' => $matchDateTime,
            'status' => 'scheduled',
            'match_type' => 'group',
            'round' => 1,
            'venue' => 'Sportska dvorana',
            'duration_minutes' => 20
        ]);

        echo "  Created: {$matchData['home']} vs {$matchData['away']} at {$matchData['time']}\n";
        $totalMatches++;
    }
    echo "\n";
}

echo "Total matches created: {$totalMatches}\n";
echo "Tournament setup completed!\n";