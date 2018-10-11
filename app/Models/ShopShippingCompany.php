<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopShippingCompany extends Model
{
    protected $table = 'shop_shipping_company';
    protected $primaryKey = 'Shipping_ID';
    public $timestamps = false;

    protected $fillable = [
        'Shipping_ID','Users_ID', 'Shipping_Code', 'Shipping_Name', 'Shipping_business','Cur_Template',
        'Shipping_Des', 'Shipping_Status', 'Shipping_CreateTime','Biz_ID'
    ];

}
