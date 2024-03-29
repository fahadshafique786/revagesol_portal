<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleHasAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'account_id',
        'application_id',
    ];

}
