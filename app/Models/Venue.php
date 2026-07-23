<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_id',
        'name',
        'slug',
        'address',
        'description',
        'logo',
        'logo_url',
        'contact_email',
        'phone',
        'website',
    ];

    /**
     * The actual logo URL to render - an uploaded file (stored on the
     * public disk, in the `logo` column) takes precedence over a pasted
     * external URL (`logo_url`).
     */
    public function logoSrc(): ?string
    {
        return $this->logo ? \Illuminate\Support\Facades\Storage::url($this->logo) : $this->logo_url;
    }

    /**
     * Get the user that owns/manages this venue.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Tournament matches (CompetitionMatch) played at this venue.
     */
    public function tournamentMatches(): HasMany
    {
        return $this->hasMany(CompetitionMatch::class, 'venue_id');
    }

    /**
     * League matches (LeagueMatch, table `matches`) played at this venue.
     */
    public function leagueMatches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class, 'venue_id');
    }
}
