<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'is_admin' => (bool) $this->is_admin,
            'email_verified_at' => $this->email_verified_at,
            'has_google_login' => filled($this->google_id),
            'player_profile' => $this->whenLoaded('playerProfile', fn () => $this->playerProfile ? [
                'id' => $this->playerProfile->id,
                'organization_id' => $this->playerProfile->organization_id,
                'name' => $this->playerProfile->name,
            ] : null),
            'created_at' => $this->created_at,
        ];
    }
}
