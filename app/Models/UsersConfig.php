<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersConfig extends Model
{
    protected  $primaryKey = "Users_ID";
    protected  $table = "users_config";
    public $timestamps = false;

    protected $fillable = [
        'Users_WechatAppId', 'Users_WechatAppSecret','Users_WechatAuth','Users_WechatVoice'
    ];

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

    public function disAreaAgenta() {
        return $this->hasMany('Dis_Agent_Tie', 'Users_ID', 'Users_ID');
    }

    //无需日期转换
    public function getDates()
    {
        return array();
    }

    //获取系统模块
    public function get_dominfo()
    {
        $dominfo = $this->select('domenable', 'domname')->get();

        return $dominfo;
    }
}
