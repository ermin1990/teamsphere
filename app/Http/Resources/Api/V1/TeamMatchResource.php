<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'competition_id' => $this->competition_id,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'home_team' => new TeamResource($this->whenLoaded('homeTeam')),
            'away_team' => new TeamResource($this->whenLoaded('awayTeam')),
            'home_score' => $this->home_score,
            'away_score' => $this->away_score,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at,
            'played_at' => $this->played_at,
            'round' => $this->round,
            'lineup' => $this->lineup,
            'home_captain_id' => $this->home_captain_id,
            'away_captain_id' => $this->away_captain_id,
            'referee_name' => $this->referee_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
