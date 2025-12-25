<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamCoach extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
