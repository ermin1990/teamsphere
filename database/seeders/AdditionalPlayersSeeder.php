<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Player;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdditionalPlayersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $org = Organization::first();
        
        if (!$org) {
            $this->command->error('No organization found!');
            return;
        }

        $players = [
            'Marko Petrovic',
            'Ana Jovanovic',
            'Stefan Nikolic',
            'Jelena Markovic',
            'Dusan Ilic',
            'Milica Popovic',
            'Nikola Djordjevic',
            'Sara Kovacevic',
            'Luka Milosevic',
            'Teodora Stankovic',
            'Petar Jovic',
            'Aleksandra Simic',
            'Milan Ristic',
            'Katarina Savic',
            'Nemanja Stojanovic',
            'Jovana Filipovic',
            'Bojan Pavlovic',
            'Ivana Radovanovic',
            'Ognjen Zivkovic',
            'Maja Antic'
        ];

        foreach ($players as $name) {
            // Check if player already exists
            if (!Player::where('name', $name)->where('organization_id', $org->id)->exists()) {
                Player::create([
                    'name' => $name,
                    'organization_id' => $org->id,
                ]);
            }
        }

        $this->command->info('Added ' . count($players) . ' players to organization: ' . $org->name);
    }
}
