<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Web_Home extends Model
{
    protected  $primaryKey = "Home_ID";
    protected  $table = "web_home";
    public $timestamps = false;

    protected $fillable = ['Users_ID','Skin_ID','Home_Json'];

}
