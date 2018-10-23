<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Back_Order extends Model
{
    protected $table = 'user_back_order';
    protected $primaryKey = 'Back_ID';
    public $timestamps = false;

    protected $fillable = [
        'Back_Sn','Users_ID', 'Biz_ID', 'Order_ID', 'User_ID','Owner_ID','Back_Type','Back_Json','Back_Shipping',
        'Back_ShippingID','Back_Status','Back_CreateTime', 'Biz_IsRead', 'Buyer_IsRead','Back_Qty','Back_Amount',
        'Back_CartID','ProductID','Back_UpdateTime','Back_IsCheck','Back_Account','Order_Status','Order_FromID',
        'Sales_By','DistributeAccount_ID','Pro_skuvaljosn','batch_no','Back_Integral','Back_Amount_Source','Back_Coin'
    ];

    protected $hidden = ['Users_ID'];

    //退货单所属的订单
    public function order()
    {
        return $this->belongsTo(UserOrder::class, 'Order_ID', 'Order_ID');
    }


    public function details()
    {
        return $this->hasMany(User_Back_Order_Detail::class, 'backid', 'Back_ID');
    }
}
