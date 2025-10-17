<?php

namespace App\Livewire;

use App\Models\Competition;
use App\Models\Organization;
use App\Models\Player;
use Livewire\Component;

class RemovePlayerFromCompetition extends Component
{
    public Organization $organization;
    public Competition $competition;
    public Player $player;

    public function mount(Organization $organization, Competition $competition, Player $player)
    {
        $this->organization = $organization;
        $this->competition = $competition;
        $this->player = $player;
    }

    public function removePlayer()
    {
        // Ensure user owns this organization
        if ($this->organization->user_id !== auth()->id()) {
            session()->flash('error', 'Nemate dozvolu za ovu akciju.');
            return;
        }

        // Ensure competition belongs to organization
        if ($this->competition->organization_id !== $this->organization->id) {
            session()->flash('error', 'Takmičenje ne pripada ovoj organizaciji.');
            return;
        }

        // Check if player belongs to the organization
        if ($this->player->organization_id !== $this->organization->id) {
            session()->flash('error', 'Igrač ne pripada ovoj organizaciji.');
            return;
        }

        // Check if player is registered
        if (!$this->competition->players->contains($this->player->id)) {
            session()->flash('error', 'Igrač nije registrovan za ovo takmičenje.');
            return;
        }

        // Remove player from competition
        $this->competition->players()->detach($this->player->id);

        session()->flash('success', "Igrač '{$this->player->name}' je uspješno uklonjen iz takmičenja.");

        // Dispatch event to refresh parent components
        $this->dispatch('player-removed');
        $this->dispatch('refreshComponent');
    }

    public function render()
    {
        return view('livewire.remove-player-from-competition');
    }
}
