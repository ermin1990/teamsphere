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
                'description' => 'Perfect for getting started with Team Sphere',
                'price' => 0.00,
                'currency' => 'EUR',
                'max_organizations' => 1,
                'max_leagues_per_organization' => 1,
                'max_teams_per_league' => 8,
                'max_players_per_team' => 15,
                'features' => [
                    '1 organizacija',
                    '1 liga po organizaciji',
                    '8 timova po ligi',
                    '15 igrača po timu',
                    'Osnovne statistike'
                ]
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'For small teams and clubs',
                'price' => 9.99,
                'currency' => 'EUR',
                'max_organizations' => 2,
                'max_leagues_per_organization' => 5,
                'max_teams_per_league' => 12,
                'max_players_per_team' => 20,
                'features' => [
                    '2 organizacije',
                    '5 liga po organizaciji',
                    '12 timova po ligi',
                    '20 igrača po timu',
                    'Email podrška',
                    'Osnovne statistike'
                ]
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'For growing sports organizations and clubs',
                'price' => 19.99,
                'currency' => 'EUR',
                'max_organizations' => 5,
                'max_leagues_per_organization' => 20,
                'max_teams_per_league' => 20,
                'max_players_per_team' => 30,
                'features' => [
                    '5 organizacija',
                    '20 liga po organizaciji',
                    '20 timova po ligi',
                    '30 igrača po timu',
                    'Prioritetna podrška',
                    'Napredne statistike',
                    'API pristup'
                ]
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'For professional leagues and large federations',
                'price' => 49.99,
                'currency' => 'EUR',
                'max_organizations' => 20,
                'max_leagues_per_organization' => 100,
                'max_teams_per_league' => 50,
                'max_players_per_team' => 50,
                'features' => [
                    '20 organizacija',
                    '100 liga po organizaciji',
                    '50 timova po ligi',
                    '50 igrača po timu',
                    'White-label rješenje',
                    'Dedikovana podrška',
                    'Real-time notifikacije',
                    'Custom integracije'
                ]
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large federations and corporations',
                'price' => 99.99,
                'currency' => 'EUR',
                'max_organizations' => 50,
                'max_leagues_per_organization' => 200,
                'max_teams_per_league' => 100,
                'max_players_per_team' => 100,
                'features' => [
                    '50 organizacija',
                    '200 liga po organizaciji',
                    '100 timova po ligi',
                    '100 igrača po timu',
                    'SLA garancija',
                    'Custom development',
                    'Dedicated account manager',
                    '24/7 podrška'
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
