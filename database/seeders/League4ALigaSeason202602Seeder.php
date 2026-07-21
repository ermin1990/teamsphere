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

class League4ALigaSeason202602Seeder extends Seeder
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

        // 3. Kreiraj ligu 4A (10 igrača, ne 8 kao ostale lige)
        $league = League::firstOrCreate(
            ['organization_id' => $organization->id, 'name' => '4A. Liga'],
            [
                'organization_id' => $organization->id,
                'name' => '4A. Liga',
                'slug' => '4a-liga-' . Str::random(6),
                'sport_id' => $sport->id,
                'type' => 'league',
                'season_id' => $season->id,
                'is_team_based' => false,
                'status' => 'active',
                'max_teams' => 10,
                'is_active' => true,
                'is_public' => true,
            ]
        );

        // 4. Igrači (10 ukupno)
        $playerData = [
            ['name' => 'Džemal Hamzić', 'short' => 'D. Hamzić'],
            ['name' => 'Edin Mehanovic', 'short' => 'E. Mehanovic'],
            ['name' => 'Danijel Nikolić', 'short' => 'D. Nikolić'],
            ['name' => 'Redzo Dzakmic', 'short' => 'R. Dzakmic'],
            ['name' => 'Maid Sarvan', 'short' => 'M. Sarvan'],
            ['name' => 'Mirza Bajrić', 'short' => 'M. Bajrić'],
            ['name' => 'Edis Husanović', 'short' => 'E. Husanović'],
            ['name' => 'Adnan Mujezinovic', 'short' => 'A. Mujezinovic'],
            ['name' => 'Edin Sarajlic', 'short' => 'E. Sarajlic'],
            ['name' => 'Mirnes Smajlovic', 'short' => 'M. Smajlovic'],
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

        // 5. Autoritativna lista mečeva (tačno kako ju je korisnik dao, 45
        //    parova za 10 igrača). Prazan rezultat = nije odigrano
        //    (scheduled). Nema WO mečeva u ovoj ligi - samo 9 od 45 je
        //    odigrano.
        //    [home, away, sets (iz home perspektive) | null, winner ('home'|'away'|null), wo (bool)]
        $rawMatches = [
            ['M. Sarvan', 'M. Smajlovic', null, null, false],
            ['M. Sarvan', 'R. Dzakmic', [[7, 5], [6, 7], [8, 10]], 'away', false],
            ['M. Sarvan', 'E. Sarajlic', null, null, false],
            ['M. Sarvan', 'D. Nikolić', [[2, 6], [0, 6]], 'away', false],
            ['M. Sarvan', 'D. Hamzić', [[0, 6], [3, 6]], 'away', false],
            ['M. Smajlovic', 'R. Dzakmic', null, null, false],
            ['M. Smajlovic', 'E. Sarajlic', null, null, false],
            ['M. Smajlovic', 'D. Nikolić', null, null, false],
            ['M. Smajlovic', 'D. Hamzić', null, null, false],
            ['M. Smajlovic', 'M. Bajrić', null, null, false],
            ['R. Dzakmic', 'E. Sarajlic', null, null, false],
            ['R. Dzakmic', 'D. Nikolić', [[6, 4], [2, 6], [7, 10]], 'away', false],
            ['R. Dzakmic', 'D. Hamzić', [[4, 6], [5, 7]], 'away', false],
            ['R. Dzakmic', 'M. Bajrić', null, null, false],
            ['R. Dzakmic', 'A. Mujezinovic', null, null, false],
            ['E. Sarajlic', 'D. Nikolić', null, null, false],
            ['E. Sarajlic', 'D. Hamzić', null, null, false],
            ['E. Sarajlic', 'M. Bajrić', null, null, false],
            ['E. Sarajlic', 'A. Mujezinovic', null, null, false],
            ['E. Sarajlic', 'E. Mehanovic', null, null, false],
            ['D. Nikolić', 'D. Hamzić', null, null, false],
            ['D. Nikolić', 'M. Bajrić', null, null, false],
            ['D. Nikolić', 'A. Mujezinovic', null, null, false],
            ['D. Nikolić', 'E. Mehanovic', [[2, 6], [4, 6]], 'away', false],
            ['D. Nikolić', 'E. Husanović', null, null, false],
            ['D. Hamzić', 'M. Bajrić', [[6, 3], [6, 4]], 'home', false],
            ['D. Hamzić', 'A. Mujezinovic', null, null, false],
            ['D. Hamzić', 'E. Mehanovic', [[6, 7], [1, 6]], 'away', false],
            ['D. Hamzić', 'E. Husanović', null, null, false],
            ['M. Bajrić', 'A. Mujezinovic', null, null, false],
            ['M. Bajrić', 'E. Mehanovic', null, null, false],
            ['M. Bajrić', 'E. Husanović', null, null, false],
            ['M. Bajrić', 'M. Sarvan', null, null, false],
            ['A. Mujezinovic', 'E. Mehanovic', null, null, false],
            ['A. Mujezinovic', 'E. Husanović', null, null, false],
            ['A. Mujezinovic', 'M. Sarvan', null, null, false],
            ['A. Mujezinovic', 'M. Smajlovic', null, null, false],
            ['E. Mehanovic', 'E. Husanović', null, null, false],
            ['E. Mehanovic', 'M. Sarvan', [[6, 2], [6, 0]], 'home', false],
            ['E. Mehanovic', 'M. Smajlovic', null, null, false],
            ['E. Mehanovic', 'R. Dzakmic', null, null, false],
            ['E. Husanović', 'M. Sarvan', null, null, false],
            ['E. Husanović', 'M. Smajlovic', null, null, false],
            ['E. Husanović', 'R. Dzakmic', null, null, false],
            ['E. Husanović', 'E. Sarajlic', null, null, false],
        ];

        // 6. Round-robin raspored (10 igrača, 9 kola) -> mapa neuređenog
        //    para na broj kola. Za neparan broj igrača dodaje se "bye" - kod
        //    10 (parno) svi igraju svako kolo.
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

        echo "\n✓ Liga '4A. Liga' uspješno kreirana!\n";
        echo "✓ Raspored: 9 kola (round-robin, 10 igrača)\n";
        echo "✓ Mečevi: 45 (9 odigranih + 36 neodigranih)\n";
        echo "✓ URL: /takmicenja/{$league->slug}\n";
    }
}
