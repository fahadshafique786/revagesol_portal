<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sports extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

//    protected $casts = [
//        'id' => 'string',
//    ];


    protected $fillable = [
        'icon',
        'name',
        'sports_type',
        'image_required',
        'multi_league',
    ];

}
