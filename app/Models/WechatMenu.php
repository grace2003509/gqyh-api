<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WechatMenu extends Model
{
    protected $table = 'wechat_menu';
    protected $primaryKey = 'Menu_ID';
    public $timestamps = false;

    protected $fillable = [
        'Menu_ID', 'Menu_Index', 'Users_ID', 'Menu_Name', 'Menu_ParentID',
        'Menu_MsgType','Menu_TextContents','Menu_MaterialID','Menu_Url'
    ];
}
