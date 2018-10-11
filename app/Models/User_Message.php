<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Message extends Model
{
    protected $primaryKey = "Message_ID";
    protected $table = "user_message";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'Message_Title', 'Message_StartTime', 'Message_EndTime', 'Message_Description',
        'Message_CreateTime', 'User_ID'
    ];
}
