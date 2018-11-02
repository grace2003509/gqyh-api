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


    /**
     * 我的团队累计销售额,只识别门槛商品
     * @params UsersID string 对应管理员的唯一标识
     * @params UserID int 分销商的UserID
     * @params posterity object 根据分销商ID查询出的分销商对象,存储的是distribute_account表的部分字段
     * @param time int 0代表累计销售额,1代表当月销售额
     * @return float 返回销售额金额
     */
    function get_my_leiji_vip_sales($UserID, $posterity, $time = 0, $type = 0)
    {
        $uo_obj = new UserOrder();
        //当月第一天时间戳
        $first_date_str = date('Y-m-01', time());
        $first_date_unix = strtotime($first_date_str);
        //当月最后一天时间戳
        $end_date_unix = strtotime("$first_date_str +1 month -1 day");
        if ($time == 1) {
            $uo_obj = $uo_obj->where('Order_CreateTime', '>', $first_date_unix)
                ->where('Order_CreateTime', '<', $end_date_unix);
        }

        $total_sales = 0;

        //计算本店下属分销商作为用户所购买门槛商品销售额
        $posterityids = array();
        if (count($posterity) > 0) {
            $posterityids = $posterity->map(function ($node) {
                return $node->User_ID;
            })->toArray();
        }
        $posterityids[] = $UserID;

        //获取所有门槛商品
        $dl_obj = new Dis_Level();
        $dis_arr = $dl_obj->where('Level_LimitType', 2)->where('Level_ID','<', 3)->get();
        $all_vip_productid_arr = array();
        foreach($dis_arr as $k => $v){
            $ids_arr = explode('|', $v['Level_LimitValue']);
            if($ids_arr[0] == 1){
                $all_vip_productid_arr[] = $ids_arr[1];
            }
        }
        $all_vip_productid_str = implode(',', $all_vip_productid_arr);
        $all_vip_productid = explode(',', $all_vip_productid_str);

        //获取爵位计入状态设置
        if($type == 1){
            $dc_obj = new Dis_Config();
            $status = $dc_obj->select('Pro_Title_Status')->find(1);
        }
        if(isset($status) && $status['Pro_Title_Status'] == 4){
            $uo_obj = $uo_obj->where('Order_Status', 4);
        }else{
            $uo_obj = $uo_obj->where('Order_Status', '>=', 2);
        }

        $r2 = $uo_obj->select('Order_CartList')
            ->where('Is_Backup', 0)
            ->whereIn('Owner_ID', $posterityids)
            ->get();
        $money = 0;
        foreach($r2 as $k => $v){
            $cart_list = json_decode($v['Order_CartList'], true);
            foreach($cart_list as $key => $value){
                if(count($all_vip_productid) > 0 && in_array($key, $all_vip_productid)){
                    $money += $value[0]['ProductsPriceX'] * $value[0]['Qty'];
                }
            }
        }

        $total_sales = $total_sales + $money;

        return $total_sales;

    }

}