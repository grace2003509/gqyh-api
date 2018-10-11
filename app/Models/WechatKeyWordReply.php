<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WechatKeyWordReply extends Model
{
    protected $table = 'wechat_keyword_reply';
    protected $primaryKey = 'Reply_ID';
    public $timestamps = false;

    protected $fillable = [
        'Reply_ID','Users_ID','Reply_Table','Reply_TableID','Reply_Display','Reply_Keywords','Reply_PatternMethod',
        'Reply_MsgType','Reply_TextContents','Reply_MaterialID','Reply_CreateTime'
    ];


    public function get_one($itemid)
    {
        $r = $this->find($itemid);
        return $r;
    }

    public function get_one_bymodel($model)
    {
        $r = $this->where('Reply_Table', $model)
            ->where('Reply_TableID', 0)
            ->where('Reply_Display', 0)
            ->first();
        $r["Reply_Keywords"] = trim($r["Reply_Keywords"],"|");
        return $r;
    }

    public function edit($data,$itemid)
    {
        $flag = $this->where('Reply_ID', $itemid)->update($data);
        return $flag;
    }

    public function add($data)
    {
        $data["Reply_Keywords"] = $this->set($data["Reply_Keywords"]);
        $rst = $this->create($data);
        return $rst['Reply_ID'];
    }


    public function get_list($fields = "*")
    {
        $lists = $this->select($fields)
            ->where('Reply_Display', 1)
            ->orderBy('Reply_ID', 'desc')
            ->paginate(20);
        foreach($lists as $k => $r){
            $r["Reply_Keywords"] = trim($r["Reply_Keywords"],"|");
        }
        return $lists;
    }

    private function set($kw){
        /*
            $html = '';
            $arr = $arr_temp = array();
            $arr = explode("|",$kw);
            foreach($arr as $a){
                if(!$a) continue;
                $arr_temp[] = $a;
            }
            $html = '|'.implode("|",$arr_temp).'|';
            return $html;
        */
        return $kw;
    }
}
