<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'city_id' => $this->city_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'address' => $this->address,
            'description' => $this->description,
            'logo' => $this->logoSrc(),
            'contact_email' => $this->contact_email,
            'phone' => $this->phone,
            'website' => $this->website,
            'city' => new CityResource($this->whenLoaded('city')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
