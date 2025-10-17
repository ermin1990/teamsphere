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

    public function mount($match)
    {
        $this->match = $match;
        $this->loadMatchData();
    }

    public function loadMatchData()
    {
        $this->isUpdating = true;
        
        // Refresh match data from database
        $this->match->refresh();

        $this->homeScore = $this->match->home_score ?? 0;
        $this->awayScore = $this->match->away_score ?? 0;
        $this->sets = $this->match->sets ?? [];
        $this->matchStatus = $this->match->status;
        $this->lastUpdated = now();
        
        $this->isUpdating = false;
    }

    public function render()
    {
        return view('livewire.public-live-score');
    }
}