<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    const PLACEMENT_TAKMICENJA = 'takmicenja_sidebar';
    const PLACEMENT_LEAGUE = 'league_sidebar';
    const PLACEMENT_ORGANIZATION = 'organization_sidebar';

    const PLACEMENTS = [
        self::PLACEMENT_TAKMICENJA => 'Desno na /takmicenja',
        self::PLACEMENT_ORGANIZATION => 'Desno na stranici organizacije',
        self::PLACEMENT_LEAGUE => 'Desno na stranici lige',
    ];

    protected $fillable = [
        'title',
        'image_path',
        'image_url',
        'link_url',
        'placements',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'placements' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForPlacement($query, string $placement)
    {
        return $query->whereJsonContains('placements', $placement)->orderBy('sort_order');
    }

    /**
     * The actual image URL to render - an uploaded file (stored on the
     * public disk) takes precedence over a pasted external URL.
     */
    public function imageSrc(): string
    {
        return $this->image_path ? \Illuminate\Support\Facades\Storage::url($this->image_path) : $this->image_url;
    }

    public function placementLabels(): string
    {
        return collect($this->placements ?? [])
            ->map(fn ($p) => self::PLACEMENTS[$p] ?? $p)
            ->implode(', ');
    }
}
