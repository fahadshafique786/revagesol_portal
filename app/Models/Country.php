<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $casts = [
        'country_name' => 'string',
        'country_code' => 'string',
    ];

    protected $fillable = [
        'country_code',
        'country_name',
        'status'
    ];
}
