<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Coupon_Record extends Model
{
    protected $primaryKey = "Record_ID";
    protected $table = "user_coupon_record";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'User_ID', 'Coupon_ID', 'Coupon_UsedTimes', 'Record_CreateTime', 'Coupon_UseArea', 'Coupon_UseType',
        'Coupon_Condition', 'Coupon_Discount', 'Coupon_Cash', 'Coupon_StartTime', 'Coupon_EndTime', 'Biz_ID'
    ];

    protected $hidden = ['Users_ID'];

    //所属优惠券
    public function coupon()
    {
        return $this->belongsTo(User_Coupon::class, 'Coupon_ID', 'Coupon_ID');
    }
}
