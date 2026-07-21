<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendlyMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'home_player_id' => $this->home_player_id,
            'away_player_id' => $this->away_player_id,
            'home_player_name' => $this->home_player_name,
            'away_player_name' => $this->away_player_name,
            'sets' => $this->sets,
            'set_durations' => $this->set_durations,
            'winner_name' => $this->winner_name,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
