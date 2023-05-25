<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerTypes extends Model
{
    protected $casts = [
        'label' => 'string',
        'name' => 'string',
    ];

    protected $fillable = [
        'label',
        'name',
        'status',
    ];


}
