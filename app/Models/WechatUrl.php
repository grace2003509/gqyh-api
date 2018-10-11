<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WechatUrl extends Model
{
    protected $table = 'wechat_url';
    protected $primaryKey = 'Url_ID';
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'Url_ID', 'Url_Name', 'Url_Value'
    ];
}
