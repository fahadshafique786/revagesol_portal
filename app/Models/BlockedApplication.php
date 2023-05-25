<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'country_id',
    ];

}
