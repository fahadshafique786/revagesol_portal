<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleHasApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'account_id',
        'application_id',
    ];

}
