<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dis_Withdraw_Record extends Model
{
    protected  $primaryKey = "Record_ID";
    protected  $table = "distribute_withdraw_record";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID','User_ID','Method_Name','Method_Account','Method_No','Method_Bank','Record_Total','Record_Fee',
        'Record_Yue','Record_Money','Record_Status','No_Record_Desc','Record_CreateTime','Record_SendID','Record_SendTime',
        'Record_WxID','Record_SendType'
    ];


    public function user()
    {
        return $this->belongsTo(Member::class, 'User_ID', 'User_ID');
    }
}
