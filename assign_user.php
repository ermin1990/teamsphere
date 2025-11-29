<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Organization;

// Pronađi ili kreiraj usera
$user = User::firstOrCreate(
    ['email' => 'ermin1990@gmail.com'],
    [
        'name' => 'Ermin',
        'password' => bcrypt('password123')
    ]
);

echo "User: {$user->email} (ID: {$user->id})\n";

// Ažuriraj organizaciju
$org = Organization::find(1);
if ($org) {
    $org->update(['user_id' => $user->id]);
    $org->users()->syncWithoutDetaching([$user->id]);
    echo "Organizacija '{$org->name}' (ID: {$org->id}) ažurirana - vlasnik je sada {$user->email}\n";
} else {
    echo "Organizacija sa ID 1 ne postoji!\n";
}

echo "\nMožeš se ulogovati sa:\n";
echo "Email: ermin1990@gmail.com\n";
echo "Password: password123\n";
echo "\nURL: http://127.0.0.1:8001/login\n";
