<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Favourite_Products extends Model
{
    protected $primaryKey = "FAVOURITE_ID";
    protected $table = "user_favourite_products";
    public $timestamps = false;

    protected $fillable = [
        'FAVOURITE_ID', 'User_ID', 'Products_ID', 'Is_Attention', 'MID'
    ];


    //所属商品
    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'Products_ID', 'Products_ID');
    }
}
