<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProduct extends Model
{
    protected  $primaryKey = "Products_ID";
    protected  $table = "shop_products";
    public $timestamps = false;
}
