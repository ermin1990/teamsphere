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

class League4CLigaSeason202602Seeder extends Seeder
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

        // 3. Kreiraj ligu 4C (9 igrača - neparan broj, jedan "bye" po kolu)
        $league = League::firstOrCreate(
            ['organization_id' => $organization->id, 'name' => '4C. Liga'],
            [
                'organization_id' => $organization->id,
                'name' => '4C. Liga',
                'slug' => '4c-liga-' . Str::random(6),
                'sport_id' => $sport->id,
                'type' => 'league',
                'season_id' => $season->id,
                'is_team_based' => false,
                'status' => 'active',
                'max_teams' => 9,
                'is_active' => true,
                'is_public' => true,
            ]
        );

        // 4. Igrači (9 ukupno)
        $playerData = [
            ['name' => 'Mirnes Mustafić', 'short' => 'M. Mustafić'],
            ['name' => 'Ervin Omerović', 'short' => 'E. Omerović'],
            ['name' => 'Ademir Omerbegovic', 'short' => 'A. Omerbegovic'],
            ['name' => 'Eldar Dubravic', 'short' => 'E. Dubravic'],
            ['name' => 'Goran Smajlovic', 'short' => 'G. Smajlovic'],
            ['name' => 'Sead Bećarević', 'short' => 'S. Bećarević'],
            ['name' => 'Tarik Žunić', 'short' => 'T. Žunić'],
            ['name' => 'Alen Alic', 'short' => 'A. Alic'],
            ['name' => 'Aldin Sinanovic', 'short' => 'A. Sinanovic'],
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

        // 5. Autoritativna lista mečeva (tačno kako ju je korisnik dao, 36
        //    parova za 9 igrača). Prazan rezultat = nije odigrano
        //    (scheduled). Nema WO mečeva u ovoj ligi - samo 5 od 36 je
        //    odigrano.
        //    [home, away, sets (iz home perspektive) | null, winner ('home'|'away'|null), wo (bool)]
        $rawMatches = [
            ['E. Omerović', 'A. Sinanovic', null, null, false],
            ['E. Omerović', 'S. Bećarević', null, null, false],
            ['E. Omerović', 'A. Alic', null, null, false],
            ['E. Omerović', 'M. Mustafić', null, null, false],
            ['A. Sinanovic', 'S. Bećarević', null, null, false],
            ['A. Sinanovic', 'A. Alic', null, null, false],
            ['A. Sinanovic', 'M. Mustafić', null, null, false],
            ['A. Sinanovic', 'A. Omerbegovic', null, null, false],
            ['S. Bećarević', 'A. Alic', null, null, false],
            ['S. Bećarević', 'M. Mustafić', [[2, 6], [3, 6]], 'away', false],
            ['S. Bećarević', 'A. Omerbegovic', null, null, false],
            ['S. Bećarević', 'G. Smajlovic', null, null, false],
            ['A. Alic', 'M. Mustafić', null, null, false],
            ['A. Alic', 'A. Omerbegovic', null, null, false],
            ['A. Alic', 'G. Smajlovic', null, null, false],
            ['A. Alic', 'E. Dubravic', null, null, false],
            ['M. Mustafić', 'A. Omerbegovic', [[6, 2], [6, 1]], 'home', false],
            ['M. Mustafić', 'G. Smajlovic', null, null, false],
            ['M. Mustafić', 'E. Dubravic', null, null, false],
            ['M. Mustafić', 'T. Žunić', null, null, false],
            ['A. Omerbegovic', 'G. Smajlovic', null, null, false],
            ['A. Omerbegovic', 'E. Dubravic', [[6, 2], [7, 6]], 'home', false],
            ['A. Omerbegovic', 'T. Žunić', null, null, false],
            ['A. Omerbegovic', 'E. Omerović', [[2, 6], [3, 6]], 'away', false],
            ['G. Smajlovic', 'E. Dubravic', null, null, false],
            ['G. Smajlovic', 'T. Žunić', null, null, false],
            ['G. Smajlovic', 'E. Omerović', [[4, 6], [2, 6]], 'away', false],
            ['G. Smajlovic', 'A. Sinanovic', null, null, false],
            ['E. Dubravic', 'T. Žunić', null, null, false],
            ['E. Dubravic', 'E. Omerović', null, null, false],
            ['E. Dubravic', 'A. Sinanovic', null, null, false],
            ['E. Dubravic', 'S. Bećarević', null, null, false],
            ['T. Žunić', 'E. Omerović', null, null, false],
            ['T. Žunić', 'A. Sinanovic', null, null, false],
            ['T. Žunić', 'S. Bećarević', null, null, false],
            ['T. Žunić', 'A. Alic', null, null, false],
        ];

        // 6. Round-robin raspored za NEPARAN broj igrača (9): dodaje se
        //    fantomski "BYE" da broj bude paran (10), pa se standardna
        //    circle-method metoda koristi; par koji uključuje BYE se
        //    preskače (taj igrač tog kola ne igra).
        $playerKeys = array_keys($players);
        if (count($playerKeys) % 2 !== 0) {
            $playerKeys[] = '__BYE__';
        }
        $playerCount = count($playerKeys);
        $pairRound = [];
        $pairKey = fn ($a, $b) => implode('|', collect([$a, $b])->sort()->values()->all());

        for ($round = 1; $round < $playerCount; $round++) {
            for ($i = 0; $i < $playerCount / 2; $i++) {
                $a = $playerKeys[$i];
                $b = $playerKeys[$playerCount - 1 - $i];
                if ($a !== $b && $a !== '__BYE__' && $b !== '__BYE__') {
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

        echo "\n✓ Liga '4C. Liga' uspješno kreirana!\n";
        echo "✓ Raspored: 9 kola (round-robin, 9 igrača, po jedan bye/kolo)\n";
        echo "✓ Mečevi: 36 (5 odigranih + 31 neodigranih)\n";
        echo "✓ URL: /takmicenja/{$league->slug}\n";
    }
}
