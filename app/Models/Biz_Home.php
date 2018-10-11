<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biz_Home extends Model
{
    protected $primaryKey = "Home_ID";
    protected $table = "biz_home";
    public $timestamps = false;

    protected $fillable = ['Users_ID', 'Home_Json', 'Biz_ID', 'Skin_ID'];
}
