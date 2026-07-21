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

class League4BLigaSeason202602Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Ista organizacija kao ostale lige (Tuzlanska liga)
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

        // 3. Kreiraj ligu 4B (10 igrača)
        $league = League::firstOrCreate(
            ['organization_id' => $organization->id, 'name' => '4B. Liga'],
            [
                'organization_id' => $organization->id,
                'name' => '4B. Liga',
                'slug' => '4b-liga-' . Str::random(6),
                'sport_id' => 3, // Tenis
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
            ['name' => 'Dževad Imširović', 'short' => 'D. Imširović'],
            ['name' => 'Nermin Krdžalić', 'short' => 'N. Krdžalić'],
            ['name' => 'Adnan Sijercic', 'short' => 'A. Sijercic'],
            ['name' => 'Narcis Berbic', 'short' => 'N. Berbic'],
            ['name' => 'Amir Osmanovic', 'short' => 'A. Osmanovic'],
            ['name' => 'Robert Kovacevic', 'short' => 'R. Kovacevic'],
            ['name' => 'Senad Neimarlija', 'short' => 'S. Neimarlija'],
            ['name' => 'Amar Hadžiselimović', 'short' => 'A. Hadziselimovic'],
            ['name' => 'Zoran Pavljašević', 'short' => 'Z. Pavljašević'],
            ['name' => 'Zijad Gergić', 'short' => 'Z. Gergić'],
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
        //    (scheduled). Nema WO mečeva u ovoj ligi - samo 12 od 45 je
        //    odigrano.
        //    [home, away, sets (iz home perspektive) | null, winner ('home'|'away'|null), wo (bool)]
        $rawMatches = [
            ['A. Hadziselimovic', 'Z. Gergić', null, null, false],
            ['A. Hadziselimovic', 'S. Neimarlija', null, null, false],
            ['A. Hadziselimovic', 'N. Berbic', [[0, 6], [0, 6]], 'away', false],
            ['A. Hadziselimovic', 'R. Kovacevic', null, null, false],
            ['A. Hadziselimovic', 'D. Imširović', null, null, false],
            ['Z. Gergić', 'S. Neimarlija', null, null, false],
            ['Z. Gergić', 'N. Berbic', null, null, false],
            ['Z. Gergić', 'R. Kovacevic', null, null, false],
            ['Z. Gergić', 'D. Imširović', null, null, false],
            ['Z. Gergić', 'N. Krdžalić', null, null, false],
            ['S. Neimarlija', 'N. Berbic', [[6, 2], [6, 1]], 'home', false],
            ['S. Neimarlija', 'R. Kovacevic', [[2, 6], [3, 6]], 'away', false],
            ['S. Neimarlija', 'D. Imširović', [[2, 6], [3, 6]], 'away', false],
            ['S. Neimarlija', 'N. Krdžalić', null, null, false],
            ['S. Neimarlija', 'A. Osmanovic', null, null, false],
            ['N. Berbic', 'R. Kovacevic', null, null, false],
            ['N. Berbic', 'D. Imširović', [[3, 6], [3, 6]], 'away', false],
            ['N. Berbic', 'N. Krdžalić', null, null, false],
            ['N. Berbic', 'A. Osmanovic', null, null, false],
            ['N. Berbic', 'Z. Pavljašević', null, null, false],
            ['R. Kovacevic', 'D. Imširović', null, null, false],
            ['R. Kovacevic', 'N. Krdžalić', [[5, 7], [6, 4], [3, 10]], 'away', false],
            ['R. Kovacevic', 'A. Osmanovic', [[7, 6], [5, 7], [5, 10]], 'away', false],
            ['R. Kovacevic', 'Z. Pavljašević', null, null, false],
            ['R. Kovacevic', 'A. Sijercic', null, null, false],
            ['D. Imširović', 'N. Krdžalić', [[3, 6], [1, 6]], 'away', false],
            ['D. Imširović', 'A. Osmanovic', null, null, false],
            ['D. Imširović', 'Z. Pavljašević', null, null, false],
            ['D. Imširović', 'A. Sijercic', [[6, 7], [6, 4], [10, 8]], 'home', false],
            ['N. Krdžalić', 'A. Osmanovic', null, null, false],
            ['N. Krdžalić', 'Z. Pavljašević', null, null, false],
            ['N. Krdžalić', 'A. Sijercic', [[6, 2], [6, 0]], 'home', false],
            ['N. Krdžalić', 'A. Hadziselimovic', null, null, false],
            ['A. Osmanovic', 'Z. Pavljašević', null, null, false],
            ['A. Osmanovic', 'A. Sijercic', [[6, 2], [6, 3]], 'home', false],
            ['A. Osmanovic', 'A. Hadziselimovic', null, null, false],
            ['A. Osmanovic', 'Z. Gergić', null, null, false],
            ['Z. Pavljašević', 'A. Sijercic', null, null, false],
            ['Z. Pavljašević', 'A. Hadziselimovic', null, null, false],
            ['Z. Pavljašević', 'Z. Gergić', null, null, false],
            ['Z. Pavljašević', 'S. Neimarlija', null, null, false],
            ['A. Sijercic', 'A. Hadziselimovic', null, null, false],
            ['A. Sijercic', 'Z. Gergić', null, null, false],
            ['A. Sijercic', 'S. Neimarlija', null, null, false],
            ['A. Sijercic', 'N. Berbic', [[6, 2], [6, 4]], 'home', false],
        ];

        // 6. Round-robin raspored (10 igrača, 9 kola) -> mapa neuređenog
        //    para na broj kola.
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

        echo "\n✓ Liga '4B. Liga' uspješno kreirana!\n";
        echo "✓ Raspored: 9 kola (round-robin, 10 igrača)\n";
        echo "✓ Mečevi: 45 (12 odigranih + 33 neodigranih)\n";
        echo "✓ URL: /takmicenja/{$league->slug}\n";
    }
}
