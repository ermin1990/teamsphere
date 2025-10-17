<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Player;
use App\Models\FriendlyMatch;
use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\Organization;

class PlayerMatchHistory extends Component
{
    public $playerId;
    public $organizationId;
    public $player;
    public $organization;
    public $matches = [];
    public $showOnlyCompleted = true;

    public function mount($playerId, $organizationId = null)
    {
        $this->playerId = $playerId;
        $this->organizationId = $organizationId;
        $this->player = Player::findOrFail($playerId);
        
        if ($organizationId) {
            $this->organization = Organization::findOrFail($organizationId);
        } else {
            $this->organization = $this->player->organization;
        }
        
        $this->loadMatches();
    }

    public function loadMatches()
    {
        $allMatches = [];

        // Fetch friendly matches
        $friendlyMatches = FriendlyMatch::where('organization_id', $this->organization->id)
            ->where(function($query) {
                $query->where('home_player_id', $this->playerId)
                      ->orWhere('away_player_id', $this->playerId)
                      ->orWhereRaw("home_player_name LIKE ?", ["%" . $this->player->name . "%"])
                      ->orWhereRaw("away_player_name LIKE ?", ["%" . $this->player->name . "%"]);
            })
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->get();

        foreach ($friendlyMatches as $match) {
            $isHome = $match->home_player_id == $this->playerId || 
                     (isset($match->home_player_name) && $match->home_player_name == $this->player->name);
            
            $opponent = $isHome ? $match->away_player_name : $match->home_player_name;
            $result = null;
            $isWin = false;
            
            if (is_array($match->sets) && count($match->sets) > 0) {
                $lastSet = $match->sets[array_key_last($match->sets)];
                $playerScore = $isHome ? $lastSet['home_score'] : $lastSet['away_score'];
                $opponentScore = $isHome ? $lastSet['away_score'] : $lastSet['home_score'];
                $result = $playerScore . ' - ' . $opponentScore;
                $isWin = $playerScore > $opponentScore;
            }
            
            $allMatches[] = [
                'id' => $match->id,
                'type' => 'Prijateljski',
                'date' => $match->completed_at,
                'league' => null,
                'league_id' => null,
                'league_slug' => null,
                'opponent' => $opponent,
                'result' => $result,
                'sets' => $match->sets,
                'winner' => $match->winner_name,
                'home_player_name' => $match->home_player_name,
                'away_player_name' => $match->away_player_name,
                'is_win' => $isWin,
                'status' => 'completed'
            ];
        }

        // Fetch league matches
        $leagues = League::where('organization_id', $this->organization->id)->get();
        
        foreach ($leagues as $league) {
            $matchQuery = LeagueMatch::where('league_id', $league->id)
                ->where(function($query) {
                    $query->where('home_player_id', $this->playerId)
                          ->orWhere('away_player_id', $this->playerId);
                });
                
            if ($this->showOnlyCompleted) {
                $matchQuery->whereIn('status', ['completed', 'forfeited'])
                          ->whereNotNull('played_at');
            }
            
            $matches = $matchQuery->orderByDesc('played_at')->get();
            
            foreach ($matches as $m) {
                $isHome = $m->home_player_id == $this->playerId;
                $opponent = $isHome ? 
                    ($m->awayPlayer ? $m->awayPlayer->name : 'Nepoznat igrač') : 
                    ($m->homePlayer ? $m->homePlayer->name : 'Nepoznat igrač');
                
                $result = null;
                $isWin = false;
                
                if ($m->status === 'forfeited') {
                    if ($m->forfeited_by === 'home' && !$isHome) {
                        $result = '0 - 0 (W.O.)';
                        $isWin = true;
                    } elseif ($m->forfeited_by === 'away' && $isHome) {
                        $result = '0 - 0 (W.O.)';
                        $isWin = true;
                    } else {
                        $result = '0 - 0 (L.O.)';
                        $isWin = false;
                    }
                } elseif (is_array($m->sets) && count($m->sets) > 0) {
                    $lastSet = $m->sets[array_key_last($m->sets)];
                    $playerScore = $isHome ? $lastSet['home_score'] : $lastSet['away_score'];
                    $opponentScore = $isHome ? $lastSet['away_score'] : $lastSet['home_score'];
                    $result = $playerScore . ' - ' . $opponentScore;
                    $isWin = $playerScore > $opponentScore;
                } elseif ($m->home_score !== null && $m->away_score !== null) {
                    $playerScore = $isHome ? $m->home_score : $m->away_score;
                    $opponentScore = $isHome ? $m->away_score : $m->home_score;
                    $result = $playerScore . ' - ' . $opponentScore;
                    $isWin = $playerScore > $opponentScore;
                }
                
                $allMatches[] = [
                    'id' => $m->id,
                    'type' => 'Ligaški',
                    'date' => $m->played_at,
                    'league' => $league->name,
                    'league_id' => $league->id,
                    'league_slug' => $league->slug,
                    'opponent' => $opponent,
                    'result' => $result,
                    'sets' => $m->sets,
                    'winner' => optional($m->winner)->name ?? ($isWin ? $this->player->name : $opponent),
                    'home_player_name' => $m->homePlayer ? $m->homePlayer->name : 'Nepoznat',
                    'away_player_name' => $m->awayPlayer ? $m->awayPlayer->name : 'Nepoznat',
                    'is_win' => $isWin,
                    'status' => $m->status
                ];
            }
        }

        // Sort all matches by date
        usort($allMatches, function($a, $b) {
            $dateA = $a['date'] ? strtotime($a['date']) : 0;
            $dateB = $b['date'] ? strtotime($b['date']) : 0;
            return $dateB - $dateA; // Newest first
        });

        $this->matches = $allMatches;
    }

    public function toggleShowOnlyCompleted()
    {
        $this->showOnlyCompleted = !$this->showOnlyCompleted;
        $this->loadMatches();
    }

    public function getMatchUrl($match)
    {
        if ($match['type'] === 'Prijateljski') {
            return route('organizations.friendly-matches.show', [
                'organization' => $this->organization->slug,
                'match' => $match['id']
            ]);
        } else {
            return route('leagues.matches.show', [
                'league' => $match['league_slug'],
                'match' => $match['id']
            ]);
        }
    }

    public function render()
    {
        return view('livewire.player-match-history');
    }
}