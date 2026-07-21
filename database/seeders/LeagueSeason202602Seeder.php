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

class LeagueSeason202602Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Kreiraj/pronađi organizaciju
        $owner = \App\Models\User::where('email', 'ermin1990@gmail.com')->firstOrFail();
        $sport = \App\Models\Sport::where('slug', 'tenis')->firstOrFail();

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

        // 2. Kreiraj sezonu 2026/02
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

        // 3. Kreiraj ligu
        $league = League::firstOrCreate(
            ['organization_id' => $organization->id, 'name' => '1. Liga - Saša Vilušić'],
            [
                'organization_id' => $organization->id,
                'name' => '1. Liga - Saša Vilušić',
                'slug' => '1-liga-sasa-vilusic-' . Str::random(6),
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

        // 4. Kreiraj igrače
        $playerData = [
            ['name' => 'Said Husejinovic', 'short' => 'S. Husejinovic'],
            ['name' => 'Emir Ferizbegovic', 'short' => 'E. Ferizbegovic'],
            ['name' => 'Emir Poljo', 'short' => 'E. Poljo'],
            ['name' => 'Admir Atić', 'short' => 'A. Atić'],
            ['name' => 'Eldar Hadziefendic', 'short' => 'E. Hadziefendic'],
            ['name' => 'Amer Bajrić', 'short' => 'A. Bajrić'],
            ['name' => 'Omar Husic', 'short' => 'O. Husic'],
            ['name' => 'Emir Cilović', 'short' => 'E. Cilović'],
        ];

        $players = [];
        $playerShortToFull = [];

        foreach ($playerData as $data) {
            $existing = Player::where('organization_id', $organization->id)
                ->where('name', $data['name'])
                ->first();

            if ($existing) {
                $players[$data['short']] = $existing;
            } else {
                $player = Player::create([
                    'organization_id' => $organization->id,
                    'name' => $data['name'],
                    'email' => null,
                    'user_id' => null,
                    'is_active' => true,
                ]);
                $players[$data['short']] = $player;
            }
            $playerShortToFull[$data['short']] = $data['name'];
        }

        // 4b. Dodaj igrače kao UČESNIKE takmičenja (pivot competition_player)
        $pivotData = collect($players)
            ->mapWithKeys(fn ($player) => [$player->id => ['joined_at' => now()]])
            ->all();
        $league->players()->syncWithoutDetaching($pivotData);

        echo "\n✓ Igrači su učitani i dodani kao učesnici takmičenja.\n";

        // 5. Autoritativna lista mečeva (tačno kako ju je korisnik dao).
        //    Format: [home, away, sets (iz home perspektive) | null, winner ('home'|'away'|null), wo (bool)]
        //    - winner=null => nije odigrano (scheduled)
        //    - wo=true      => predat meč (WalkOver), gubitnik nije došao / diskvalifikovan
        $rawMatches = [
            ['E. Poljo', 'E. Cilović', null, 'home', true],
            ['E. Poljo', 'E. Ferizbegovic', null, null, false],
            ['E. Poljo', 'A. Atić', null, null, false],
            ['E. Poljo', 'E. Hadziefendic', [[6, 1], [6, 1]], 'home', false],
            ['E. Cilović', 'E. Ferizbegovic', null, 'away', true],
            ['E. Cilović', 'A. Atić', null, 'away', true],
            ['E. Cilović', 'E. Hadziefendic', null, 'away', true],
            ['E. Cilović', 'O. Husic', null, 'away', true],
            ['E. Ferizbegovic', 'A. Atić', [[3, 6], [6, 3], [10, 7]], 'home', false],
            ['E. Ferizbegovic', 'E. Hadziefendic', [[7, 5], [6, 3]], 'home', false],
            ['E. Ferizbegovic', 'O. Husic', [[6, 2], [2, 6], [11, 9]], 'home', false],
            ['E. Ferizbegovic', 'S. Husejinovic', [[6, 7], [2, 6]], 'away', false],
            ['A. Atić', 'E. Hadziefendic', [[6, 2], [6, 2]], 'home', false],
            ['A. Atić', 'O. Husic', null, 'home', true],
            ['A. Atić', 'S. Husejinovic', [[1, 6], [7, 6], [3, 10]], 'away', false],
            ['A. Atić', 'A. Bajrić', [[3, 6], [7, 5], [10, 7]], 'home', false],
            ['E. Hadziefendic', 'O. Husic', null, 'home', true],
            ['E. Hadziefendic', 'S. Husejinovic', [[5, 7], [1, 6]], 'away', false],
            ['E. Hadziefendic', 'A. Bajrić', [[6, 3], [6, 3]], 'home', false],
            ['O. Husic', 'S. Husejinovic', [[5, 7], [1, 6]], 'away', false],
            ['O. Husic', 'A. Bajrić', null, 'away', true],
            ['O. Husic', 'E. Poljo', null, 'away', true],
            ['S. Husejinovic', 'A. Bajrić', [[6, 3], [7, 6]], 'home', false],
            ['S. Husejinovic', 'E. Poljo', [[2, 6], [6, 7]], 'away', false],
            ['S. Husejinovic', 'E. Cilović', null, 'home', true],
            ['A. Bajrić', 'E. Poljo', [[6, 7], [2, 6]], 'away', false],
            ['A. Bajrić', 'E. Cilović', null, 'home', true],
            ['A. Bajrić', 'E. Ferizbegovic', [[1, 6], [2, 6]], 'away', false],
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
                    // WalkOver - pobjednik 2:0, gubitnik je predao meč
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

        echo "\n✓ Liga '1. Liga - Saša Vilušić' uspješno kreirana!\n";
        echo "✓ Raspored: 7 kola po round-robin sistemu\n";
        echo "✓ Mečevi: 28 (odigrani + WO + neodigrani)\n";
        echo "✓ Tabela: Izračunata kroz LeagueStandingsService\n";
    }
}
