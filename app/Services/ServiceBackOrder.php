<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/28
 * Time: 16:13
 */

namespace App\Services;

use App\Models\ShopProduct;
use App\Models\User_Back_Order;
use App\Models\User_Back_Order_Detail;
use App\Models\UserOrder;

class ServiceBackOrder
{
    //申请退货
    public function add_backup($rsOrder, $productid, $cartid, $qty, $reason, $account){
        $orderid = $rsOrder["Order_ID"];
        $allQty = $rsOrder["All_Qty"];
        $CartList=json_decode(htmlspecialchars_decode($rsOrder["Order_CartList"]),true);
        $ShippingList=json_decode(htmlspecialchars_decode($rsOrder["Order_Shipping"]),true);
        $item = $CartList[$productid][$cartid];

        $sp_obj = new ShopProduct();
        $ubo_obj = new User_Back_Order();
        $uo_obj = new UserOrder();

        $product = $sp_obj->select('Products_IsVirtual')->find(intval($productid));
        //虚拟产品物流费用为0
        if ($product['Products_IsVirtual'] == 1) {
            $ShippingMoney = 0;
        } else {
            $ShippingMoney = $ShippingList['Price'];

        }
        unset($product);

        $item["Qty"] = $qty;
        if(!empty($item['Property']) && isset($item['Property']['shu_pricesimp'])){
            $ProductsPriceX = $item['Property']['shu_pricesimp'];
        }else{
            $ProductsPriceX = $item['ProductsPriceX'];
        }

        //获得更新的产品的属性库存，保存到退货表
        foreach ($CartList as $Product_ID=>$Product_List) {
            foreach ($Product_List as $k => $v) {
                if (!empty($v['Property'])) {
                    //修改产品属性的库存
                    $rsProduct = $sp_obj->find($Product_ID);
                    //产品属性详情
                    $rsProduct['skuvaljosn'] = json_decode($rsProduct['skuvaljosn'],true);
                    //获得加入购买产品的属性skuID 如：[1;4;5]
                    $atrid_str = $k;
                    //加上退货的件数，剩余多少库存
                    foreach($rsProduct['skuvaljosn'] as $key=>$value){
                        if($atrid_str == $key){
                            $rsProduct['skuvaljosn'][$key]['Txt_CountSon'] += $v["Qty"];
                        }
                    }
                    $skuvaljosn = json_encode($rsProduct['skuvaljosn'],JSON_UNESCAPED_UNICODE);
                    //更新产品表属性JSON数据
                    /*$rsProduct['skuvaljosn'] = $skuvaljosn;
                    $rsProduct->save();*/
                } else {
                    $skuvaljosn = '';
                }
            }
        }

        if($rsOrder['Order_Status'] >= 2){    //已付款
            $cpyCarList = $CartList;
            $cpyCarList[$productid][$cartid]["Qty"] = $cpyCarList[$productid][$cartid]["Qty"] - $qty;
            if($cpyCarList[$productid][$cartid]["Qty"]==0){
                unset($cpyCarList[$productid][$cartid]);
            }
            if(count($cpyCarList[$productid])==0){
                unset($cpyCarList[$productid]);
            }
            $amount = $qty * $ProductsPriceX;
            $amount = isset($user_curagio) && !empty($user_curagio) ? $amount * (1 - $user_curagio / 100) : $amount;
            $amount = round_pad_zero($amount, 2);
            if(empty($cpyCarList)){
                $amount += $ShippingMoney;
            }
        }else{    //已发货
            $amount = $qty * $ProductsPriceX;
        }
        $amount_source = $qty * $ProductsPriceX;
        $Integral_Money = 0;
        $back_coin = 0;
        if(empty($CartList)){
            if(!empty((int)$rsOrder['Coupon_Cash'])){
                $amount -= $rsOrder['Coupon_Cash'];
            }
            if(!empty((int)$rsOrder['Integral_Money'])){
                $amount -= $rsOrder['Integral_Money'];
            }
            $back_coin = $rsOrder['order_coin'];

        } else {
            if(!empty((int)$rsOrder['Coupon_Cash'])){
                if (!empty($rsOrder['cash_str'])) {
                    $backqtyarr = json_decode($rsOrder['cash_str'],true);
                    if (isset($backqtyarr['cash_str'][$productid][$cartid])) {
                        $curpro_cash = $backqtyarr['cash_str'][$productid][$cartid]['cash_str'] / $backqtyarr['cash_str'][$productid][$cartid]['Qty'] * $qty;
                    }
                } else {
                    $curpro_cash = 0;
                }
                $amount -= $curpro_cash;
            }
            if(!empty((int)$rsOrder['Integral_Money'])){
                $Integral_Money = $rsOrder['Integral_Money'] / $allQty * $qty;
                $amount -= $Integral_Money;
            }
            if (!empty($rsOrder['order_coin'])) {
                $back_coin = $rsOrder['order_coin'] / $allQty * $qty;
            }
        }

        $time = time();
        $data = array(
            'Back_SN' => $this->build_order_no(),
            'Users_ID'=>USERSID,
            'Biz_ID'=>$rsOrder["Biz_ID"],
            'Order_ID'=>$orderid,
            'User_ID'=>$rsOrder["User_ID"],
            'Owner_ID'=>$rsOrder["Owner_ID"],
            'Back_Type'=>'shop',
            'Back_Json'=>json_encode($item,JSON_UNESCAPED_UNICODE),
            'Back_Status'=>0,
            'Back_CreateTime'=>$time,
            'Back_Qty'=>$qty,
            'Back_CartID'=>$cartid,
            'Back_Integral' => $Integral_Money,
            "Back_Coin" => $back_coin,
            'Back_Amount'=>$amount,
            'Back_Amount_Source'=>$amount_source,
            'Back_Account'=>$account,
            'ProductID'=>$productid,
            'Pro_skuvaljosn'=>$skuvaljosn
        );
        $record = $ubo_obj->create($data);

        $detail = "买家申请退款，退款金额：".($amount)."，退款原因：".$reason;
        //增加退款流程记录
        $this->add_record($record['Back_ID'],0,$detail,$time);

        $CartList[$productid][$cartid]["Qty"] = $CartList[$productid][$cartid]["Qty"] - $qty;
        if($CartList[$productid][$cartid]["Qty"]==0){
            unset($CartList[$productid][$cartid]);
        }
        if(count($CartList[$productid])==0){
            unset($CartList[$productid]);
        }

        if (empty($rsOrder['back_qty_str'])) {
            $backqtyarr = array();
            $backqtyarr[$productid][$cartid] = $qty;
        } else {
            $backqtyarr = json_decode($rsOrder['back_qty_str'],true);
            if (isset($backqtyarr[$productid][$cartid])) {
                $backqtyarr[$productid][$cartid] = $backqtyarr[$productid][$cartid] + $qty;
            } else {
                $backqtyarr[$productid][$cartid] = $qty;
            }
        }

        if(!empty($CartList)){
            if($rsOrder['Order_Status'] == 2){
                $data = array(
                    'Order_Status'=>2,
                    'Front_Order_Status'=>2,
                    'Is_Backup'=>1,
                    'Order_CartList'=>json_encode($CartList,JSON_UNESCAPED_UNICODE),
                    'Back_salems'=>'',
                    'back_qty_str'=>json_encode($backqtyarr,JSON_UNESCAPED_UNICODE),
                    'Back_Integral' => $rsOrder["Back_Integral"] + $Integral_Money,
                    'back_qty' => $rsOrder["back_qty"] + $qty,
                    'Back_Amount'=>$rsOrder["Back_Amount"]+$amount,
                    'Back_Amount_Source'=>$rsOrder["Back_Amount_Source"]+$amount_source
                );
            }elseif($rsOrder['Order_Status'] == 3){
                $data = array(
                    'Order_Status'=>3,
                    'Front_Order_Status'=>3,
                    'Is_Backup'=>1,
                    'Order_CartList'=>json_encode($CartList,JSON_UNESCAPED_UNICODE),
                    'Back_salems'=>'',
                    'back_qty_str'=>json_encode($backqtyarr,JSON_UNESCAPED_UNICODE),
                    'Back_Integral' => $rsOrder["Back_Integral"] + $Integral_Money,
                    'back_qty' => $rsOrder["back_qty"] + $qty,
                    'Back_Amount'=>$rsOrder["Back_Amount"]+$amount,
                    'Back_Amount_Source'=>$rsOrder["Back_Amount_Source"]+$amount_source
                );
            }
        }else{
            $data = array(
                'Order_Status'=>5,
                'Is_Backup'=>1,
                'Front_Order_Status'=>$rsOrder['Order_Status'],
                'Order_CartList'=>json_encode($CartList,JSON_UNESCAPED_UNICODE),
                'Back_salems'=> '',
                'back_qty_str'=>json_encode($backqtyarr,JSON_UNESCAPED_UNICODE),
                'Back_Integral' => $rsOrder["Back_Integral"] + $Integral_Money,
                'back_qty' => $rsOrder["back_qty"] + $qty,
                'Back_Amount'=>$rsOrder["Back_Amount"]+$amount,
                'Back_Amount_Source'=>$rsOrder["Back_Amount_Source"]+$amount_source
            );

        }
        $uo_obj->where('Order_ID', $rsOrder["Order_ID"])->update($data);
        /*
        if($rsOrder["Order_Status"]==2 && $rsOrder["Order_IsVirtual"]==0){//已付款,商家未发货订单退款
            $this->update_backup("seller_recieve",$recordid,$amount."||%$%已付款/商家未发货订单退款，系统自动完成");
        }*/

    }


    /**
     * 生成一个退款单编号
     */
    protected function build_order_no()
    {
        mt_srand((double) microtime() * 1000000);
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    /**
     * 生成一个退款记录
     * @param $recordid
     * @param $status
     * @param $detail
     * @param $time
     */
    protected function add_record($recordid,$status,$detail,$time)
    {
        $ubod_obj = new User_Back_Order_Detail();
        $Data = array(
            "backid"=>$recordid,
            "detail"=>$detail,
            "status"=>$status,
            "createtime"=>$time
        );
        $ubod_obj->create($Data);
    }

}