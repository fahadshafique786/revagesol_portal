<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmobAds extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_detail_id',
        'adName',
        'adUId',
        'isAdShow',
    ];
}
