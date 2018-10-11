<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biz_Union_Home extends Model
{
    protected $table = 'biz_union_home';
    protected $primaryKey = 'Home_ID';
    public $timestamps = false;

    protected $fillable = ['Users_ID', 'Home_Json'];


}
