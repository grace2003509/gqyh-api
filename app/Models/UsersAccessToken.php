<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersAccessToken extends Model
{
    protected $table = 'users_access_token';
    protected $primaryKey = 'itemid';
    public $timestamps = false;

    protected $fillable = [
        'itemid', 'usersid', 'access_token', 'expires_in', 'jssdk_ticket', 'jssdk_expires_in'
    ];
}
