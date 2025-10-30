<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Podaci koje je korisnik poslao
$standingsData = [
    'A' => [
        1 => ['name' => 'Jelena Mrđen', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        2 => ['name' => 'Šenk Ljupka', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        3 => ['name' => 'Bojana Nježić', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        4 => ['name' => 'Mujić Ajna', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
    ],
    'B' => [
        1 => ['name' => 'Emina Cerić', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        2 => ['name' => 'Sandra Dragišić', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        3 => ['name' => 'Duvnjak Zara', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        4 => ['name' => 'Teodora Bjelajac', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
    ],
    'C' => [
        1 => ['name' => 'Husaković Lamija', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        2 => ['name' => 'Rebihić Nejra', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        3 => ['name' => 'Zara Kolak', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        4 => ['name' => 'Osmić Adna', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
    ],
    'D' => [
        1 => ['name' => 'Zembić Enna', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        2 => ['name' => 'Letić Anela', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        3 => ['name' => 'Saira Handžić', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        4 => ['name' => 'Mikanović Dajana', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
    ],
    'E' => [
        1 => ['name' => 'Ana Sladoje', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        2 => ['name' => 'Knežević Slađa', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        3 => ['name' => 'Novosel Iva', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
    ],
];

$groups = App\Models\Competition::find(17)->tournamentGroups;

foreach($groups as $group) {
    $groupName = $group->name;
    if (!isset($standingsData[$groupName])) {
        continue;
    }

    echo "Ažuriram Standing zapise za grupu $groupName:\n";

    foreach($standingsData[$groupName] as $position => $playerData) {
        $playerName = $playerData['name'];

        // Pronađi igrača
        $player = App\Models\Player::where('name', $playerName)->first();

        if ($player) {
            // Pronađi ili kreiraj Standing zapis
            $standing = App\Models\Standing::where('competition_id', 17)
                ->where('tournament_group_id', $group->id)
                ->where('player_id', $player->id)
                ->first();

            if ($standing) {
                // Ažuriraj postojeći zapis
                $standing->update([
                    'position' => $position,
                    'played' => 0,
                    'won' => $playerData['pob'],
                    'drawn' => $playerData['rem'],
                    'lost' => $playerData['por'],
                    'points' => $playerData['bod'],
                    'sets_won' => 0,
                    'sets_lost' => 0,
                    'points_won' => 0,
                    'points_lost' => 0,
                    'goals_for' => 0,
                    'goals_against' => 0,
                    'goal_difference' => 0,
                ]);
                echo "  Ažuriran: $playerName (pozicija $position)\n";
            } else {
                // Kreiraj novi zapis
                App\Models\Standing::create([
                    'competition_id' => 17,
                    'tournament_group_id' => $group->id,
                    'player_id' => $player->id,
                    'position' => $position,
                    'played' => 0,
                    'won' => $playerData['pob'],
                    'drawn' => $playerData['rem'],
                    'lost' => $playerData['por'],
                    'points' => $playerData['bod'],
                    'sets_won' => 0,
                    'sets_lost' => 0,
                    'points_won' => 0,
                    'points_lost' => 0,
                    'goals_for' => 0,
                    'goals_against' => 0,
                    'goal_difference' => 0,
                ]);
                echo "  Kreiran: $playerName (pozicija $position)\n";
            }
        } else {
            echo "  IGRAČ NIJE PRONAĐEN: $playerName\n";
        }
    }
    echo "\n";
}

echo "Završeno!\n";