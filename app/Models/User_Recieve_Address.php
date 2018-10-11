<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Recieve_Address extends Model
{
    protected  $primaryKey = "Users_ID";
    protected  $table = "user_recieve_address";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'RecieveProvince', 'RecieveCity', 'RecieveArea', 'RecieveAddress', 'RecieveName', 'RecieveMobile'
    ];
}
