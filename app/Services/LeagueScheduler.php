<?php

namespace App\Services;

use App\Models\League;

class LeagueScheduler
{
    /**
     * Generiše double round robin raspored za ligu
     *
     * @param League $league
     * @return void
     */
    public static function generateRoundRobinSchedule(League $league): void
    {
        $participants = $league->is_team_based ? $league->teams()->pluck('id')->toArray() : $league->players()->pluck('id')->toArray();
        $numParticipants = count($participants);

        if ($numPlayers < 2) {
            return;
        }

        // Ako je neparan broj igrača, dodaj "dummy" (pauzu)
        $hasDummy = false;
        if ($numParticipants % 2 !== 0) {
            $participants[] = null; // null označava "Bye" kolo
            $numParticipants++;
            $hasDummy = true;
        }

        $rounds = $numParticipants - 1;
        $half = $numParticipants / 2;

        $schedule = [];

        for ($round = 0; $round < $rounds; $round++) {
            $pairings = [];

            for ($i = 0; $i < $half; $i++) {
                $home = $participants[$i];
                $away = $participants[$numParticipants - 1 - $i];

                if ($home !== null && $away !== null) {
                    $pairings[] = ['home' => $home, 'away' => $away];
                }
            }

            $schedule[] = $pairings;

            // Rotacija igrača osim prvog
            $fixed = array_shift($participants);
            $moved = array_pop($participants);
            array_unshift($participants, $fixed);
            array_splice($participants, 1, 0, [$moved]);
        }

        // Double round robin – drugi krug sa zamijenjenim domaćinom/gostom
        $doubleSchedule = array_merge(
            $schedule,
            array_map(fn($round) => array_map(fn($p) => [
                'home' => $p['away'],
                'away' => $p['home']
            ], $round), $schedule)
        );

        // Snimi sve u bazu
        foreach ($doubleSchedule as $roundIndex => $pairings) {
            foreach ($pairings as $pair) {
                $league->matches()->create([
                    'round' => $roundIndex + 1,
                    'home_team_id' => $league->is_team_based ? $pair['home'] : null,
                    'away_team_id' => $league->is_team_based ? $pair['away'] : null,
                    'home_player_id' => !$league->is_team_based ? $pair['home'] : null,
                    'away_player_id' => !$league->is_team_based ? $pair['away'] : null,
                    'status' => 'scheduled',
                ]);
            }
        }
    }
}