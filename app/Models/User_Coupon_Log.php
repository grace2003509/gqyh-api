<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Coupon_Log extends Model
{
    protected $primaryKey = "Logs_ID";
    protected $table = "user_coupon_logs";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'User_ID', 'User_Name', 'Coupon_Subject', 'Logs_Price', 'Coupon_UsedTimes', 'Logs_CreateTime',
        'Operator_UserName', 'Biz_ID'
    ];

    protected $hidden = ['Users_ID'];
}
