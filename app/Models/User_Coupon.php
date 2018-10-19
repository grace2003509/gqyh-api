<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Coupon extends Model
{
    protected $primaryKey = "Coupon_ID";
    protected $table = "user_coupon";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'Coupon_Keywords', 'Coupon_Title', 'Coupon_Subject', 'Coupon_PhotoPath','Coupon_UsedTimes',
        'Coupon_UserLevel', 'Coupon_StartTime', 'Coupon_EndTime', 'Coupon_Description', 'Coupon_CreateTime',
        'Coupon_UseArea', 'Coupon_UseType', 'Coupon_Condition', 'Coupon_Discount', 'Coupon_Cash', 'Biz_ID',
        'Is_Delete', 'Is_Deletes'
    ];

    protected $hidden = ['Users_ID'];
}
