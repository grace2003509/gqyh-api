<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSalesPayment extends Model
{
    protected $table = 'shop_sales_payment';
    protected $primaryKey = 'Payment_ID';
    public $timestamps = false;

    protected $fillable = [
        'Payment_Sn','Users_ID','Biz_ID','FromTime','EndTime','Amount','Diff','Web','Bonus','Total','Bank','BankNo',
        'BankName','BankMobile','Status','CreateTime','Payment_Type','OpenID','aliPayNo','aliPayName','Msg',
        'Shipping'
    ];
}
