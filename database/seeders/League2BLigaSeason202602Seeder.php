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

class League2BLigaSeason202602Seeder extends Seeder
{
    public function run(): void
    {
        $owner = \App\Models\User::where('email', 'ermin1990@gmail.com')->firstOrFail();
        $sport = \App\Models\Sport::where('slug', 'tenis')->firstOrFail();

        // 1. Ista organizacija kao 1. i 2A. Liga (Tuzlanska liga)
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

        // 3. Kreiraj ligu 2B
        $league = League::firstOrCreate(
            ['organization_id' => $organization->id, 'name' => '2B. Liga'],
            [
                'organization_id' => $organization->id,
                'name' => '2B. Liga',
                'slug' => '2b-liga-' . Str::random(6),
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
            ['name' => 'Ahmed Saračević', 'short' => 'A. Saračević'],
            ['name' => 'Emir Džinđovski', 'short' => 'E. Džinđovski'],
            ['name' => 'Samir Mehic', 'short' => 'S. Mehic'],
            ['name' => 'Emir Aščerić', 'short' => 'E. Aščerić'],
            ['name' => 'Alem Jusufagic', 'short' => 'A. Jusufagic'],
            ['name' => 'Jasmin Beganović', 'short' => 'J. Beganović'],
            ['name' => 'Nedim Pirić', 'short' => 'N. Pirić'],
            ['name' => 'Mirza Kavgic', 'short' => 'M. Kavgic'],
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
        //    Prazan rezultat = nije odigrano (scheduled). Jedini WO: S. Mehic - N. Pirić.
        $rawMatches = [
            ['M. Kavgic', 'A. Jusufagic', null, null, false],
            ['M. Kavgic', 'J. Beganović', null, null, false],
            ['M. Kavgic', 'S. Mehic', null, null, false],
            ['M. Kavgic', 'E. Aščerić', null, null, false],
            ['A. Jusufagic', 'J. Beganović', null, null, false],
            ['A. Jusufagic', 'S. Mehic', [[0, 6], [0, 6]], 'away', false],
            ['A. Jusufagic', 'E. Aščerić', null, null, false],
            ['A. Jusufagic', 'E. Džinđovski', [[6, 7], [3, 6]], 'away', false],
            ['J. Beganović', 'S. Mehic', [[0, 6], [1, 6]], 'away', false],
            ['J. Beganović', 'E. Aščerić', [[2, 6], [4, 6]], 'away', false],
            ['J. Beganović', 'E. Džinđovski', [[0, 6], [1, 6]], 'away', false],
            ['J. Beganović', 'A. Saračević', [[1, 6], [0, 6]], 'away', false],
            ['S. Mehic', 'E. Aščerić', [[0, 6], [5, 7]], 'away', false],
            ['S. Mehic', 'E. Džinđovski', null, null, false],
            ['S. Mehic', 'A. Saračević', [[2, 6], [3, 6]], 'away', false],
            ['S. Mehic', 'N. Pirić', null, 'home', true], // WO - pobijedio S. Mehic
            ['E. Aščerić', 'E. Džinđovski', [[2, 6], [1, 6]], 'away', false],
            ['E. Aščerić', 'A. Saračević', null, null, false],
            ['E. Aščerić', 'N. Pirić', null, null, false],
            ['E. Džinđovski', 'A. Saračević', [[4, 6], [3, 6]], 'away', false],
            ['E. Džinđovski', 'N. Pirić', [[2, 6], [2, 6]], 'away', false],
            ['E. Džinđovski', 'M. Kavgic', null, null, false],
            ['A. Saračević', 'N. Pirić', null, null, false],
            ['A. Saračević', 'M. Kavgic', null, null, false],
            ['A. Saračević', 'A. Jusufagic', [[7, 5], [6, 1]], 'home', false],
            ['N. Pirić', 'M. Kavgic', null, null, false],
            ['N. Pirić', 'A. Jusufagic', [[0, 6], [0, 6]], 'away', false],
            ['N. Pirić', 'J. Beganović', null, null, false],
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

        echo "\n✓ Liga '2B. Liga' uspješno kreirana!\n";
        echo "✓ Raspored: 7 kola (round-robin)\n";
        echo "✓ Mečevi: 28 (odigrani + 1 WO + neodigrani)\n";
        echo "✓ URL: /takmicenja/{$league->slug}\n";
    }
}
