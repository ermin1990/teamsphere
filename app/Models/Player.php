<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'user_id',
        'organization_id',
        'date_of_birth',
        'position',
        'jersey_number',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that this player represents (if registered).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the organization this player belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope a query to only include active players.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include registered players.
     */
    public function scopeRegistered($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope a query to only include unregistered players.
     */
    public function scopeUnregistered($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Check if player is registered user.
     */
    public function isRegistered(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Get player's age.
     */
    public function getAge(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Get player's display name.
     */
    public function getDisplayName(): string
    {
        if ($this->jersey_number) {
            return $this->name . ' #' . $this->jersey_number;
        }
        return $this->name;
    }
}
