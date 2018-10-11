<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReplyConfig extends Model
{
    protected $table = 'wechat_attention_reply';
    protected $primaryKey = 'Reply_ID';
    public $timestamps = false;

    protected $fillable = [
        'Reply_ID', 'Users_ID', 'Reply_MsgType', 'Reply_TextContents', 'Reply_MaterialID',
        'Reply_Subscribe','Reply_MemberNotice'
    ];


}
