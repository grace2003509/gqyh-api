<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSalesRecord extends Model
{
    protected $table = 'shop_sales_record';
    protected $primaryKey = 'Record_ID';
    public $timestamps = false;

    protected $fillable = [
        'Record_ID','Users_ID','Order_ID','Biz_ID','Order_Amount','Order_Diff','Order_Shipping','Order_TotalPrice',
        'Order_Json','Bonus','Record_Status','Record_CreateTime','Payment_ID','Finance_Type','Finance_Rate','Web_Price'
    ];
}
