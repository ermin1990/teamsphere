<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\FriendlyMatch;

class FriendlyMatchesList extends Component
{
    public $matches;
    public $organizationId = null;

    public function mount($organizationId = null)
    {
        $this->organizationId = $organizationId;
        $query = FriendlyMatch::orderByDesc('completed_at');
        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }
        $this->matches = $query->get();
    }

    public function render()
    {
        return view('livewire.friendly-matches-list');
    }
}
