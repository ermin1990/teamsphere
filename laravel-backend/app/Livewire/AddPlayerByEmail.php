<?php

namespace App\Livewire;

use App\Models\League;
use App\Models\Organization;
use App\Models\Player;
use App\Models\User;
use Livewire\Component;

class AddPlayerByEmail extends Component
{
    public $organization;
    public $league;
    public $email = '';
    public $foundUser = null;
    public $foundPlayer = null;
    public $isSearching = false;
    public $searchMessage = '';

    public function mount(Organization $organization, League $league)
    {
        $this->organization = $organization;
        $this->league = $league;
    }

    public function updatedEmail()
    {
        $this->foundUser = null;
        $this->foundPlayer = null;
        $this->searchMessage = '';
        $this->isSearching = true;

        if (empty($this->email)) {
            $this->isSearching = false;
            return;
        }

        // Check if email belongs to a registered user
        $user = User::where('email', $this->email)->first();

        if ($user) {
            $this->foundUser = $user;

            // Check if this user is already a player in this organization
            $existingPlayer = Player::where('organization_id', $this->organization->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingPlayer) {
                $this->foundPlayer = $existingPlayer;

                // Check if player is already in the league
                if ($this->league->players()->where('player_id', $existingPlayer->id)->exists()) {
                    $this->searchMessage = 'Player is already in this league.';
                } else {
                    $this->searchMessage = 'Player found and can be added to the league.';
                }
            } else {
                $this->searchMessage = 'User found but not registered as a player in this organization. You can create a player profile for them.';
            }
        } else {
            $this->searchMessage = 'No user found with this email address.';
        }

        $this->isSearching = false;
    }

    public function addExistingPlayer()
    {
        if (!$this->foundPlayer) {
            return;
        }

        // Check if player is already in the league
        if ($this->league->players()->where('player_id', $this->foundPlayer->id)->exists()) {
            $this->searchMessage = 'Player is already in this league.';
            return;
        }

        // Add player to the league
        $this->league->players()->attach($this->foundPlayer->id, ['joined_at' => now()]);

        $this->searchMessage = 'Player successfully added to the league!';
        $this->foundUser = null;
        $this->foundPlayer = null;
        $this->email = '';

        // Emit event to refresh the parent component
        $this->dispatch('playerAdded');
    }

    public function createAndAddPlayer()
    {
        if (!$this->foundUser) {
            return;
        }

        // Check if user is already a player in this organization
        $existingPlayer = Player::where('organization_id', $this->organization->id)
            ->where('user_id', $this->foundUser->id)
            ->first();

        if ($existingPlayer) {
            $this->addExistingPlayer();
            return;
        }

        // Create new player
        $player = Player::create([
            'name' => $this->foundUser->name,
            'email' => $this->foundUser->email,
            'user_id' => $this->foundUser->id,
            'organization_id' => $this->organization->id,
            'is_active' => true,
        ]);

        // Add player to the league
        $this->league->players()->attach($player->id, ['joined_at' => now()]);

        $this->searchMessage = 'Player profile created and added to the league!';
        $this->foundUser = null;
        $this->foundPlayer = null;
        $this->email = '';

        // Emit event to refresh the parent component
        $this->dispatch('playerAdded');
    }

    public function clearSearch()
    {
        $this->email = '';
        $this->foundUser = null;
        $this->foundPlayer = null;
        $this->searchMessage = '';
        $this->isSearching = false;
    }

    public function render()
    {
        return view('livewire.add-player-by-email');
    }
}
