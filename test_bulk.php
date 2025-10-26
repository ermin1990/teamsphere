<?php

// Test bulk import parsing
$text = "Zijadić Benjamin, Aladža;
Čorić Emin, Aladža;
Škulj Bakir, Aladža;
Panić Demijan, Banja Luka;";

$lines = explode("\n", $text);
$playersData = [];
$errors = [];

foreach ($lines as $lineNumber => $line) {
    $line = trim($line);
    if (empty($line)) continue;

    if (!str_ends_with($line, ';')) {
        $errors[] = "Linija " . ($lineNumber + 1) . ": Mora završavati sa ';'";
        continue;
    }

    $line = rtrim($line, ';');
    $parts = explode(',', $line, 2);

    if (count($parts) !== 2) {
        $errors[] = "Linija " . ($lineNumber + 1) . ": Format mora biti 'Ime i prezime, Klub'";
        continue;
    }

    $name = trim($parts[0]);
    $club = trim($parts[1]);

    if (empty($name)) {
        $errors[] = "Linija " . ($lineNumber + 1) . ": Ime igrača je obavezno";
        continue;
    }

    $playersData[] = [
        'name' => $name,
        'club' => $club,
    ];
}

echo "Parsed players:\n";
foreach ($playersData as $player) {
    echo "- {$player['name']} ({$player['club']})\n";
}

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
}