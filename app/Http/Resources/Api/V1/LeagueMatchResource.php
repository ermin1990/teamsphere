<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeagueMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'competition_id' => $this->competition_id,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'home_player_id' => $this->home_player_id,
            'away_player_id' => $this->away_player_id,
            'home_captain_id' => $this->home_captain_id,
            'away_captain_id' => $this->away_captain_id,
            'home_score' => $this->home_score,
            'away_score' => $this->away_score,
            'scheduled_at' => $this->scheduled_at,
            'played_at' => $this->played_at,
            'status' => $this->status,
            'round' => $this->round,
            'sets' => $this->sets,
            'forfeited_by' => $this->forfeited_by,
            'table_id' => $this->table_id,
            'venue_id' => $this->venue_id,
            'referee_user_id' => $this->referee_user_id,
            'referee_name' => $this->referee_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
