<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dis_Account_Message extends Model
{
    protected $primaryKey = "Mes_ID";
    protected $table = "distribute_account_message";
    public $timestamps = false;

    protected $fillable = ['UsersID', 'User_ID', 'Receiver_User_ID', 'Mess_Content', 'Mess_Status','Mess_CreateTime'];

    protected $hidden = ['UsersID'];


    //发送信息用户
    public function send_user()
    {
        return $this->belongsTo(Member::class, 'User_ID', 'User_ID');
    }

}
