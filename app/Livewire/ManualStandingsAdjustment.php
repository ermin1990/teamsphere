<?php

namespace App\Livewire;

use App\Models\Competition;
use App\Models\Standing;
use Livewire\Component;

class ManualStandingsAdjustment extends Component
{
    public $competitionId;
    public $groupId;
    public $standingsOrder = [];

    public function mount($competition, $group)
    {
        $this->competitionId = $competition->id;
        $this->groupId = $group->id;
        $this->loadStandings();
    }

    public function loadStandings()
    {
        $standings = Standing::where('competition_id', $this->competitionId)
            ->where('tournament_group_id', $this->groupId)
            ->with('player')
            ->orderByRaw('CASE WHEN manual_order IS NULL THEN 1 ELSE 0 END ASC, manual_order ASC')
            ->orderBy('points', 'desc')
            ->orderByRaw('(sets_won - sets_lost) desc')
            ->orderByRaw('(points_won - points_lost) desc')
            ->orderByDesc('points_won')
            ->orderByDesc('sets_won')
            ->orderByDesc('won')
            ->orderBy('id')
            ->get();

        $this->standingsOrder = $standings->pluck('id')->toArray();
    }

    public function updateOrder($orderedIds)
    {
        $this->standingsOrder = $orderedIds;
    }

    public function saveOrder()
    {
        foreach ($this->standingsOrder as $index => $standingId) {
            Standing::where('id', $standingId)->update(['manual_order' => $index + 1]);
        }

        // Clear manual_order for standings not in the list (if any removed, but shouldn't)
        Standing::where('competition_id', $this->competitionId)
            ->where('tournament_group_id', $this->groupId)
            ->whereNotIn('id', $this->standingsOrder)
            ->update(['manual_order' => null]);

        session()->flash('message', 'Pozicije su uspješno ažurirane.');
        $this->loadStandings(); // Reload
    }

    public function resetOrder()
    {
        Standing::where('competition_id', $this->competitionId)
            ->where('tournament_group_id', $this->groupId)
            ->update(['manual_order' => null]);

        $this->loadStandings();
        session()->flash('message', 'Ručne pozicije su resetovane.');
    }

    public function render()
    {
        $standings = Standing::whereIn('id', $this->standingsOrder)
            ->with('player')
            ->orderByRaw('FIELD(id, ' . implode(',', $this->standingsOrder) . ')')
            ->get();

        return view('livewire.manual-standings-adjustment', compact('standings'));
    }
}
