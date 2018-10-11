<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Order_Commit extends Model
{
    protected  $primaryKey = "Item_ID";
    protected  $table = "user_order_commit";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'Biz_ID', 'User_ID', 'MID', 'Order_ID', 'Product_ID', 'Score', 'Note', 'CreateTime', 'Status'
    ];

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'Product_ID', 'Products_ID');
    }
}
