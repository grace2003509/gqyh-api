<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dis_Withdraw_Method extends Model
{
    protected  $primaryKey = "Method_ID";
    protected  $table = "distribute_withdraw_method";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID','Method_Name','Method_Type','Method_CreateTime','Status'
    ];
}
