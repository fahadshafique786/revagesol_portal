<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SponsorAds extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_detail_id',
        'adName',
        'adUrlImage',
        'clickAdToGo',
        'isAdShow',
    ];
}
