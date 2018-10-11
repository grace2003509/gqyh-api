<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biz_Skin extends Model
{
    protected $primaryKey = "Skin_ID";
    protected $table = "biz_skin";
    public $timestamps = false;

    protected $fillable = ['Users_ID', 'Skin_Name', 'Skin_ImgPath', 'Skin_Json'];
}
