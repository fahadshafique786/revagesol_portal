<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchedulesApps extends Model
{
    protected $fillable = [
        'schedule_id',
        'application_id',
    ];
}
