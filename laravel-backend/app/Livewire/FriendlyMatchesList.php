<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\FriendlyMatch;

class FriendlyMatchesList extends Component
{
    public $matches;
    public $organizationId = null;
    public $organization;
    public $limit = null;

    public function mount($organizationId = null, $organization = null, $limit = null)
    {
        $this->organizationId = $organizationId;
        $this->organization = $organization;
        $this->limit = $limit;

        $query = FriendlyMatch::orderByDesc('completed_at');
        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }
        if ($limit) {
            $query->limit($limit);
        }
        $this->matches = $query->get();
    }

    public function render()
    {
        return view('livewire.friendly-matches-list');
    }
}
