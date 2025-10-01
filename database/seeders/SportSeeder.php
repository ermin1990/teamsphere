<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sport;

class SportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sports = [
            [
                'name' => 'Stoni Tenis',
                'slug' => 'stoni-tenis',
                'description' => 'Brzi i dinamični sport koji zahtijeva preciznost, brzinu i strategiju.',
                'icon' => '🏓',
            ],
            [
                'name' => 'Padel',
                'slug' => 'padel',
                'description' => 'Sport koji kombinuje elemente tenisa, skvoša i badmintona u zatvorenom terenu.',
                'icon' => '🎾',
            ],
            [
                'name' => 'Tenis',
                'slug' => 'tenis',
                'description' => 'Klasični sport sa reketom i loptom koji se igra na terenu sa mrežom.',
                'icon' => '🎾',
            ],
            [
                'name' => 'Košarka',
                'slug' => 'kosarka',
                'description' => 'Dinamični timski sport gdje se lopta ubacuje u koš.',
                'icon' => '🏀',
            ],
            [
                'name' => 'Mali Fudbal',
                'slug' => 'mali-fudbal',
                'description' => 'Brzi timski sport koji se igra u zatvorenom prostoru sa 5 igrača po timu.',
                'icon' => '⚽',
            ],
        ];

        foreach ($sports as $sport) {
            Sport::create($sport);
        }
    }
}
