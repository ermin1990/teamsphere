<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes a row from the shared `matches` table - used for both
 * LeagueMatch and CompetitionMatch model instances since they're the same
 * table/columns under two Eloquent classes.
 */
class CompetitionMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'competition_id' => $this->competition_id,
            'team_match_id' => $this->team_match_id,
            'position_code' => $this->position_code,
            'match_order' => $this->match_order,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'home_player_id' => $this->home_player_id,
            'away_player_id' => $this->away_player_id,
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
            // Tournament fields
            'phase' => $this->phase,
            'tournament_group_id' => $this->tournament_group_id,
            'round_number' => $this->round_number,
            'bracket_position' => $this->bracket_position,
            'is_bye' => $this->is_bye,
            // Player group info
            'home_player_group' => $this->home_player_group,
            'home_player_position' => $this->home_player_position,
            'away_player_group' => $this->away_player_group,
            'away_player_position' => $this->away_player_position,
            'home_captain_id' => $this->home_captain_id,
            'away_captain_id' => $this->away_captain_id,
            // Relations (only when eager loaded)
            'home_player' => $this->whenLoaded('homePlayer'),
            'away_player' => $this->whenLoaded('awayPlayer'),
            'home_team' => $this->whenLoaded('homeTeam'),
            'away_team' => $this->whenLoaded('awayTeam'),
            'venue' => $this->whenLoaded('venue'),
            'competition' => new CompetitionResource($this->whenLoaded('competition')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
