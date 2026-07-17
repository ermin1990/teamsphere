<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'billing_period',
        'max_organizations',
        'max_leagues_per_organization',
        'max_competitions_per_organization',
        'max_teams_per_league',
        'max_players_per_team',
        'is_active',
        'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_organizations' => 'integer',
        'max_leagues_per_organization' => 'integer',
        'max_competitions_per_organization' => 'integer',
        'max_teams_per_league' => 'integer',
        'max_players_per_team' => 'integer',
        'is_active' => 'boolean',
        'features' => 'array',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Scope a query to only include active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the user plans for this plan.
     */
    public function userPlans()
    {
        return $this->hasMany(UserPlan::class);
    }
    public function isFree()
    {
        return $this->price == 0;
    }

    /**
     * Bosnian label for the billing period suffix, e.g. "/god" or "/mj".
     */
    public function getBillingPeriodLabelAttribute()
    {
        return $this->billing_period === 'yearly' ? 'god' : 'mj';
    }

    /**
     * Local display label for the currency code, e.g. "BAM" -> "KM".
     */
    public function getCurrencyLabelAttribute()
    {
        return match ($this->currency) {
            'BAM' => 'KM',
            default => $this->currency,
        };
    }

    /**
     * Get formatted price, e.g. "299.00 KM/god" or "Besplatno".
     */
    public function getFormattedPriceAttribute()
    {
        if ($this->isFree()) {
            return __('Besplatno');
        }

        return number_format($this->price, 2) . ' ' . $this->currency_label . '/' . $this->billing_period_label;
    }
}
