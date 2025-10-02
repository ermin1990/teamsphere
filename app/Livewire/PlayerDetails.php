<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Player;
use App\Models\FriendlyMatch;

class PlayerDetails extends Component
{
    public $organization;
    public $player;
    public $matches;
    public $stats;

    public function mount($organization, $playerId)
    {
        $this->organization = $organization;
        $this->player = Player::findOrFail($playerId);

        // Fetch matches for this player in this organization
        $this->matches = FriendlyMatch::where('organization_id', $organization->id)
            ->where(function($query) use ($playerId) {
                $query->where('home_player_id', $playerId)
                      ->orWhere('away_player_id', $playerId);
            })
            ->orderByDesc('completed_at')
            ->get();

        $this->calculateStats();
    }

    private function calculateStats()
    {
        $stats = [
            'mp' => 0, 'w' => 0, 'l' => 0, 'sets_w' => 0, 'sets_l' => 0,
            'points_w' => 0, 'points_l' => 0,
            'win_streak' => 0, 'loss_streak' => 0, 'longest_win_streak' => 0, 'longest_loss_streak' => 0,
            'opponents' => []
        ];

        $lastResult = null;

        foreach ($this->matches as $match) {
            $stats['mp']++;
            $isHome = $match->home_player_id == $this->player->id;
            $playerSets = $isHome ? $match->sets[array_key_last($match->sets)]['home_score'] : $match->sets[array_key_last($match->sets)]['away_score'];
            $opponentSets = $isHome ? $match->sets[array_key_last($match->sets)]['away_score'] : $match->sets[array_key_last($match->sets)]['home_score'];

            $opponentId = $isHome ? $match->away_player_id : $match->home_player_id;
            $opponentName = $isHome ? $match->away_player_name : $match->home_player_name;

            if (!isset($stats['opponents'][$opponentId])) {
                $stats['opponents'][$opponentId] = ['name' => $opponentName, 'wins' => 0, 'losses' => 0];
            }

            if ($playerSets > $opponentSets) {
                $stats['w']++;
                $stats['opponents'][$opponentId]['wins']++;
                if ($lastResult === 'win') {
                    $stats['win_streak']++;
                } else {
                    $stats['win_streak'] = 1;
                    $stats['loss_streak'] = 0;
                }
                $lastResult = 'win';
            } else {
                $stats['l']++;
                $stats['opponents'][$opponentId]['losses']++;
                if ($lastResult === 'loss') {
                    $stats['loss_streak']++;
                } else {
                    $stats['loss_streak'] = 1;
                    $stats['win_streak'] = 0;
                }
                $lastResult = 'loss';
            }

            $stats['longest_win_streak'] = max($stats['longest_win_streak'], $stats['win_streak']);
            $stats['longest_loss_streak'] = max($stats['longest_loss_streak'], $stats['loss_streak']);

            foreach ($match->sets as $set) {
                $stats['sets_w'] += $isHome ? $set['home_score'] : $set['away_score'];
                $stats['sets_l'] += $isHome ? $set['away_score'] : $set['home_score'];
            }

            // Calculate points if available (assuming sets have points, but we don't have detailed points)
            // For now, skip points calculation as we don't store individual points
        }

        $this->stats = $stats;
    }

    public function render()
    {
        return view('livewire.player-details');
    }
}