<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerInvitationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'competition_id' => $this->competition_id,
            'organization_id' => $this->organization_id,
            'player_id' => $this->player_id,
            'email' => $this->email,
            'token' => $this->when($request->routeIs('api.v1.organizations.player-invitations.store'), $this->token),
            'invited_by' => $this->invited_by,
            'status' => $this->status,
            'is_expired' => $this->isExpired(),
            'expires_at' => $this->expires_at,
            'accepted_at' => $this->accepted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
