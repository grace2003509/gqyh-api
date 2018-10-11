<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Back_Order_Detail extends Model
{
    protected $table = 'user_back_order_detail';
    protected $primaryKey = 'itemid';
    public $timestamps = false;

    protected $fillable = [
        'backid', 'detail','status','createtime'
    ];
}
