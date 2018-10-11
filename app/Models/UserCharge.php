<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCharge extends Model
{
    protected  $primaryKey = "Item_ID";
    protected  $table = "user_charge";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'User_ID', 'Amount', 'Total', 'Operator', 'Status', 'CreateTime'
    ];
}
