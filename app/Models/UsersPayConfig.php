<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersPayConfig extends Model
{
    protected  $primaryKey = "Users_ID";
    protected  $table = "users_payconfig";
    public $timestamps = false;


    // 多where
    public function scopeMultiwhere($query, $arr)
    {
        if (!is_array($arr)) {
            return $query;
        }

        foreach ($arr as $key => $value) {
            $query = $query->where($key, $value);
        }
        return $query;
    }


    //无需日期转换
    public function getDates()
    {
        return array();
    }
}
