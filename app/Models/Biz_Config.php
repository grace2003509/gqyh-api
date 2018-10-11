<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biz_Config extends Model
{
    protected $primaryKey = "ItemID";
    protected $table = "biz_config";
    public $timestamps = false;

    protected $fillable = ['Users_ID', 'BaoZhengJin', 'NianFei', 'JieSuan', 'year_fee',
        'bond_desc', 'bannerimg', 'join_desc', 'mobile_desc'];


}
