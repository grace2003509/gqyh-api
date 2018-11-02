<?php
/**
 * 分销账户记录Model
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Capsule\Manager as Capsule;


class Dis_Account_Record extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $primaryKey = "Record_ID";
    protected $table = "distribute_account_record";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID','Ds_Record_ID','User_ID','level','Record_Sn','Account_Info','Record_Qty','Record_Price','Record_Money',
        'Record_Description','Record_Type','Record_Status','Record_CreateTime','deleted_at','Owner_ID','CartID','skustr',
        'skukey','yformat'
    ];

    protected  $hidden = ['Users_ID'];

    //一个佣金获得记录属于一个分销记录
    public function DisRecord()
    {
        return $this->belongsTo(Dis_Record::class, 'Ds_Record_ID', 'Record_ID');
    }

    /*一条佣金分销记录属于一个用户*/
    public function User()
    {
        return $this->belongsTo(Member::class, 'User_ID', 'User_ID');
    }


    /**
     * 指定时间内分销佣金合计
     * @param  string $Users_ID 本店唯一ID
     * @param  int $Begin_Time 开始时间
     * @param  int $End_Time 结束视
     * @param  int $Status 佣金状态
     * @return float  $sum     佣金合计数额
     */
    public function recordMoneySum($Begin_Time, $End_Time, $Record_Status = '')
    {
        $builder = $this->whereBetween('Record_CreateTime', [$Begin_Time, $End_Time]);
        if (strlen($Record_Status) > 0) {
            //$builder->where('Record_Status', $Record_Status);
            $builder->where('Record_Status', '>=', $Record_Status);
        }

        $sum = $builder->sum('Record_Money');

        return $sum;
    }


    /**
     * 指定时间内的记录
     * @param  $Users_ID 店铺唯一标识
     * @param  $Begin_Time 开始时间
     * @param  $End_Time 结束时间
     * @return array 订单列表
     */
    public function recordBetween($Begin_Time, $End_Time, $Record_Status)
    {
        $builder = $this::with('Admin');

        if ($Record_Status != 'all') {
            $builder = $builder->where('Record_Status', $Record_Status);
        }

        $builder->whereBetween('Record_CreateTime', [$Begin_Time, $End_Time])
            ->orderBy('Record_CreateTime', 'desc');

        return $builder;
    }

    /**
     * 通过订单ID更改分销账号记录
     * @param  int $orderID 订单ID
     * @param  int $Status 分销账号记录的状态
     * @return bool $flag  是否更改成功
     */
    public static function changeStatusByOrderID($OrderID, $Status)
    {

        $order = UserOrder::Find($OrderID);
        $disAccountRecord = $order->disAccountRecord();
        $flag = true;
        if ($disAccountRecord->count() > 0) {
            $flag = $disAccountRecord->rawUpdate(array('Record_Status' => $Status));
        }
        return $flag;
    }


    /**
     *获取我的累计佣金收入
     */
    public function get_my_leiji_income($UserID)
    {
        $total = $this->where(['User_ID' => $UserID, 'Record_Type' => 0])
            ->where('Record_Status', '>=', 1)
            ->sum('Record_Money');
        $total_income = floor($total * 100) / 100;
        return $total_income;
    }

}