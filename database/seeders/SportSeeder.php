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
                'rules' => [
                    'game_type' => 'points_based',
                    'max_points_per_game' => 11,
                    'games_to_win' => 5,
                    'serve_change_every' => 2,
                    'deuce' => false,
                    'time_limits' => null,
                    'players_per_team' => 1,
                    'scoring' => [
                        'point_values' => [1],
                        'special_rules' => 'Must win by 2 points'
                    ]
                ]
            ],
            [
                'name' => 'Padel',
                'slug' => 'padel',
                'description' => 'Sport koji kombinuje elemente tenisa, skvoša i badmintona u zatvorenom terenu.',
                'icon' => '🎾',
                'rules' => [
                    'game_type' => 'sets_games',
                    'sets_to_win' => 2,
                    'games_per_set' => 6,
                    'tie_break_at' => 6,
                    'serve_change_every' => 1,
                    'deuce' => true,
                    'time_limits' => null,
                    'players_per_team' => 2,
                    'team_tie_format' => 'single_match',
                    'scoring' => [
                        'point_values' => [15, 30, 40, 'game'],
                        'special_rules' => 'Tie-break at 6-6'
                    ]
                ]
            ],
            [
                'name' => 'Tenis',
                'slug' => 'tenis',
                'description' => 'Klasični sport sa reketom i loptom koji se igra na terenu sa mrežom.',
                'icon' => '🎾',
                'rules' => [
                    'game_type' => 'sets_games',
                    'sets_to_win' => 2,
                    'games_per_set' => 6,
                    'tie_break_at' => 6,
                    'serve_change_every' => 1,
                    'deuce' => true,
                    'time_limits' => null,
                    'players_per_team' => 1,
                    'scoring' => [
                        'point_values' => [15, 30, 40, 'game'],
                        'special_rules' => 'Tie-break at 6-6, 3 sets to win match'
                    ]
                ]
            ],
            [
                'name' => 'Košarka',
                'slug' => 'kosarka',
                'description' => 'Dinamični timski sport gdje se lopta ubacuje u koš.',
                'icon' => '🏀',
                'rules' => [
                    'game_type' => 'time_based',
                    'periods' => 4,
                    'period_duration' => 600, // 10 minutes in seconds
                    'overtime' => true,
                    'players_per_team' => 5,
                    'max_players_on_court' => 5,
                    'scoring' => [
                        'point_values' => [1, 2, 3],
                        'special_rules' => '2 points for field goal, 3 points for three-pointer, 1 point for free throw'
                    ]
                ]
            ],
            [
                'name' => 'Mali Fudbal',
                'slug' => 'mali-fudbal',
                'description' => 'Brzi timski sport koji se igra u zatvorenom prostoru sa 5 igrača po timu.',
                'icon' => '⚽',
                'rules' => [
                    'game_type' => 'time_based',
                    'periods' => 2,
                    'period_duration' => 1200, // 20 minutes in seconds
                    'overtime' => false,
                    'players_per_team' => 5,
                    'max_players_on_court' => 5,
                    'scoring' => [
                        'point_values' => [1],
                        'special_rules' => '1 goal = 1 point, offside rule applies'
                    ]
                ]
            ],
        ];

        foreach ($sports as $sport) {
            Sport::updateOrCreate(
                ['slug' => $sport['slug']],
                $sport
            );
        }
    }
}
