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

class League3BLigaSeason202602Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Ista organizacija kao ostale lige (Tuzlanska liga)
        $organization = Organization::firstOrCreate(
            ['name' => 'Tuzlanska liga'],
            [
                'name' => 'Tuzlanska liga',
                'slug' => 'tuzlanska-liga-' . Str::random(6),
                'sport_id' => $sport->id,
                'user_id' => $owner->id,
                'is_active' => true,
            ]
        );

        // 2. Sezona 2026/02 (dijeli se sa ostalim ligama)
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

        // 3. Kreiraj ligu 3B
        $league = League::firstOrCreate(
            ['organization_id' => $organization->id, 'name' => '3B. Liga'],
            [
                'organization_id' => $organization->id,
                'name' => '3B. Liga',
                'slug' => '3b-liga-' . Str::random(6),
                'sport_id' => $sport->id,
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
        // NAPOMENA: postoje DVA igrača s istim skraćenim imenom u rasporedu ("M.
        // Nurkanovic" = Mirza Nurkanovic i Mahir Nurkanovic). Raspored ne
        // razlikuje koji je koji, pa je identitet svake pojedinačne "M.
        // Nurkanovic" utakmice rekonstruisan matematički iz OU/Pob/Izgub
        // brojeva u tabeli koju je korisnik dao (svaki broj se tačno poklapa
        // s ovom raspodjelom - vidi rawMatches ispod).
        $playerData = [
            ['name' => 'Alen Catic', 'short' => 'A. Catic'],
            ['name' => 'Denijal Ahmetović', 'short' => 'D. Ahmetović'],
            ['name' => 'Elvir Hodžić', 'short' => 'E. Hodžić'],
            ['name' => 'Mirza Nurkanovic', 'short' => 'Mirza N.'],
            ['name' => 'Boris Milić', 'short' => 'B. Milić'],
            ['name' => 'Mahir Nurkanovic', 'short' => 'Mahir N.'],
            ['name' => 'Danijel Stuhli', 'short' => 'D. Stuhli'],
            ['name' => 'Adis Mulabdić', 'short' => 'A. Mulabdić'],
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

        // 5. Autoritativna lista mečeva (tačno kako ju je korisnik dao, uz
        //    razrješenu "Mirza N." / "Mahir N." dvosmislenost).
        //    [home, away, sets (iz home perspektive) | null, winner ('home'|'away'|null), wo (bool)]
        //    Prazan rezultat = nije odigrano (scheduled). Nema WO mečeva u ovoj ligi.
        $rawMatches = [
            ['Mirza N.', 'A. Catic', [[2, 6], [2, 6]], 'away', false],
            ['Mirza N.', 'Mahir N.', [[6, 4], [6, 7], [10, 7]], 'home', false],
            ['Mirza N.', 'E. Hodžić', null, null, false],
            ['Mirza N.', 'D. Stuhli', [[6, 2], [6, 3]], 'home', false],
            ['A. Catic', 'Mahir N.', [[6, 3], [6, 3]], 'home', false],
            ['A. Catic', 'E. Hodžić', [[6, 3], [6, 1]], 'home', false],
            ['A. Catic', 'D. Stuhli', [[6, 4], [6, 2]], 'home', false],
            ['A. Catic', 'D. Ahmetović', [[4, 6], [2, 6]], 'away', false],
            ['Mahir N.', 'E. Hodžić', null, null, false],
            ['Mahir N.', 'D. Stuhli', null, null, false],
            ['Mahir N.', 'D. Ahmetović', [[0, 6], [0, 6]], 'away', false],
            ['Mahir N.', 'B. Milić', [[0, 6], [0, 6]], 'away', false],
            ['E. Hodžić', 'D. Stuhli', [[6, 0], [6, 3]], 'home', false],
            ['E. Hodžić', 'D. Ahmetović', null, null, false],
            ['E. Hodžić', 'B. Milić', [[6, 4], [7, 5]], 'home', false],
            ['E. Hodžić', 'A. Mulabdić', null, null, false],
            ['D. Stuhli', 'D. Ahmetović', [[1, 6], [0, 6]], 'away', false],
            ['D. Stuhli', 'B. Milić', null, null, false],
            ['D. Stuhli', 'A. Mulabdić', null, null, false],
            ['D. Ahmetović', 'B. Milić', [[6, 3], [6, 3]], 'home', false],
            ['D. Ahmetović', 'A. Mulabdić', null, null, false],
            ['D. Ahmetović', 'Mirza N.', null, null, false],
            ['B. Milić', 'A. Mulabdić', null, null, false],
            ['B. Milić', 'Mirza N.', null, null, false],
            ['B. Milić', 'A. Catic', null, null, false],
            ['A. Mulabdić', 'Mirza N.', null, null, false],
            ['A. Mulabdić', 'A. Catic', null, null, false],
            ['A. Mulabdić', 'Mahir N.', null, null, false],
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

        // 8. Tabela se gradi kroz app servis (points_for_win/loss ista kao ostale lige)
        $competition = \App\Models\Competition::find($league->id);
        $competition->update(['points_for_win' => 3, 'points_for_loss' => 1]);
        app(LeagueStandingsService::class)->rebuildForCompetition($competition);

        echo "\n✓ Liga '3B. Liga' uspješno kreirana!\n";
        echo "✓ Raspored: 7 kola (round-robin)\n";
        echo "✓ Mečevi: 28 (odigrani + neodigrani)\n";
        echo "✓ URL: /takmicenja/{$league->slug}\n";
    }
}
