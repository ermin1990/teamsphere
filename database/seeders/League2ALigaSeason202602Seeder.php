<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Season;
use App\Models\League;
use App\Models\Player;
use App\Models\LeagueMatch;
use App\Services\LeagueStandingsService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class League2ALigaSeason202602Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Ista organizacija kao 1. Liga (Tuzlanska liga)
        $organization = Organization::firstOrCreate(
            ['name' => 'Tuzlanska liga'],
            [
                'name' => 'Tuzlanska liga',
                'slug' => 'tuzlanska-liga-' . Str::random(6),
                'sport_id' => 3, // Tenis
                'user_id' => $owner->id,
                'is_active' => true,
            ]
        );

        // 2. Sezona 2026/02 (dijeli se sa 1. Ligom)
        $season = Season::firstOrCreate(
            ['organization_id' => $organization->id, 'name' => '2026/02'],
            [
                'organization_id' => $organization->id,
                'name' => '2026/02',
                'starts_at' => '2026-02-01',
                'ends_at' => '2026-02-28',
                'is_active' => true,
            ]
        );

        // 3. Kreiraj ligu 2A
        $league = League::firstOrCreate(
            ['organization_id' => $organization->id, 'name' => '2A. Liga'],
            [
                'organization_id' => $organization->id,
                'name' => '2A. Liga',
                'slug' => '2a-liga-' . Str::random(6),
                'sport_id' => 3, // Tenis
                'type' => 'league',
                'season_id' => $season->id,
                'is_team_based' => false,
                'status' => 'active',
                'max_teams' => 8,
                'is_active' => true,
                'is_public' => true,
            ]
        );

        // 4. Igrači
        $playerData = [
            ['name' => 'Jasminko Hodzic', 'short' => 'J. Hodzic'],
            ['name' => 'Amer Salkic', 'short' => 'A. Salkic'],
            ['name' => 'Adnan Aljić', 'short' => 'A. Aljić'],
            ['name' => 'Edin Muharemagić', 'short' => 'E. Muharemagić'],
            ['name' => 'Haris Dautović', 'short' => 'H. Dautović'],
            ['name' => 'Dejan Muslimović', 'short' => 'D. Muslimović'],
            ['name' => 'Izudin Rizvic', 'short' => 'I. Rizvic'],
            ['name' => 'Dino Crnkić', 'short' => 'D. Crnkić'],
        ];

        $players = [];
        $duplicates = [];

        foreach ($playerData as $data) {
            $existing = Player::where('organization_id', $organization->id)
                ->where('name', $data['name'])
                ->first();

            if ($existing) {
                $duplicates[] = "{$data['name']} (ID: {$existing->id})";
                $players[$data['short']] = $existing;
            } else {
                $players[$data['short']] = Player::create([
                    'organization_id' => $organization->id,
                    'name' => $data['name'],
                    'email' => null,
                    'user_id' => null,
                    'is_active' => true,
                ]);
            }
        }

        if (!empty($duplicates)) {
            echo "\n⚠️  DUPLIRANA IMENA (korišten postojeći igrač):\n";
            foreach ($duplicates as $d) {
                echo "  - $d\n";
            }
        }

        // 4b. Dodaj igrače kao UČESNIKE takmičenja (pivot competition_player)
        $pivotData = collect($players)
            ->mapWithKeys(fn ($player) => [$player->id => ['joined_at' => now()]])
            ->all();
        $league->players()->syncWithoutDetaching($pivotData);

        echo "\n✓ Igrači učitani i dodani kao učesnici takmičenja.\n";

        // 5. Autoritativna lista mečeva (tačno kako ju je korisnik dao).
        //    [home, away, sets (iz home perspektive) | null, winner ('home'|'away'|null), wo (bool)]
        //    Ova liga nema WO mečeva; prazni rezultati = neodigrano (scheduled).
        $rawMatches = [
            ['D. Muslimović', 'I. Rizvic', [[6, 4], [4, 6], [4, 10]], 'away', false],
            ['D. Muslimović', 'A. Salkic', [[2, 6], [2, 6]], 'away', false],
            ['D. Muslimović', 'D. Crnkić', null, null, false],
            ['D. Muslimović', 'A. Aljić', [[1, 6], [6, 2], [4, 10]], 'away', false],
            ['I. Rizvic', 'A. Salkic', [[1, 6], [1, 6]], 'away', false],
            ['I. Rizvic', 'D. Crnkić', null, null, false],
            ['I. Rizvic', 'A. Aljić', [[3, 6], [1, 6]], 'away', false],
            ['I. Rizvic', 'J. Hodzic', [[1, 6], [7, 6], [7, 10]], 'away', false],
            ['A. Salkic', 'D. Crnkić', null, null, false],
            ['A. Salkic', 'A. Aljić', null, null, false],
            ['A. Salkic', 'J. Hodzic', [[3, 6], [1, 6]], 'away', false],
            ['A. Salkic', 'H. Dautović', [[6, 2], [6, 1]], 'home', false],
            ['D. Crnkić', 'A. Aljić', null, null, false],
            ['D. Crnkić', 'J. Hodzic', null, null, false],
            ['D. Crnkić', 'H. Dautović', null, null, false],
            ['D. Crnkić', 'E. Muharemagić', null, null, false],
            ['A. Aljić', 'J. Hodzic', [[6, 4], [6, 3]], 'home', false],
            ['A. Aljić', 'H. Dautović', null, null, false],
            ['A. Aljić', 'E. Muharemagić', [[5, 7], [4, 6]], 'away', false],
            ['J. Hodzic', 'H. Dautović', [[6, 3], [6, 4]], 'home', false],
            ['J. Hodzic', 'E. Muharemagić', [[7, 5], [6, 4]], 'home', false],
            ['J. Hodzic', 'D. Muslimović', [[6, 4], [6, 2]], 'home', false],
            ['H. Dautović', 'E. Muharemagić', [[6, 3], [5, 7], [7, 10]], 'away', false],
            ['H. Dautović', 'D. Muslimović', [[6, 2], [6, 1]], 'home', false],
            ['H. Dautović', 'I. Rizvic', [[6, 4], [6, 3]], 'home', false],
            ['E. Muharemagić', 'D. Muslimović', [[6, 3], [4, 6], [13, 15]], 'away', false],
            ['E. Muharemagić', 'I. Rizvic', null, null, false],
            ['E. Muharemagić', 'A. Salkic', [[2, 6], [2, 6]], 'away', false],
        ];

        // 6. Round-robin raspored -> mapa neuređenog para na broj kola
        $playerKeys = array_keys($players);
        $playerCount = count($playerKeys);
        $pairRound = [];
        $pairKey = fn ($a, $b) => implode('|', collect([$a, $b])->sort()->values()->all());

        for ($round = 1; $round < $playerCount; $round++) {
            for ($i = 0; $i < $playerCount / 2; $i++) {
                $a = $playerKeys[$i];
                $b = $playerKeys[$playerCount - 1 - $i];
                if ($a !== $b) {
                    $pairRound[$pairKey($a, $b)] = $round;
                }
            }
            $last = array_pop($playerKeys);
            array_splice($playerKeys, 1, 0, [$last]);
        }

        // 7. Kreiraj mečeve iz autoritativne liste, sa dodijeljenim kolom
        foreach ($rawMatches as [$homeShort, $awayShort, $sets, $winner, $isWo]) {
            $homePlayer = $players[$homeShort];
            $awayPlayer = $players[$awayShort];

            $homeScore = null;
            $awayScore = null;
            $forfeitedBy = null;
            $status = 'scheduled';

            if ($winner !== null) {
                $status = 'completed';

                if ($isWo) {
                    if ($winner === 'home') {
                        $homeScore = 2;
                        $awayScore = 0;
                        $forfeitedBy = 'away';
                    } else {
                        $homeScore = 0;
                        $awayScore = 2;
                        $forfeitedBy = 'home';
                    }
                    $sets = null;
                } else {
                    // Prebroj osvojene setove i pretvori u {home,away} format koji app očekuje
                    $homeScore = 0;
                    $awayScore = 0;
                    $sets = array_map(function ($set) use (&$homeScore, &$awayScore) {
                        if ($set[0] > $set[1]) {
                            $homeScore++;
                        } else {
                            $awayScore++;
                        }
                        return ['home' => $set[0], 'away' => $set[1]];
                    }, $sets);
                }
            }

            LeagueMatch::create([
                'competition_id' => $league->id,
                'home_player_id' => $homePlayer->id,
                'away_player_id' => $awayPlayer->id,
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'sets' => $sets,
                'status' => $status,
                'forfeited_by' => $forfeitedBy,
                'round' => $pairRound[$pairKey($homeShort, $awayShort)] ?? null,
            ]);
        }

        // 8. Tabela se gradi kroz app servis (ista logika kao u aplikaciji)
        $competition = \App\Models\Competition::find($league->id);
        app(LeagueStandingsService::class)->rebuildForCompetition($competition);

        echo "\n✓ Liga '2A. Liga' uspješno kreirana!\n";
        echo "✓ Raspored: 7 kola (round-robin)\n";
        echo "✓ Mečevi: 28 (odigrani + neodigrani)\n";
        echo "✓ Tabela: Izračunata kroz LeagueStandingsService\n";
        echo "✓ URL: /takmicenja/{$league->slug}\n";
    }
}
