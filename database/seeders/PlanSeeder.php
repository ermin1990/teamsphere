<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Isprobaj MojTurnir besplatno s jednom organizacijom',
                'price' => 0.00,
                'currency' => 'BAM',
                'billing_period' => 'yearly',
                'max_organizations' => 1,
                'max_leagues_per_organization' => 2,
                'max_competitions_per_organization' => 5,
                'max_teams_per_league' => 8,
                'max_players_per_team' => 15,
                'features' => [
                    'Osnovno upravljanje ligama',
                    'Do 2 lige po organizaciji',
                    'Do 8 timova po ligi',
                    'Osnovna statistika',
                    'Podrška zajednice',
                ],
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Za organizatore koji vode više liga i naplaćuju kotizaciju',
                'price' => 299.00,
                'currency' => 'BAM',
                'billing_period' => 'yearly',
                'max_organizations' => 3,
                'max_leagues_per_organization' => 8,
                'max_competitions_per_organization' => 20,
                'max_teams_per_league' => 20,
                'max_players_per_team' => 28,
                'features' => [
                    'Sve iz Free plana',
                    'Do 3 organizacije',
                    'Napredna statistika',
                    'Prioritetna podrška',
                    'Izvoz podataka',
                ],
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Za saveze, asocijacije i veće federacije s više organizacija',
                'price' => 699.00,
                'currency' => 'BAM',
                'billing_period' => 'yearly',
                'max_organizations' => 10,
                'max_leagues_per_organization' => 50,
                'max_competitions_per_organization' => 100,
                'max_teams_per_league' => 50,
                'max_players_per_team' => 60,
                'features' => [
                    'Sve iz Pro plana',
                    'Do 10 organizacija',
                    'Napredna analitika',
                    'Namjenska podrška',
                    'Prilagođene integracije',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
