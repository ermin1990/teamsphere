<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'organization_id' => $this->organization_id,
            'competition_id' => $this->competition_id,
            'captain_id' => $this->captain_id,
            'coach' => $this->coach,
            'status' => $this->status,
            'players_count' => $this->when(isset($this->players_count), $this->players_count),
            'players' => TeamPlayerResource::collection($this->whenLoaded('players')),
            'coaches' => TeamCoachResource::collection($this->whenLoaded('coaches')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
