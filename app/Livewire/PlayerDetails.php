<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Player;
use App\Models\FriendlyMatch;
use App\Models\League;
use App\Models\LeagueMatch;

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

        // Get all matches for the player for stats and recent form
        $this->loadMatches();
        $this->calculateStats();
    }

    private function loadMatches()
    {
        // Fetch friendly matches
        $friendlyMatches = FriendlyMatch::where('organization_id', $this->organization->id)
            ->where(function($query) {
                $query->where('home_player_id', $this->player->id)
                      ->orWhere('away_player_id', $this->player->id)
                      ->orWhereRaw("home_player_name LIKE ?", ["%" . $this->player->name . "%"])
                      ->orWhereRaw("away_player_name LIKE ?", ["%" . $this->player->name . "%"]);
            })
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->get();

        $detailedFriendly = [];
        foreach ($friendlyMatches as $match) {
            $isHome = $match->home_player_id == $this->player->id || (isset($match->home_player_name) && $match->home_player_name == $this->player->name);
            $opponent = $isHome ? $match->away_player_name : $match->home_player_name;
            $result = null;
            if (is_array($match->sets) && count($match->sets) > 0) {
                $lastSet = $match->sets[array_key_last($match->sets)];
                $result = ($isHome ? $lastSet['home_score'] : $lastSet['away_score']) . ' - ' . ($isHome ? $lastSet['away_score'] : $lastSet['home_score']);
            }
            $detailedFriendly[] = [
                'id' => $match->id,
                'type' => 'Prijateljski',
                'date' => $match->completed_at,
                'league' => null,
                'league_id' => null,
                'opponent' => $opponent,
                'result' => $result,
                'sets' => $match->sets,
                'winner' => $match->winner_name,
                'home_player_name' => $match->home_player_name,
                'away_player_name' => $match->away_player_name,
            ];
        }

        // Fetch league matches
        $detailedLeague = [];
        $leagues = League::where('organization_id', $this->organization->id)->get();
        
        foreach ($leagues as $league) {
            $matches = LeagueMatch::where('league_id', $league->id)
                ->where(function($query) {
                    $query->where('home_player_id', $this->player->id)
                          ->orWhere('away_player_id', $this->player->id);
                })
                ->whereIn('status', ['completed', 'forfeited'])
                ->whereNotNull('played_at')
                ->orderByDesc('played_at')
                ->get();
            
            foreach ($matches as $m) {
                $isHome = $m->home_player_id == $this->player->id;
                $opponent = $isHome ? 
                    ($m->awayPlayer ? $m->awayPlayer->name : 'Nepoznat igrač') : 
                    ($m->homePlayer ? $m->homePlayer->name : 'Nepoznat igrač');
                
                $result = null;
                
                if ($m->status === 'forfeited') {
                    if ($m->forfeited_by === 'home' && !$isHome) {
                        $result = '0 - 0 (W.O.)';
                    } elseif ($m->forfeited_by === 'away' && $isHome) {
                        $result = '0 - 0 (W.O.)';
                    } else {
                        $result = '0 - 0 (L.O.)';
                    }
                } elseif (is_array($m->sets) && count($m->sets) > 0) {
                    $lastSet = $m->sets[array_key_last($m->sets)];
                    $playerScore = $isHome ? $lastSet['home_score'] : $lastSet['away_score'];
                    $opponentScore = $isHome ? $lastSet['away_score'] : $lastSet['home_score'];
                    $result = $playerScore . ' - ' . $opponentScore;
                } elseif ($m->home_score !== null && $m->away_score !== null) {
                    $playerScore = $isHome ? $m->home_score : $m->away_score;
                    $opponentScore = $isHome ? $m->away_score : $m->home_score;
                    $result = $playerScore . ' - ' . $opponentScore;
                }
                
                $detailedLeague[] = [
                    'id' => $m->id,
                    'type' => 'Ligaški',
                    'date' => $m->played_at,
                    'league' => $league->name,
                    'league_id' => $league->id,
                    'league_slug' => $league->slug,
                    'opponent' => $opponent,
                    'result' => $result,
                    'sets' => $m->sets,
                    'winner' => optional($m->winner)->name,
                    'home_player_name' => $m->homePlayer ? $m->homePlayer->name : 'Nepoznat',
                    'away_player_name' => $m->awayPlayer ? $m->awayPlayer->name : 'Nepoznat',
                ];
            }
        }

        // Combine and sort all matches
        $this->matches = collect($detailedFriendly)->merge($detailedLeague)->sortByDesc('date');
    }

    private function calculateStats()
    {
        $matches = $this->matches;

        // Initialize stats
        $stats = [
            'mp' => 0,  // matches played
            'w' => 0,   // wins
            'l' => 0,   // losses
            'sets_w' => 0,
            'sets_l' => 0,
            'opponents' => [],
            'win_streak' => 0,
            'loss_streak' => 0,
            'longest_win_streak' => 0,
            'longest_loss_streak' => 0,
        ];

        $currentWinStreak = 0;
        $currentLossStreak = 0;
        $maxWinStreak = 0;
        $maxLossStreak = 0;

        foreach ($matches as $match) {
            $stats['mp']++;

            // Determine if this is a win or loss
            $isWin = false;
            if ($match['winner'] === $this->player->name || str_contains($match['winner'], $this->player->name)) {
                $isWin = true;
                $stats['w']++;
                $currentWinStreak++;
                $currentLossStreak = 0;
                $maxWinStreak = max($maxWinStreak, $currentWinStreak);
            } else {
                $stats['l']++;
                $currentLossStreak++;
                $currentWinStreak = 0;
                $maxLossStreak = max($maxLossStreak, $currentLossStreak);
            }

            // Count sets
            if (is_array($match['sets'])) {
                foreach ($match['sets'] as $set) {
                    $isHome = $match['home_player_name'] === $this->player->name || str_contains($match['home_player_name'], $this->player->name);
                    $playerScore = $isHome ? $set['home_score'] : $set['away_score'];
                    $opponentScore = $isHome ? $set['away_score'] : $set['home_score'];

                    if ($playerScore > $opponentScore) {
                        $stats['sets_w']++;
                    } else {
                        $stats['sets_l']++;
                    }
                }
            }

            // Track opponents
            $opponent = $match['opponent'];
            if (!isset($stats['opponents'][$opponent])) {
                $stats['opponents'][$opponent] = ['name' => $opponent, 'wins' => 0, 'losses' => 0];
            }

            if ($isWin) {
                $stats['opponents'][$opponent]['wins']++;
            } else {
                $stats['opponents'][$opponent]['losses']++;
            }
        }

        // Set current streaks
        $stats['win_streak'] = $currentWinStreak;
        $stats['loss_streak'] = $currentLossStreak;
        $stats['longest_win_streak'] = $maxWinStreak;
        $stats['longest_loss_streak'] = $maxLossStreak;

        $this->stats = $stats;
    }

    public function render()
    {
        return view('livewire.player-details');
    }
}