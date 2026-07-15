<?php

namespace App\Livewire;

use App\Models\Competition;
use App\Models\Organization;
use Livewire\Component;

class ManagePlayers extends Component
{
    public Organization $organization;
    public Competition $competition;

    protected $listeners = [
        'players-added' => '$refresh',
        'player-removed' => '$refresh',
        'refreshComponent' => '$refresh'
    ];

    public function mount(Organization $organization, Competition $competition)
    {
        $this->organization = $organization;
        $this->competition = $competition;
    }

    public function render()
    {
        // Refresh the competition relationship to get updated players
        $this->competition->load('players');

        $pendingJoinRequests = $this->competition->joinRequests()
            ->where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();

        return view('livewire.manage-players', [
            'competition' => $this->competition,
            'organization' => $this->organization,
            'pendingJoinRequests' => $pendingJoinRequests,
        ]);
    }
}