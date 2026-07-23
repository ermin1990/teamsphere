<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Flat mobile standings row. player_id/player_name work for team-based
 * competitions too - Standing::participant(_name) resolves team-or-player,
 * unlike StandingResource which exposes the full organizer-facing shape with
 * separate team_id/player_id columns.
 */
class PlayerStandingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $setDifference = ($this->sets_won ?? 0) - ($this->sets_lost ?? 0);

        return [
            'rank' => $this->position,
            'player_id' => $this->player_id,
            'player_name' => $this->participant_name,
            'played' => $this->played,
            'wins' => $this->won,
            'losses' => $this->lost,
            'set_difference' => $setDifference > 0 ? "+{$setDifference}" : (string) $setDifference,
            'points' => $this->points,
        ];
    }
}
