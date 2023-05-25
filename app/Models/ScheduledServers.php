<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledServers extends Model
{
    use HasFactory;


    protected $fillable = [
        'server_id',
        'schedule_id',
        'connection_time',
//        'sports_id',
    ];



}
