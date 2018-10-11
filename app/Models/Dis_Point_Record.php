<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dis_Point_Record extends Model
{
    protected $primaryKey = "id";
    protected $table = "distribute_point_record";
    public $timestamps = false;

    /**
     * 记录所属得奖人
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Member::class, 'User_ID', 'User_ID');
    }

    /**
     * 更新记录状态
     * $type 1:点位奖，2:重消奖，3:区域代理奖，4:团队奖
     */
    public static function updatePointStatus($Order_ID, $status)
    {
        $count = Dis_Point_Record::where('orderid', $Order_ID)->count('id');
        if ($count > 0) {
            $flag = Dis_Point_Record::where('orderid', $Order_ID)->update(['status' => $status]);
        } else {
            $flag = true;
        }
        return $flag;
    }
}
