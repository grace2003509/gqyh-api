<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Address extends Model
{
    protected  $primaryKey = "Address_ID";
    protected  $table = "user_address";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'User_ID', 'Address_Name', 'Address_Mobile', 'Address_Province', 'Address_City', 'Address_Area',
        'Address_Detailed', 'Address_Is_Default', 'Address_TrueName', 'Address_Certificate'
    ];

    protected $hidden = ['Users_ID'];

}
