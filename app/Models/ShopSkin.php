<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSkin extends Model
{
    protected  $primaryKey = "Skin_ID";
    protected  $table = "shop_skin";
    public $timestamps = false;

    protected $fillable = [
        'Skin_Name', 'Skin_Json', 'Skin_Status', 'Skin_Index'
    ];
}
