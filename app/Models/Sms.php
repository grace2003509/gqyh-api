<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected  $primaryKey = "itemid";
    protected  $table = "sms";
    public $timestamps = false;

    protected $fillable = [
        'mobile', 'message', 'word', 'sendtime', 'code', 'usersid'
    ];
}
