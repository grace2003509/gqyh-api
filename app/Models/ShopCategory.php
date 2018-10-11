<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCategory extends Model
{
    protected $table = 'shop_category';
    protected $primaryKey = 'Category_ID';
    public $timestamps = false;

    protected $fillable = [
        'Users_ID','Category_Index','Category_Name','Category_ParentID','Category_ListTypeID',
        'Category_Img','Category_IndexShow','Category_Bond','Category_CommissionRate','Category_ProfitRate'
    ];
}
