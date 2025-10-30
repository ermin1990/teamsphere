<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Podaci koje je korisnik poslao
$standingsData = [
    'A' => [
        ['name' => 'Jelena Mrđen', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Šenk Ljupka', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Bojana Nježić', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Mujić Ajna', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
    ],
    'B' => [
        ['name' => 'Emina Cerić', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Sandra Dragišić', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Duvnjak Zara', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Teodora Bjelajac', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
    ],
    'C' => [
        ['name' => 'Husaković Lamija', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Rebihić Nejra', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Zara Kolak', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Osmić Adna', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
    ],
    'D' => [
        ['name' => 'Zembić Enna', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Letić Anela', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Saira Handžić', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Mikanović Dajana', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
    ],
    'E' => [
        ['name' => 'Ana Sladoje', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Knežević Slađa', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
        ['name' => 'Novosel Iva', 'pob' => 0, 'rem' => 0, 'por' => 0, 'set_pm' => 0, 'bod' => 0],
    ],
];

$groups = App\Models\Competition::find(17)->tournamentGroups;

foreach($groups as $group) {
    $groupName = $group->name;
    if (!isset($standingsData[$groupName])) {
        continue;
    }

    echo "Ažuriram grupu $groupName:\n";

    // Mapiraj igrače na ID-jeve
    $players = App\Models\Player::whereIn('id', $group->player_ids)->get()->keyBy('name');

    $newStandings = [];
    foreach($standingsData[$groupName] as $index => $playerData) {
        $playerName = $playerData['name'];
        $player = $players->get($playerName);

        if ($player) {
            $newStandings[] = [
                'player_id' => $player->id,
                'played' => 0, // $playerData['pob'] + $playerData['rem'] + $playerData['por'],
                'won' => $playerData['pob'],
                'lost' => $playerData['por'],
                'drawn' => $playerData['rem'],
                'points' => $playerData['bod'],
                'sets_won' => 0, // Trebam da izračunam
                'sets_lost' => 0, // Trebam da izračunam
                'points_scored' => 0,
                'points_conceded' => 0,
            ];
            echo "  $playerName -> ID: {$player->id}\n";
        } else {
            echo "  IGRAČ NIJE PRONAĐEN: $playerName\n";
        }
    }

    // Ažuriraj standings u bazi
    $group->update(['standings' => $newStandings]);
    echo "Grupa $groupName ažurirana sa " . count($newStandings) . " igrača\n\n";
}

echo "Završeno!\n";