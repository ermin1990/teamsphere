<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'location' => $this->location,
            'organizer_contact' => $this->organizer_contact,
            'entry_fee' => $this->entry_fee,
            'organization_id' => $this->organization_id,
            'sport_id' => $this->sport_id,
            'category_id' => $this->category_id,
            'season_id' => $this->season_id,
            'city_id' => $this->city_id,
            'registration_deadline' => $this->registration_deadline,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'max_teams' => $this->max_teams,
            'is_team_based' => $this->is_team_based,
            'is_double_round' => $this->is_double_round,
            'settings' => $this->settings,
            'is_active' => $this->is_active,
            'is_public' => $this->is_public,
            'registration_open' => $this->registration_open,
            'is_recreational' => $this->is_recreational,
            'allow_rematches' => $this->allow_rematches,
            // Tournament fields
            'type' => $this->type,
            'max_participants' => $this->max_participants,
            'group_count' => $this->group_count,
            'players_per_group' => $this->players_per_group,
            'players_advancing_per_group' => $this->players_advancing_per_group,
            'group_rounds' => $this->group_rounds,
            'advancement_method' => $this->advancement_method,
            'current_phase' => $this->current_phase,
            'knockout_bracket' => $this->knockout_bracket,
            'groups_completed_at' => $this->groups_completed_at,
            'knockout_completed_at' => $this->knockout_completed_at,
            // Match settings
            'sets_to_win' => $this->sets_to_win,
            'points_per_set' => $this->points_per_set,
            'deuce_at' => $this->deuce_at,
            'must_win_by_two' => $this->must_win_by_two,
            'points_for_win' => $this->points_for_win,
            'points_for_draw' => $this->points_for_draw,
            'points_for_loss' => $this->points_for_loss,
            'has_tiebreak' => $this->has_tiebreak,
            'tiebreak_points' => $this->tiebreak_points,
            'manual_knockout_selection' => $this->manual_knockout_selection,
            // Relations / counts (only present when eager loaded)
            'organization' => new OrganizationResource($this->whenLoaded('organization')),
            'players' => PlayerSummaryResource::collection($this->whenLoaded('players')),
            'players_count' => $this->whenCounted('players'),
            'matches_count' => $this->whenCounted('matches'),
            'groups_count' => $this->whenCounted('tournamentGroups'),
            'is_member' => $this->when(!is_null($this->is_member), fn () => (bool) $this->is_member),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
