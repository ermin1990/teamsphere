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

class League3ALigaSeason202602Seeder extends Seeder
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

        // 3. Kreiraj ligu 3A
        $league = League::firstOrCreate(
            ['organization_id' => $organization->id, 'name' => '3A. Liga'],
            [
                'organization_id' => $organization->id,
                'name' => '3A. Liga',
                'slug' => '3a-liga-' . Str::random(6),
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
        $playerData = [
            ['name' => 'Danijel Stjepanović', 'short' => 'D. Stjepanović'],
            ['name' => 'Slaven Juric', 'short' => 'S. Juric'],
            ['name' => 'Zlatan Husic', 'short' => 'Z. Husic'],
            ['name' => 'Mirza Smajlovic', 'short' => 'M. Smajlovic'],
            ['name' => 'Muhamed Suljagic', 'short' => 'M. Suljagic'],
            ['name' => 'Adnan Hadžić', 'short' => 'A. Hadžić'],
            ['name' => 'Amir Agić', 'short' => 'A. Agić'],
            ['name' => 'Nedret Kikanović', 'short' => 'N. Kikanović'],
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
        //    Prazan rezultat = nije odigrano (scheduled). Nema WO mečeva u ovoj ligi.
        $rawMatches = [
            ['M. Suljagic', 'Z. Husic', [[4, 6], [6, 1], [0, 10]], 'away', false],
            ['M. Suljagic', 'M. Smajlovic', [[6, 3], [6, 3]], 'home', false],
            ['M. Suljagic', 'A. Agić', null, null, false],
            ['M. Suljagic', 'N. Kikanović', null, null, false],
            ['Z. Husic', 'M. Smajlovic', [[1, 6], [6, 3], [10, 5]], 'home', false],
            ['Z. Husic', 'A. Agić', null, null, false],
            ['Z. Husic', 'N. Kikanović', [[6, 1], [6, 3]], 'home', false],
            ['Z. Husic', 'S. Juric', [[3, 6], [3, 6]], 'away', false],
            ['M. Smajlovic', 'A. Agić', null, null, false],
            ['M. Smajlovic', 'N. Kikanović', null, null, false],
            ['M. Smajlovic', 'S. Juric', [[4, 6], [1, 6]], 'away', false],
            ['M. Smajlovic', 'D. Stjepanović', [[1, 6], [1, 6]], 'away', false],
            ['A. Agić', 'N. Kikanović', null, null, false],
            ['A. Agić', 'S. Juric', [[3, 6], [6, 4], [8, 10]], 'away', false],
            ['A. Agić', 'D. Stjepanović', [[2, 6], [2, 6]], 'away', false],
            ['A. Agić', 'A. Hadžić', [[6, 3], [6, 0]], 'home', false],
            ['N. Kikanović', 'S. Juric', null, null, false],
            ['N. Kikanović', 'D. Stjepanović', null, null, false],
            ['N. Kikanović', 'A. Hadžić', null, null, false],
            ['S. Juric', 'D. Stjepanović', [[2, 6], [3, 6]], 'away', false],
            ['S. Juric', 'A. Hadžić', null, null, false],
            ['S. Juric', 'M. Suljagic', [[6, 2], [7, 5]], 'home', false],
            ['D. Stjepanović', 'A. Hadžić', [[6, 1], [6, 3]], 'home', false],
            ['D. Stjepanović', 'M. Suljagic', [[6, 1], [6, 0]], 'home', false],
            ['D. Stjepanović', 'Z. Husic', null, null, false],
            ['A. Hadžić', 'M. Suljagic', null, null, false],
            ['A. Hadžić', 'Z. Husic', [[1, 6], [6, 3], [10, 4]], 'home', false],
            ['A. Hadžić', 'M. Smajlovic', [[6, 4], [3, 6], [5, 10]], 'away', false],
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

        echo "\n✓ Liga '3A. Liga' uspješno kreirana!\n";
        echo "✓ Raspored: 7 kola (round-robin)\n";
        echo "✓ Mečevi: 28 (odigrani + neodigrani)\n";
        echo "✓ URL: /takmicenja/{$league->slug}\n";
    }
}
