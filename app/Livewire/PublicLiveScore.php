<?php

namespace App\Livewire;

use App\Models\LeagueMatch;
use Livewire\Component;

class PublicLiveScore extends Component
{
    public $match;
    public $homeScore = 0;
    public $awayScore = 0;
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
        $freshMatch = \App\Models\LeagueMatch::find($this->match->id);
        
        $this->homeScore = $freshMatch->home_score ?? 0;
        $this->awayScore = $freshMatch->away_score ?? 0;
        $this->sets = $freshMatch->sets ?? [];
        $this->matchStatus = $freshMatch->status;
        $this->lastUpdated = now();
        $this->randomId = rand(1000, 9999); // Force DOM update
        
        $this->isUpdating = false;
    }

    public function render()
    {
        return view('livewire.public-live-score');
    }
}