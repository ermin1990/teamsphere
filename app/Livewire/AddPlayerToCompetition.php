<?php

namespace App\Livewire;

use App\Models\Competition;
use App\Models\Organization;
use App\Models\Player;
use Livewire\Component;

class AddPlayerToCompetition extends Component
{
    public Organization $organization;
    public Competition $competition;

    public string $search = '';
    public array $selectedPlayers = [];
    public bool $showNewPlayerForm = false;

    // New player form fields
    public string $newPlayerName = '';
    public string $newPlayerEmail = '';

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount(Organization $organization, Competition $competition)
    {
        $this->organization = $organization;
        $this->competition = $competition;
    }

    public function updatedSearch()
    {
        $this->selectedPlayers = []; // Reset selection when search changes
    }

    public function updatedSelectedPlayers()
    {
        // This method is called when selectedPlayers array changes
        // We can add any additional logic here if needed
    }

    public function getAvailablePlayersProperty()
    {
        $currentPlayerIds = $this->competition->players()->pluck('players.id');

        $query = $this->organization->players()
            ->whereNotIn('id', $currentPlayerIds);

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate(20);
    }

    public function addSelectedPlayers()
    {
        if (empty($this->selectedPlayers)) {
            session()->flash('error', 'Please select at least one player.');
            return;
        }

        $addedCount = 0;
        $errors = [];

        // Get current players in competition (fresh query)
        $currentPlayerIds = $this->competition->players()->pluck('players.id')->toArray();

        foreach ($this->selectedPlayers as $playerId) {
            try {
                $player = Player::findOrFail($playerId);

                // Check if player belongs to the organization
                if ($player->organization_id !== $this->organization->id) {
                    $errors[] = "{$player->name} does not belong to this organization.";
                    continue;
                }

                // Check if player is already registered
                if (in_array($player->id, $currentPlayerIds)) {
                    $errors[] = "{$player->name} is already registered for this competition.";
                    continue;
                }

                // Check max participants limit for tournaments
                if ($this->competition->isTournament() && $this->competition->max_participants) {
                    $currentCount = $this->competition->players()->count();
                    if ($currentCount >= $this->competition->max_participants) {
                        $errors[] = 'Maximum number of participants reached.';
                        break;
                    }
                }

                $this->competition->players()->attach($player->id);
                $addedCount++;
                $currentPlayerIds[] = $player->id; // Add to current list to prevent duplicates in same batch

            } catch (\Exception $e) {
                $errors[] = "Error adding player: " . $e->getMessage();
            }
        }

        $this->selectedPlayers = [];
        $this->search = '';

        if ($addedCount > 0) {
            session()->flash('success', "Successfully added {$addedCount} player(s) to the competition.");
            $this->dispatch('players-added'); // Dispatch event to refresh parent components
        }

        if (!empty($errors)) {
            session()->flash('error', implode('<br>', $errors));
        }

        $this->dispatch('refreshComponent');
    }

    public function createNewPlayer()
    {
        $this->validate([
            'newPlayerName' => 'required|string|max:255',
            'newPlayerEmail' => 'nullable|email|unique:players,email',
        ]);

        try {
            $player = Player::create([
                'name' => $this->newPlayerName,
                'email' => $this->newPlayerEmail,
                'organization_id' => $this->organization->id,
                'user_id' => null, // Not linked to a user account yet
            ]);

            // Check max participants limit for tournaments
            if ($this->competition->isTournament() && $this->competition->max_participants) {
                $currentCount = $this->competition->players()->count();
                if ($currentCount >= $this->competition->max_participants) {
                    session()->flash('error', 'Maximum number of participants reached.');
                    $player->delete(); // Remove the created player
                    return;
                }
            }

            $this->competition->players()->attach($player->id);

            $this->newPlayerName = '';
            $this->newPlayerEmail = '';
            $this->showNewPlayerForm = false;

            session()->flash('success', "Player '{$player->name}' created and added to the competition.");
            $this->dispatch('players-added'); // Dispatch event to refresh parent components
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            session()->flash('error', 'Error creating player: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.add-player-to-competition', [
            'availablePlayers' => $this->availablePlayers,
        ]);
    }
}
