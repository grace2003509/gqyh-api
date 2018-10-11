<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/28
 * Time: 16:13
 */

namespace App\Services;


use App\Models\Member;
use App\Models\User_Back_Order;
use App\Models\User_Back_Order_Detail;
use App\Models\UserMoneyRecord;
use App\Models\UserOrder;

class ServiceOrder
{
    /**
     * 获得订单编号
     * @param $oid
     * @return bool|mixed|string
     */
    function getorderno($oid) {
        $builder = new UserOrder();
        $builder = $builder->where('Order_ID',$oid);
        $rsOrder = $builder->first(array('Order_Type','Order_CreateTime','Order_Code'));
        if ($rsOrder) {
            $rsOrder->toArray();
        } else {
            return false;
        }
        if($rsOrder['Order_Type'] == 'pintuan' || $rsOrder['Order_Type'] == 'dangou'){
            $orderno = $rsOrder['Order_Code'];
        }else{
            $orderno = date("Ymd", $rsOrder["Order_CreateTime"]) . $oid;
        }
        return $orderno;
    }


    /**
     * 处理退货单
     * @param $action
     * @param $backid
     * @param string $reason
     */
    public function update_backup($action,$backid,$reason='')
    {
        $ubo_obj = new User_Back_Order();
        $uo_obj = new UserOrder();
        $m_obj = new Member();
        $umr_obj = new UserMoneyRecord();

        $backinfo = $ubo_obj->find($backid);
        switch($action){
            case 'seller_agree'://卖家同意
                $detail = '卖家同意退款';
                //增加流程
                $this->add_back_record($backid,1,$detail);

                $Data = array(
                    "Back_Status"=>1,
                    "Buyer_IsRead"=>0,
                    "Back_UpdateTime"=>time()
                );
                $ubo_obj->where('Back_ID', $backid)->update($Data);
                break;
            case 'seller_reject'://卖家驳回
                $detail = '卖家驳回退款申请，驳回理由：'.$reason;

                //增加退款流程
                $this->add_back_record($backid,1,$detail);

                //更新退款单
                $Data = array(
                    "Back_Status"=>5,
                    "Buyer_IsRead"=>0,
                    "Back_UpdateTime"=>time()
                );
                $back = $ubo_obj->where('Back_ID', $backid)->update($Data);

                $backjson = json_decode(htmlspecialchars_decode($back["Back_Json"]),true);
                $Order = $uo_obj->find($backinfo['Order_ID']);
                //更新订单
                $CartList=json_decode(htmlspecialchars_decode($Order["Order_CartList"]),true);
                if(empty($CartList[$back["ProductID"]])){
                    $CartList[$back["ProductID"]][] = $backjson;
                }else{
                    if(empty($CartList[$back["ProductID"]][$back["Back_CartID"]])){
                        $CartList[$back["ProductID"]][] = $backjson;
                    }else{
                        $CartList[$back["ProductID"]][$back["Back_CartID"]]["Qty"] = $CartList[$back["ProductID"]][$back["Back_CartID"]]["Qty"] + $backjson["Qty"];
                    }
                }
                if($back['Back_Status'] == 5){
                    if($Order['Order_IsVirtual']==1){  //虚拟商品
                        $Data = array(
                            'Is_Backup'=>0,
                            'Order_Status'=>2,
                            'Order_CartList'=>json_encode($CartList,JSON_UNESCAPED_UNICODE),
                            'Back_Amount'=>$Order["Back_Amount"]-$back["Back_Amount"],
                            'Back_Amount_Source'=>$Order["Back_Amount_Source"]-$back["Back_Amount_Source"]
                        );
                    }else{

                        $Data = array(
                            'Order_Status'=>$Order['Front_Order_Status'],
                            'Is_Backup'=>0,
                            'Order_CartList'=>json_encode($CartList,JSON_UNESCAPED_UNICODE),
                            'Back_Amount'=>$Order["Back_Amount"]-$back["Back_Amount"],
                            'Back_Amount_Source'=>$Order["Back_Amount_Source"]-$back["Back_Amount_Source"]
                        );
                    }
                }else{
                    $Data = array(
                        'Is_Backup'=>0,
                        'Order_Status'=>2,
                        'Order_CartList'=>json_encode($CartList,JSON_UNESCAPED_UNICODE),
                        'Back_Amount'=>$Order["Back_Amount"]-$back["Back_Amount"],
                        'Back_Amount_Source'=>$Order["Back_Amount_Source"]-$back["Back_Amount_Source"]
                    );
                }
                $uo_obj->where('Order_ID', $backinfo['Order_ID'])->update($Data);
                break;
            case 'buyer_send':
                $arr = explode("||%$%",$reason);
                $detail = '买家已发货，物流方式：'.$arr[0]."，物流单号：".$arr[1];

                //增加流程
                $this->add_back_record($backid,1,$detail);

                //更新退款单
                $Data = array(
                    "Back_Status"=>2,
                    "Biz_IsRead"=>0,
                    "Back_Shipping"=>$arr[0],
                    "Back_ShippingID"=>$arr[1],
                    "Back_UpdateTime"=>time()
                );
                $uo_obj->where('Order_ID', $backinfo['Order_ID'])->update($Data);
                break;
            case 'seller_recieve':
                $arr = explode("||%$%",$reason);
                $orderinfo = $backinfo["order"];

                if($orderinfo["Order_IsVirtual"]==1){
                    $detail = '卖家已同意并确定了退款金额，退款金额为：'.$arr[0]."，理由：".$arr[1];
                }else{
                    $detail = '卖家已收货并确定了退款金额，退款金额为：'.$arr[0]."，理由：".$arr[1];
                }
                //增加流程
                $this->add_back_record($backid,1,$detail);

                //更新退款单
                $Data = array(
                    "Back_Status"=>3,
                    "Buyer_IsRead"=>0,
                    "Back_Amount"=>$arr[0],
                    "Back_UpdateTime"=>time()
                );
                $ubo_obj->where('Back_ID', $backid)->update($Data);
                $amount = $orderinfo["Back_Amount"]+$arr[0]-$backinfo["Back_Amount"];
                $Data = array(
                    "Back_Amount"=>$amount>0 ? $amount : 0
                );
                $uo_obj->where('Order_ID', $backinfo['Order_ID'])->update($Data);
                break;
            case 'admin_backmoney'://卖家退款给客户

                $Order = $backinfo['order'];
                //微信支付，支付宝支付，余额支付
                $method = $Order['Order_PaymentMethod'];
                $PaymentMethod = array(
                    "微支付" => "1",
                    "支付宝" => "2",
                    "余额支付" => "3",
                    "线下支付" => "4"
                );

                $user_data = $m_obj->find($Order['User_ID']);
                if ($PaymentMethod[$method]==1) { //微支付
                    //todo 微信支付退款
                } elseif ($PaymentMethod[$method]==2) { //支付宝
                    //todo 支付宝退款
                } elseif ($PaymentMethod[$method]==3) { //余额支付
                    $User_Money = $backinfo['Back_Amount'] + $user_data['User_Money'];
                    $user_data->User_Money = $User_Money;
                    $user_data->save();
                    //更新退款单
                    $Data = array(
                        "Back_Status"=>4,
                        "Buyer_IsRead"=>0,
                        "Back_IsCheck"=>1,
                        "Back_UpdateTime"=>time()
                    );
                    $ubo_obj->where('Back_ID', $backid)->update($Data);

                    $Order_CartList = json_decode($Order['Order_CartList'],true);
                    if(empty($Order_CartList)){
                        $uo_obj->where("Order_ID", $Order['Order_ID'])->update(['Order_Status' => 4]);
                    }
                }else if($PaymentMethod[$method]==4){//线下支付
                    $User_Money = $backinfo['Back_Amount'] + $user_data['User_Money'];
                    $user_data->User_Money = $User_Money;
                    $user_data->save();

                    $Data = array(
                        "Back_Status"=>4,
                        "Buyer_IsRead"=>0,
                        "Back_IsCheck"=>1,
                        "Back_UpdateTime"=>time()
                    );
                    $ubo_obj->where('Back_ID', $backid)->update($Data);
                    $Order_CartList = json_decode($Order['Order_CartList'],true);
                    if(empty($Order_CartList)){
                        $uo_obj->where("Order_ID", $Order['Order_ID'])->update(['Order_Status' =>4]);
                    }
                }

                $user_coin = $backinfo['Back_Coin'] + $user_data['User_Integral'];
                $user_data->User_Integral = $user_coin;
                $user_data->save();

                $detail = '管理员已退款给买家';
                //增加流程
                $this->add_back_record($backid,4,$detail);
                $return_money =  $backinfo["Back_Amount"];
                $Data=array(
                    'Users_ID'=>$Order['Users_ID'],
                    'User_ID'=>$Order['User_ID'],
                    'Type'=>1,
                    'Amount'=>$backinfo['Back_Amount'],
                    'Total'=>$user_data['User_Money']+$backinfo["Back_Amount"],
                    'Note'=>"买家退货返还 +".$return_money ." (订单号:".$backinfo["Order_ID"].")",
                    'CreateTime'=>time()
                );
                $umr_obj->create($Data);
                break;
        }
    }


    /**
     * 增加退货记录
     * @param $recordid
     * @param $status
     * @param $detail
     * @param $time
     */
    protected function add_back_record($recordid,$status,$detail)
    {
        $ubod_obj = new User_Back_Order_Detail();
        $Data = array(
            "backid"=>$recordid,
            "detail"=>$detail,
            "status"=>$status,
            "createtime"=>time()
        );
        $ubod_obj->create($Data);
    }

}