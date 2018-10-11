<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMoneyRecord extends Model
{
    protected $table = 'user_money_record';
    protected $primaryKey = 'Item_ID';
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'User_ID', 'Type', 'Amount', 'Total', 'Note', 'CreateTime'
    ];

}
