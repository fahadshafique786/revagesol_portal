<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationAdditionalInfo extends Model
{
    use HasFactory;

    protected $casts = [
        'key_name' => 'string',
        'key_value' => 'string',
    ];

    protected $fillable = [
        'push_notification_id',
        'key_name',
        'key_value'
    ];

    public function pushNotifications(){
        return $this->hasOne(PushNotification::class , 'push_notification_id','id');
    }
}
