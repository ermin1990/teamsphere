<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'logo' => $this->logo,
            'logo_url' => $this->logo_url,
            'logo_src' => $this->logoSrc(),
            'sport_id' => $this->sport_id,
            'is_active' => $this->is_active,
            'user_id' => $this->user_id,
            'is_owner' => $request->user()?->id === $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
