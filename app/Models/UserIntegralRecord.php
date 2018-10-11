<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserIntegralRecord extends Model
{
    protected  $primaryKey = "Record_ID";
    protected  $table = "user_integral_record";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'User_ID', 'Record_Integral', 'Record_SurplusIntegral', 'Operator_UserName', 'Record_Type',
        'Record_Description', 'Record_CreateTime', 'Action_ID'
    ];
}
