<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Minimal player info safe to expose to other participants of a competition
 * (e.g. the opponent picker when logging a match) - no email/date_of_birth.
 */
class PlayerSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->getDisplayName(),
        ];
    }
}
