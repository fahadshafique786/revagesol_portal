<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teams extends Model
{
    use HasFactory;

//    protected $casts = [
//        'id' => 'string',
//        'sports_id' => 'string',
//        'leagues_id' => 'string',
//        'points' => 'string',
//    ];


    protected $fillable = [
        'icon',
        'name',
        'sports_id',
        'leagues_id',
        'points',
    ];
}
