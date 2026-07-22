<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'organization_id',
        'competition_id',
        'user_id',
        'title',
        'body',
        'is_featured',
    ];

    protected $casts = [
        'organization_id' => 'integer',
        'competition_id' => 'integer',
        'user_id' => 'integer',
        'is_featured' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check whether this announcement applies to the whole organization
     * rather than a single competition/league.
     */
    public function isOrganizationWide(): bool
    {
        return $this->competition_id === null;
    }
}
