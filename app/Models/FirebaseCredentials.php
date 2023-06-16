<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirebaseCredentials extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'app_detail_id',
        'apps_url',
        'leagues_url',
        'schedules_url',
        'servers_url',
        'app_setting_url',
        'reCaptchaKeyId',
        'notificationKey',
        'firebaseConfigJson',
    ];


}
