<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class, 'table_id');
    }

    public function leagueMatches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class, 'table_id');
    }

    public function competitionMatches(): HasMany
    {
        return $this->hasMany(CompetitionMatch::class, 'table_id');
    }
}
