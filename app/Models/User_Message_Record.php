<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Message_Record extends Model
{
    protected $primaryKey = "Record_ID";
    protected $table = "user_message_record";
    public $timestamps = false;

    protected $fillable = [ 'Users_ID', 'User_ID', 'Message_ID', 'Record_CreateTime'];

}
