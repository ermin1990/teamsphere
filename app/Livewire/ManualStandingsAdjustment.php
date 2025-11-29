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

        $this->standingsOrder = $standings->map(function($standing, $index) {
            return [
                'id' => $standing->id,
                'position' => $standing->manual_order ?? ($index + 1)
            ];
        })->toArray();
    }

    public function saveOrder()
    {
        // Sort by entered position
        $ordered = collect($this->standingsOrder)->sortBy('position')->values();
        
        foreach ($ordered as $index => $item) {
            Standing::where('id', $item['id'])->update(['manual_order' => $index + 1]);
        }

        session()->flash('message', 'Pozicije su uspješno ažurirane.');
        $this->loadStandings();
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
        $standingIds = collect($this->standingsOrder)->pluck('id')->toArray();
        
        $standings = Standing::whereIn('id', $standingIds)
            ->with('player')
            ->get()
            ->sortBy(function($standing) use ($standingIds) {
                return array_search($standing->id, $standingIds);
            })
            ->values();

        return view('livewire.manual-standings-adjustment', compact('standings'));
    }
}
