<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedules extends Model
{
    use HasFactory;

    protected $casts = [
        'icon' => 'string',
        'sponsorAdClickUrl' => 'string',
        'sponsorAdImageUrl' => 'string',
    ];
    protected $fillable = [
        'scheduleName',
        'sports_id',
        'leagues_id',
        'home_team_id',
        'away_team_id',
        'start_time',
        'end_time',
        'is_live',
        'isSponsorAd',
        'sponsorAdClickUrl',
        'sponsorAdImageUrl',
    ];
}
