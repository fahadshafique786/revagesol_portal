<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerHeaders extends Model
{
    use HasFactory;

    protected $casts = [
        'key_name' => 'string',
        'key_value' => 'string',
    ];

    protected $fillable = [
        'server_id',
        'key_name',
        'key_value'
    ];

}
