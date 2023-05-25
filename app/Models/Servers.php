<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servers extends Model
{
    use HasFactory;


    protected $casts = [
        'sponsorAdClickUrl' => 'string',
        'sponsorAdImageUrl' => 'string',
    ];

    protected $fillable = [
        'sports_id',
        'leagues_id',
        'server_type_id',
        'name',
        'link',
        'isHeader',
        'isPremium',
        'isTokenAdded',
        'isIpAddressApiCall',
        'isSponsorAd',
        'sponsorAdClickUrl',
        'sponsorAdImageUrl',
    ];


}
