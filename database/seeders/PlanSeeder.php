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
                'description' => 'Perfect for getting started with MojTurnir',
                'price' => 0.00,
                'currency' => 'EUR',
                'max_organizations' => 1,
                'max_leagues_per_organization' => 2,
                'max_competitions_per_organization' => 5,
                'max_teams_per_league' => 8,
                'max_players_per_team' => 15,
                'features' => [
                    'Basic league management',
                    'Up to 2 leagues per organization',
                    'Up to 8 teams per league',
                    'Basic statistics',
                    'Community support'
                ]
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'For growing sports organizations and clubs',
                'price' => 19.99,
                'currency' => 'EUR',
                'max_organizations' => 3,
                'max_leagues_per_organization' => 10,
                'max_competitions_per_organization' => 20,
                'max_teams_per_league' => 20,
                'max_players_per_team' => 30,
                'features' => [
                    'Everything in Free',
                    'Advanced statistics',
                    'Custom branding',
                    'Priority support',
                    'API access',
                    'Export data'
                ]
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'For professional leagues and large federations',
                'price' => 49.99,
                'currency' => 'EUR',
                'max_organizations' => 10,
                'max_leagues_per_organization' => 50,
                'max_competitions_per_organization' => 100,
                'max_teams_per_league' => 50,
                'max_players_per_team' => 50,
                'features' => [
                    'Everything in Pro',
                    'Unlimited organizations',
                    'Advanced analytics',
                    'White-label solution',
                    'Dedicated support',
                    'Custom integrations',
                    'Real-time notifications'
                ]
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
