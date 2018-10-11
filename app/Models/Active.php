<?php
/**
 * 活动表
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Active extends Model
{
    protected $table = 'active';
    protected $primaryKey = 'Active_ID';
    public $timestamps = false;

    protected $fillable = [
        'Type_ID', 'Active_Name', 'Users_ID', 'MaxGoodsCount', 'MaxBizCount', 'BizGoodsCount', 'IndexBizGoodsCount',
        'IndexShowGoodsCount', 'ListShowGoodsCount', 'BizShowGoodsCount', 'imgurl', 'Status', 'addtime',
        'starttime', 'stoptime'
    ];


    public function biz_actives()
    {
        return $this->hasMany(Biz_Active::class, 'Active_ID', 'Active_ID');
    }
}
