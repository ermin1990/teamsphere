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

class League3DLigaSeason202602Seeder extends Seeder
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

        // 3. Kreiraj ligu 3D
        $league = League::firstOrCreate(
            ['organization_id' => $organization->id, 'name' => '3D. Liga'],
            [
                'organization_id' => $organization->id,
                'name' => '3D. Liga',
                'slug' => '3d-liga-' . Str::random(6),
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
            ['name' => 'Semir Omerovic', 'short' => 'S. Omerovic'],
            ['name' => 'Selmir Halilčević', 'short' => 'S. Halilčević'],
            ['name' => 'Azmir Husić', 'short' => 'A. Husić'],
            ['name' => 'Zlatko Džinović', 'short' => 'Z. Džinović'],
            ['name' => 'J. Omerefendić', 'short' => 'J. Omerefendić'],
            ['name' => 'Izet Karić', 'short' => 'I. Karić'],
            ['name' => 'Amir Meskovic', 'short' => 'A. Meskovic'],
            ['name' => 'Jasmin Zahirović', 'short' => 'J. Zahirović'],
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
        //    Prazan rezultat = nije odigrano (scheduled). Jedini WO: A. Husić - I. Karić.
        $rawMatches = [
            ['I. Karić', 'J. Omerefendić', null, null, false],
            ['I. Karić', 'J. Zahirović', null, null, false],
            ['I. Karić', 'Z. Džinović', [[4, 6], [6, 3], [10, 8]], 'home', false],
            ['I. Karić', 'S. Omerovic', [[4, 6], [0, 6]], 'away', false],
            ['J. Omerefendić', 'J. Zahirović', null, null, false],
            ['J. Omerefendić', 'Z. Džinović', [[6, 4], [6, 4]], 'home', false],
            ['J. Omerefendić', 'S. Omerovic', [[1, 6], [3, 6]], 'away', false],
            ['J. Omerefendić', 'A. Husić', [[6, 1], [2, 6], [8, 10]], 'away', false],
            ['J. Zahirović', 'Z. Džinović', [[0, 6], [0, 6]], 'away', false],
            ['J. Zahirović', 'S. Omerovic', null, null, false],
            ['J. Zahirović', 'A. Husić', null, null, false],
            ['J. Zahirović', 'S. Halilčević', null, null, false],
            ['Z. Džinović', 'S. Omerovic', [[4, 6], [1, 6]], 'away', false],
            ['Z. Džinović', 'A. Husić', [[5, 7], [4, 6]], 'away', false],
            ['Z. Džinović', 'S. Halilčević', [[3, 6], [3, 6]], 'away', false],
            ['Z. Džinović', 'A. Meskovic', [[6, 0], [6, 0]], 'home', false],
            ['S. Omerovic', 'A. Husić', [[4, 6], [6, 2], [10, 4]], 'home', false],
            ['S. Omerovic', 'S. Halilčević', [[1, 6], [4, 6]], 'away', false],
            ['S. Omerovic', 'A. Meskovic', [[6, 0], [6, 0]], 'home', false],
            ['A. Husić', 'S. Halilčević', null, null, false],
            ['A. Husić', 'A. Meskovic', [[6, 0], [6, 0]], 'home', false],
            ['A. Husić', 'I. Karić', null, 'home', true], // WO - pobijedio A. Husić
            ['S. Halilčević', 'A. Meskovic', [[6, 0], [6, 0]], 'home', false],
            ['S. Halilčević', 'I. Karić', [[6, 4], [6, 2]], 'home', false],
            ['S. Halilčević', 'J. Omerefendić', [[6, 4], [6, 3]], 'home', false],
            ['A. Meskovic', 'I. Karić', [[0, 6], [0, 6]], 'away', false],
            ['A. Meskovic', 'J. Omerefendić', [[0, 6], [0, 6]], 'away', false],
            ['A. Meskovic', 'J. Zahirović', null, null, false],
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

        echo "\n✓ Liga '3D. Liga' uspješno kreirana!\n";
        echo "✓ Raspored: 7 kola (round-robin)\n";
        echo "✓ Mečevi: 28 (odigrani + 1 WO + neodigrani)\n";
        echo "✓ URL: /takmicenja/{$league->slug}\n";
    }
}
