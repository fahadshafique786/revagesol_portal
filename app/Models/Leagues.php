<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leagues extends Model
{
    use HasFactory;

    protected $casts = [
        'icon' => 'string',
        'sponsorAdClickUrl' => 'string',
        'sponsorAdImageUrl' => 'string',
    ];

    protected $fillable = [
        'icon',
        'name',
        'sports_id',
        'isSponsorAd',
        'sponsorAdClickUrl',
        'sponsorAdImageUrl',
        'start_datetime',
        'end_datetime',
        'is_live',
    ];
}
