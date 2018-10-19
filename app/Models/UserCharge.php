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

    protected $hidden = ['Users_ID'];

    //所属用户
    public function user()
    {
        return $this->belongsTo(Member::class, 'User_ID', 'User_ID');
    }
}
