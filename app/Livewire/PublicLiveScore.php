<?php

namespace App\Livewire;

use App\Models\LeagueMatch;
use Livewire\Component;

class PublicLiveScore extends Component
{
    public $match;
    public $homeScore = 0;
    public $awayScore = 0;
    public $currentSetHomeScore = 0;
    public $currentSetAwayScore = 0;
    public $sets = [];
    public $matchStatus = 'scheduled';
    public $lastUpdated;
    public $isUpdating = false;

    public $randomId;

    public function mount($match)
    {
        $this->match = $match;
        $this->randomId = rand(1000, 9999);
        $this->loadMatchData();
    }

    public function loadMatchData()
    {
        $this->isUpdating = true;
        
        // Refresh match data from database with fresh query
        // Handle both LeagueMatch and CompetitionMatch
        if ($this->match instanceof \App\Models\CompetitionMatch) {
            $freshMatch = \App\Models\CompetitionMatch::find($this->match->id);
        } else {
            $freshMatch = \App\Models\LeagueMatch::find($this->match->id);
        }
        
        $this->homeScore = $freshMatch->home_score ?? 0;
        $this->awayScore = $freshMatch->away_score ?? 0;
        $this->currentSetHomeScore = $freshMatch->current_set_home_score ?? 0;
        $this->currentSetAwayScore = $freshMatch->current_set_away_score ?? 0;
        $this->sets = $freshMatch->sets ?? [];
        $this->matchStatus = $freshMatch->status;
        $this->lastUpdated = now();
        $this->randomId = rand(1000, 9999); // Force DOM update
        
        $this->isUpdating = false;
    }

    public function pollMatchData()
    {
        $this->loadMatchData();
    }

    public function render()
    {
        // Determine if this is an individual match or team match
        $isIndividualMatch = $this->match->homePlayer && $this->match->awayPlayer;

        // Get the parent competition/league
        $parent = $this->match->league ?? $this->match->competition;

        return view('livewire.public-live-score', [
            'isIndividualMatch' => $isIndividualMatch,
            'homeScore' => $this->homeScore,
            'awayScore' => $this->awayScore,
            'currentSetHomeScore' => $this->currentSetHomeScore,
            'currentSetAwayScore' => $this->currentSetAwayScore,
            'sets' => $this->sets,
            'matchStatus' => $this->matchStatus,
            'lastUpdated' => $this->lastUpdated,
            'isUpdating' => $this->isUpdating,
            'parent' => $parent,
        ]);
    }
}