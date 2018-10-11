<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopHome extends Model
{
    protected  $primaryKey = "Home_ID";
    protected  $table = "shop_home";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID','Skin_ID','Home_Json'
    ];
}
