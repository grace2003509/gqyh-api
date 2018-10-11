<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopConfig extends Model
{
    protected  $primaryKey = "Users_ID";
    protected  $table = "shop_config";
    public $timestamps = false;


    public function get_one($fields='*'){
        $r = $this->select($fields)->find(USERSID);
        return $r;
    }


    public function set_config($data){
        $flag = $this->where('Users_ID', USERSID)->update($data);
        return $flag;
    }

}
