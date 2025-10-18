<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Player;
use App\Models\Standing;

class ManageKnockoutPlayers extends Component
{
    public $competition;
    public $organization;
    public $selectedPlayers = []; // Array of match_id => ['home_player_id', 'away_player_id']
    public $availablePlayers = []; // Array of available players for selection

    public function mount(Competition $competition, $organization)
    {
        $this->competition = $competition;
        $this->organization = $organization;

        // Load existing selected players
        $knockoutMatches = CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->get();

        foreach ($knockoutMatches as $match) {
            $this->selectedPlayers[$match->id] = [
                'home_player_id' => $match->home_player_id,
                'away_player_id' => $match->away_player_id,
            ];
        }

        $this->loadAvailablePlayers();
    }

    public function loadAvailablePlayers()
    {
        // Get players from group standings for first round, or from previous rounds
        $this->availablePlayers = collect();

        $knockoutMatches = CompetitionMatch::where('competition_id', $this->competition->id)
            ->where('phase', 'knockout')
            ->orderBy('round_number')
            ->get()
            ->groupBy('round_number');

        foreach ($knockoutMatches as $roundNumber => $roundMatches) {
            if ($roundNumber === 1) {
                // First round: get advancing players from groups
                if ($this->competition->tournamentGroups) {
                    foreach ($this->competition->tournamentGroups as $group) {
                        $standings = Standing::where('competition_id', $this->competition->id)
                            ->where('tournament_group_id', $group->id)
                            ->orderBy('points', 'desc')
                            ->orderByRaw('(sets_won - sets_lost) desc')
                            ->limit($this->competition->players_advancing_per_group ?? 2)
                            ->get();

                        foreach ($standings as $standing) {
                            if ($standing->player) {
                                $this->availablePlayers->push($standing->player);
                            }
                        }
                    }
                }
            } else {
                // Later rounds: get players from previous round winners
                $prevMatches = $knockoutMatches->get($roundNumber - 1) ?? collect();
                foreach ($prevMatches as $prevMatch) {
                    if ($prevMatch->homePlayer) $this->availablePlayers->push($prevMatch->homePlayer);
                    if ($prevMatch->awayPlayer) $this->availablePlayers->push($prevMatch->awayPlayer);
                }
            }
        }

        $this->availablePlayers = $this->availablePlayers->unique('id')->values();
    }

    public function updatePlayer($matchId, $playerType, $playerId)
    {
        if (!isset($this->selectedPlayers[$matchId])) {
            $this->selectedPlayers[$matchId] = ['home_player_id' => null, 'away_player_id' => null];
        }

        $this->selectedPlayers[$matchId][$playerType . '_player_id'] = $playerId ?: null;
    }

    public function confirmPlayers($matchId)
    {
        if (!isset($this->selectedPlayers[$matchId])) {
            session()->flash('error', 'Nema odabranih igrača za ovu utakmicu.');
            return;
        }

        $match = CompetitionMatch::find($matchId);
        if (!$match || $match->competition_id !== $this->competition->id) {
            session()->flash('error', 'Utakmica nije pronađena.');
            return;
        }

        $homePlayerId = $this->selectedPlayers[$matchId]['home_player_id'];
        $awayPlayerId = $this->selectedPlayers[$matchId]['away_player_id'];

        // Validate that players exist and are different
        if ($homePlayerId && $awayPlayerId && $homePlayerId === $awayPlayerId) {
            session()->flash('error', 'Igrači moraju biti različiti.');
            return;
        }

        // Update the match
        $match->update([
            'home_player_id' => $homePlayerId,
            'away_player_id' => $awayPlayerId,
        ]);

        session()->flash('success', 'Igrači su uspješno potvrđeni za utakmicu #' . $matchId);
    }

    public function render()
    {
        $knockoutMatches = CompetitionMatch::where('competition_id', $this->competition->id)
            ->where('phase', 'knockout')
            ->with(['homePlayer', 'awayPlayer'])
            ->orderBy('round_number')
            ->orderBy('id')
            ->get()
            ->groupBy('round_number');

        return view('livewire.manage-knockout-players', [
            'knockoutMatches' => $knockoutMatches,
        ]);
    }
}
