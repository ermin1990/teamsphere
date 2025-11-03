<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'is_team_based' => $this->is_team_based,
            'max_teams' => $this->max_teams,
            'max_players_per_team' => $this->max_players_per_team,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'organization' => $this->whenLoaded('organization', function () {
                return [
                    'id' => $this->organization->id,
                    'name' => $this->organization->name,
                    'url_slug' => $this->organization->url_slug,
                ];
            }),
            'sport' => $this->whenLoaded('sport', function () {
                return [
                    'id' => $this->sport->id,
                    'name' => $this->sport->name,
                    'icon' => $this->sport->icon,
                ];
            }),
            'groups_count' => $this->whenCounted('groups'),
            'players_count' => $this->whenCounted('players'),
            'matches_count' => $this->whenCounted('matches'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
