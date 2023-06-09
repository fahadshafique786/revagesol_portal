<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppCredentials extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_detail_id',
        'package_id',
        'server_auth_key',
        'appSigningKey',
        'versionCode',
        'stream_key',
        'token_key'
    ];

}
