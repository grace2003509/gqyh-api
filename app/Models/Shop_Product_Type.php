<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop_Product_Type extends Model
{
    protected  $primaryKey = "Type_ID";
    protected  $table = "shop_product_type";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'Type_Index', 'Type_Name', 'Attr_Group', 'Status', 'attrnum', 'Biz_ID'
    ];
}
