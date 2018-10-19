<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Operator extends Model
{
    protected  $primaryKey = "Operator_ID";
    protected  $table = "user_operator";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'Operator_UserName', 'Operator_Password'
    ];

    protected $hidden = ['Users_ID'];
}
