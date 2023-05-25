<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;

    protected $casts = [
        'image' => 'string',
    ];

    protected $fillable = [
        'sports_id',
        'app_detail_id',
        'title',
        'message',
        'image',
        'schedule_datetime',
        'status',
        'isSponsorAd',
        'sponsorAdClickUrl',
        'sponsorAdImageUrl',
    ];

    public function additionalInfo(){
        return $this->hasMany(NotificationAdditionalInfo::class , 'push_notification_id','id');
    }
}
