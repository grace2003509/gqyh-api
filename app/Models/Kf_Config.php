<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kf_Config extends Model
{
    protected  $primaryKey = "KF_ID";
    protected  $table = "kf_config";
    public $timestamps = false;

    protected $fillable = [
        'KF_IsWeb','KF_IsShop','KF_IsUser','KF_kanjia','KF_Icon','Wx_keyword','qq_postion','qq_icon',
        'qq','KF_Code','kftype','KF_Link'
    ];
}
