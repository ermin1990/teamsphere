<?php

namespace App\Livewire;

use App\Models\Competition;
use App\Models\Standing;
use Livewire\Component;

class LeagueManualStandingsAdjustment extends Component
{
    public $competitionId;

    public function mount($competition)
    {
        $this->competitionId = $competition->id;
    }

    private function orderedStandings()
    {
        return Standing::where('competition_id', $this->competitionId)
            ->whereNull('tournament_group_id')
            ->with('player', 'team')
            ->orderByRaw('CASE WHEN manual_order IS NULL THEN 1 ELSE 0 END ASC, manual_order ASC')
            ->orderByDesc('points')
            ->orderByRaw('(sets_won - sets_lost) desc')
            ->orderByRaw('(points_won - points_lost) desc')
            ->orderByDesc('points_won')
            ->orderByDesc('sets_won')
            ->orderByDesc('won')
            ->orderBy('id')
            ->get();
    }

    /**
     * Persist the current displayed order as everyone's manual_order, so a
     * swap always has explicit values to work with (a mix of null and set
     * manual_order would make "move up/down" ambiguous about what it's
     * swapping against).
     */
    private function commitCurrentOrder()
    {
        $standings = $this->orderedStandings();

        foreach ($standings as $index => $standing) {
            $standing->update(['manual_order' => $index + 1]);
        }

        return $standings;
    }

    public function moveUp($standingId)
    {
        $standings = $this->commitCurrentOrder();
        $index = $standings->search(fn ($s) => $s->id === $standingId);

        if ($index === false || $index === 0) {
            return;
        }

        $current = $standings[$index];
        $above = $standings[$index - 1];

        Standing::where('id', $current->id)->update(['manual_order' => $above->manual_order]);
        Standing::where('id', $above->id)->update(['manual_order' => $current->manual_order]);
    }

    public function moveDown($standingId)
    {
        $standings = $this->commitCurrentOrder();
        $index = $standings->search(fn ($s) => $s->id === $standingId);

        if ($index === false || $index === $standings->count() - 1) {
            return;
        }

        $current = $standings[$index];
        $below = $standings[$index + 1];

        Standing::where('id', $current->id)->update(['manual_order' => $below->manual_order]);
        Standing::where('id', $below->id)->update(['manual_order' => $current->manual_order]);
    }

    public function resetOrder()
    {
        Standing::where('competition_id', $this->competitionId)
            ->whereNull('tournament_group_id')
            ->update(['manual_order' => null]);

        session()->flash('message', 'Ručne pozicije su resetovane - tabela se ponovo računa automatski.');
    }

    public function render()
    {
        return view('livewire.league-manual-standings-adjustment', [
            'standings' => $this->orderedStandings(),
        ]);
    }
}
