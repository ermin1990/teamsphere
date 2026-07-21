<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'competition_id' => $this->competition_id,
            'name' => $this->name,
            'group_number' => $this->group_number,
            'player_ids' => $this->player_ids,
            'is_completed' => $this->is_completed,
            'completed_at' => $this->completed_at,
            'standings' => StandingResource::collection($this->whenLoaded('standings')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
