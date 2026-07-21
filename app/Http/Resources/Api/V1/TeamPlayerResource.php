<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamPlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'position' => $this->position,
            'jersey_number' => $this->jersey_number,
            'is_active' => $this->is_active,
            'role' => $this->whenPivotLoaded('team_player', fn () => $this->pivot->role),
        ];
    }
}
