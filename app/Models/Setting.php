<?php
/**
 * 系统基本设置表
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected  $primaryKey = "id";
    protected  $table = "setting";
    public $timestamps = false;

    protected $fillable = ['sys_name','sys_logo','sys_copyright','sys_baidukey','sys_dommain'];

    //
    public function set_homejson_array($array){
        $data = array();
        if (empty($array)) {
            return 110;
        }
        foreach($array as $value){
            if(is_array($value['Title'])){
                $value['Title'] = json_encode($value['Title']);
            }
            if(is_array($value['ImgPath'])){
                $value['ImgPath'] = json_encode($value['ImgPath']);
            }
            if(is_array($value['Url'])){
                $value['Url'] = json_encode($value['Url']);
            }
            $data[] = $value;
        }
        return $data;
    }
}
