<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent_Order extends Model
{
    protected $table = 'agent_order';
    protected $primaryKey = 'Order_ID';
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'User_ID', 'Applyfor_Name', 'Applyfor_Mobile', 'Applyfor_WeixinID', 'Order_PaymentMethod',
        'Order_PaymentInfo', 'Order_TotalPrice', 'Owner_ID', 'Order_PayTime', 'Order_PayID', 'ProvinceId', 'CityId',
        'AreaId', 'Level_ID', 'Level_Name', 'Order_Status', 'Refuse_Be', 'Order_CreateTime', 'Area', 'AreaMark', 'Area_Concat'
    ];
}
