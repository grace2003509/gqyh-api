<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Web_Skin extends Model
{
    protected  $primaryKey = "Skin_ID";
    protected  $table = "web_skin";
    public $timestamps = false;

    protected $fillable = ['Skin_Name','Skin_ImgPath','Trade_ID','Skin_Json','Skin_Status','Skin_Index'];
}
