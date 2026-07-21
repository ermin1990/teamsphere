<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StandingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'competition_id' => $this->competition_id,
            'tournament_group_id' => $this->tournament_group_id,
            'team_id' => $this->team_id,
            'player_id' => $this->player_id,
            'participant_name' => $this->participant_name,
            'played' => $this->played,
            'won' => $this->won,
            'drawn' => $this->drawn,
            'lost' => $this->lost,
            'points' => $this->points,
            'sets_won' => $this->sets_won,
            'sets_lost' => $this->sets_lost,
            'points_won' => $this->points_won,
            'points_lost' => $this->points_lost,
            'goals_for' => $this->goals_for,
            'goals_against' => $this->goals_against,
            'goal_difference' => $this->goal_difference,
            'position' => $this->position,
            'manual_order' => $this->manual_order,
        ];
    }
}
