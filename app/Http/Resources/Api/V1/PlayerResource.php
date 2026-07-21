<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->getDisplayName(),
            'email' => $this->email,
            'user_id' => $this->user_id,
            'is_registered' => $this->isRegistered(),
            'organization_id' => $this->organization_id,
            'date_of_birth' => $this->date_of_birth,
            'age' => $this->getAge(),
            'position' => $this->position,
            'jersey_number' => $this->jersey_number,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
