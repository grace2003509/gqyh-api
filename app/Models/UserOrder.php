<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOrder extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'user_order';
    protected $primaryKey = 'Order_ID';
    public $timestamps = false;

    protected $fillable = [
        'Users_ID','User_ID','Order_ID','Order_Type','Address_Name','Address_Mobile','Address_Province','Address_City',
        'Address_Area','Address_Detailed','Address_TrueName','Address_Certificate','Order_Remark','Order_Shipping',
        'Order_ShippingID','Order_CartList','Order_TotalPrice','order_coin','Order_CreateTime','Order_DefautlPaymentMethod',
        'Order_PaymentMethod','Order_PaymentInfo','Order_Status','Order_IsRead','Coupon_ID','Coupon_Discount','Coupon_Cash',
        'Order_TotalAmount','Owner_ID','Is_Commit','Is_Backup','Order_Code','Order_IsVirtual','Integral_Consumption',
        'Integral_Money','Integral_Get','Message_Notice','Order_IsRecieve','deleted_at','Biz_ID','Order_NeedInvoice',
        'Order_InvoiceInfo','Back_Amount','Order_SendTime','Order_Virtual_Cards','Front_Order_Status',
        'transaction_id','Is_Factorage','Web_Price','Web_Pricejs','curagio_money','Back_Integral','muilti','Is_Backup_js',
        'addtype','All_Qty','Is_User_Distribute','Back_salems','back_qty','back_qty_str','Back_Amount_Source',
        'cash_str','Web_Pricejs_new','store_mention','store_mention_time'
    ];


    /*一个订单属于一个用户*/
    public function User()
    {
        return $this->belongsTo(Member::class, 'User_ID', 'User_ID');
    }

    /**
     * 订单所属店铺
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function biz()
    {
        return $this->belongsTo(Biz::class, 'Biz_ID', 'Biz_ID');
    }

    /*一个订单对应多条分销记录*/
    public function disRecord()
    {
        return $this->hasMany(Dis_Record::class, 'Order_ID', 'Order_ID');
    }

    /*一个订单拥有多条分销账号记录*/
    public function disAccountRecord()
    {
        return $this->hasManyThrough(Dis_Account_Record::class, Dis_Record::class, 'Order_ID', 'Ds_Record_ID');
    }

    /**
     * 一个订单包含多个点位奖记录
     */
    public function pointRecord()
    {
        return $this->hasMany(Dis_Point_Record::class, 'orderid', 'Order_ID');
    }

    /**
     * 一个订单包含一个退货/退款单
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function backOrder()
    {
        return $this->hasOne(User_Back_Order::class, 'Order_ID', 'Order_ID');
    }

    /**
     * 确认收货
     * @param  $Order_ID 指定订单的ID
     * @return bool  $flag 确认收货操作是否成功
     */
    public function confirmReceive($orderid)
    {
        $flag_a = $this->where('Order_ID', $orderid)->update(['Order_Status' => 4]);
        $this->fireModelEvent('confirmed', false);

        return $flag_a;
    }

    /**
     * 统计订单信息
     * @param  string $Users_ID 店铺唯一标识
     * @param  string $type 统计类型 num订单数目  sales 订单销售额
     * @param  int $begin_time 开始时间戳
     * @param  int $end_time 结束时间戳
     * @return int  $res           返回结果
     */
    public function statistics($type, $begin_time, $end_time, $Order_Status = '')
    {

        $builder = $this->whereBetween('Order_CreateTime', [$begin_time, $end_time]);

        if (strlen($Order_Status) > 0) {
            $builder->where('Order_Status', '>=', $Order_Status);
        }

        if ($type == 'num') {
            $res = $builder->count();
        } else {
            $res = $builder->sum('Order_TotalAmount');
        }

        return $res;
    }
    /*edit数据统计20160408--start--*/
    /**
     * 统计订单信息
     * @param  string $Users_ID 店铺唯一标识
     * @param  string $type 统计类型 num订单数目  sales 订单销售额
     * @param  int $begin_time 开始时间戳
     * @param  int $end_time 结束时间戳
     * @return int  $res           返回结果
     */
    public function statistics2($type, $begin_time, $end_time, $Order_Status = '')
    {

        $builder = $this->whereBetween('Order_CreateTime', [$begin_time, $end_time]);

        if (strlen($Order_Status) > 0) {
            $builder->where('Order_Status', '=', $Order_Status);
        }

        if ($type == 'num') {
            $res = $builder->count();
        } else {
            $res = $builder->sum('Order_TotalAmount');
        }

        return $res;
    }

    public function statistics3($type, $begin_time, $end_time, $Order_Status = '')
    {
        $builder = $this->whereBetween('Order_CreateTime', [$begin_time, $end_time]);

        if (strlen($Order_Status) > 0) {
            $builder->where('Is_Backup', '=', $Order_Status);
        }

        if ($type == 'num') {
            $res = $builder->count();
        } else {
            $res = $builder->sum('Back_Amount');
        }

        return $res;
    }
    /*edit数据统计20160408--end--*/


    /**
     * 指定时间内的订单
     * @param  $Users_ID 店铺唯一标识
     * @param  $Begin_Time 开始时间
     * @param  $End_Time 结束时间
     * @return array 订单列表
     */
    public function ordersBetween($Begin_Time, $End_Time, $Order_Status)
    {
        if ($Order_Status != 'all') {
            $builder = $this->where('Order_Status', $Order_Status);
        }

        $builder->whereBetween('Order_CreateTime', [$Begin_Time, $End_Time])
            ->orderBy('Order_CreateTime', 'desc');

        return $builder;
    }


    //生成进账记录信息
    public function order_input_record($Begin_Time, $End_Time)
    {

        $fields = array('Order_ID', 'Order_CreateTime', 'Order_TotalAmount', 'Order_Status', 'Order_IsVirtual');

        $input_record_builder = $this->ordersBetween($Begin_Time, $End_Time, 4);
        $paginate_obj = $input_record_builder->paginate(5, $fields);

        $res = array(
            'sum' => $input_record_builder->sum('Order_TotalAmount'),
            'input_paginate' => $paginate_obj,
            'total_pages' => $paginate_obj->lastPage()
        );

        return $res;

    }


    //获取订单编号
    public function getorderno($oid)
    {
        $rsOrder = $this->select('Order_Type','Order_CreateTime','Order_Code')
            ->where('Order_ID',$oid)
            ->first();
        if (!$rsOrder) {
            return false;
        }
        if($rsOrder['Order_Type'] == 'pintuan' || $rsOrder['Order_Type'] == 'dangou'){
            $orderno = $rsOrder['Order_Code'];
        }else{
            $orderno = date("Ymd", $rsOrder["Order_CreateTime"]) . $oid;
        }
        return $orderno;
    }

    //获取虚拟订单消费券码
    public function get_virtual_confirm_code($orderid)
    {
        for($i = 0; $i <= 1; $i++) {
            $temchars = virtual_randcode(10);
            $r = $this->where('Order_Code', $temchars)->find($orderid);
            $i = $r ? 0 : 1;
        }
        return $temchars;
    }


    public function update_order($orderid,$data)
    {
        $this->where('Order_ID', $orderid)->update($data);
    }


    /**
     * 团队累计销售额
     * @param $UsersID
     * @param $UserID
     * @param $posterity
     * @param int $time
     * @param int $type
     * @return float|int
     */
    public function get_my_leiji_vip_sales($UserID, $posterity, $time = 0, $type = 0)
    {
        $dc_obj = new Dis_Config();
        $dl_obj = new Dis_Level();

        $obj = $this->where('Is_Backup', 0);

        //当月第一天时间戳
        $first_date_str = date('Y-m-01', time());
        $first_date_unix = strtotime($first_date_str);
        //当月最后一天时间戳
        $end_date_unix = strtotime("$first_date_str +1 month -1 day");
        $time_where = '';
        if ($time == 1) {
            $obj = $obj->where('Order_CreateTime', '>=', $first_date_unix);
            $obj = $obj->where('Order_CreateTime', '<=', $end_date_unix);
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
        $dis_arr = $dl_obj->where('Level_LimitType', 2)
            ->where('Level_ID','<', 3)
            ->get();
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
            $status = $dc_obj->select('Pro_Title_Status')->first();
        }
        if(isset($status) && $status['Pro_Title_Status'] == 4){
            $obj = $obj->where('Order_Status', 4);
        }else{
            $obj = $obj->where('Order_Status', '>=', 2);
        }

        $r2 = $obj->select('Order_CartList')->whereIn('Owner_ID', $posterityids);
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
