<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Config extends Model
{
    protected $primaryKey = "Users_ID";
    protected $table = "user_config";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'BusinessName', 'IsSign', 'SignIntegral', 'BusinessPhone', 'Address', 'UserLevel', 'CardName',
        'CardLogo', 'CardStyle', 'CardStyleCustom', 'ProStartime', 'ProIntegral', 'IsPro', 'ProRstart', 'CustomImgPath',
        'PrimaryLng', 'PrimaryLat', 'ExpireTime'
    ];

}
