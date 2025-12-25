<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$comp = \App\Models\Competition::where('slug', 'premijer-liga-muskarci-20252026-1766657518')->first();
if ($comp) {
    // Re-calculate everything to be sure
    $teamsInComp = $comp->teamMatches()->pluck('home_team_id')->merge($comp->teamMatches()->pluck('away_team_id'))->unique();

    foreach ($teamsInComp as $teamId) {
        $won = 0;
        $lost = 0;
        $sets_won = 0;
        $sets_lost = 0;
        $points = 0;

        $homeMatches = \App\Models\TeamMatch::where('competition_id', $comp->id)->where('home_team_id', $teamId)->where('status', 'completed')->get();
        $awayMatches = \App\Models\TeamMatch::where('competition_id', $comp->id)->where('away_team_id', $teamId)->where('status', 'completed')->get();

        foreach ($homeMatches as $match) {
            $sets_won += $match->home_score;
            $sets_lost += $match->away_score;
            if ($match->home_score > $match->away_score) {
                $won++;
                $points += 2;
            } else {
                $lost++;
            }
        }

        foreach ($awayMatches as $match) {
            $sets_won += $match->away_score;
            $sets_lost += $match->home_score;
            if ($match->away_score > $match->home_score) {
                $won++;
                $points += 2;
            } else {
                $lost++;
            }
        }

        \App\Models\Standing::updateOrCreate(
            ['competition_id' => $comp->id, 'team_id' => $teamId],
            [
                'won' => $won,
                'lost' => $lost,
                'sets_won' => $sets_won,
                'sets_lost' => $sets_lost,
                'points' => $points,
                'played' => $won + $lost
            ]
        );
    }

    // Update positions
    $standings = \App\Models\Standing::where('competition_id', $comp->id)
        ->orderByDesc('points')
        ->orderByRaw('(sets_won - sets_lost) DESC')
        ->get();

    foreach ($standings as $index => $standing) {
        $standing->update(['position' => $index + 1]);
    }
    echo "Standings fixed for " . $comp->name . "\n";
} else {
    echo "Competition not found\n";
}
