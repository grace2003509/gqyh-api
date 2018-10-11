<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/28
 * Time: 14:06
 */

namespace App\Services;

use App\Models\Biz;
use App\Models\Dis_Account;
use App\Models\Dis_Account_Record;
use App\Models\Dis_Agent_Area;
use App\Models\Dis_Agent_Record;
use App\Models\Dis_Config;
use App\Models\Dis_Level;
use App\Models\Dis_Point_Record;
use App\Models\Dis_Record;
use App\Models\Member;
use App\Models\User_Message;
use App\Models\UserIntegralRecord;
use App\Models\UserOrder;
use App\Models\Setting;
use App\Models\ShopConfig;
use App\Models\ShopProduct;
use Illuminate\Support\Facades\DB;

class ServicePayOrder
{
    public function make_pay($orderid)
    {
        if (strpos($orderid, "PRE") > -1) {
            $pre_order = DB::table('user_pre_order')->select('orderids')->where('pre_sn', $orderid)->first();
            $orderids = explode(",", $pre_order["orderids"]);
            foreach ($orderids as $orderid) {
                if (!$orderid) {
                    continue;
                }
                $data = $this->pay_orders($orderid);
            }
            $pre_order = DB::table('user_pre_order')->where('pre_sn', $orderid)->update(["status" => 2]);

        } else {
            $data = $this->pay_orders($orderid);
        }
        return $data;
    }


    private function pay_orders($orderid)
    {
        $rsOrder = UserOrder::find($orderid);
        if (!$rsOrder) {
            return array("status" => 0, "msg" => "订单不存在");
        }
        $rsUser = Member::find($rsOrder["User_ID"]);

        if ($rsOrder["Order_Status"] >= 2) {
            return array("status" => 1);
        }
        //更新订单状态
        $rsOrder->Order_Status = 2;
        $Flag_b = $rsOrder->save();

        //更新商品销量
        $CartList = json_decode(htmlspecialchars_decode($rsOrder['Order_CartList']), true);
        foreach ($CartList as $ProductID => $product_list) {
            $qty = 0;
            foreach ($product_list as $key => $item) {
                $qty += $item['Qty'];
            }
            $product = ShopProduct::find($ProductID);
            $Products_Count = $product['Products_Count'] - $qty;
            if ($Products_Count == 0) {
                $product->Products_SoldOut = 1;
            }
            if ($Products_Count < 0) {
                return array("status" => 0, "msg" => "商品库存不足");
            }
            $product->Products_Sales += $qty;
            $product->Products_Count = $Products_Count;
            $product->save();
        }

        //积分抵用
        if ($rsOrder['Integral_Consumption'] != 0 || $rsOrder['order_coin'] != 0) {
            $integral_up = $rsUser['User_Integral'] - $rsOrder['Integral_Consumption'] - $rsOrder['order_coin'];
            $integral_up_amote = $rsUser['User_TotalIntegral'] - $rsOrder['Integral_Consumption'];
            $rsUser->User_Integral = $integral_up;
            $rsUser->User_TotalIntegral = $integral_up_amote;
            $rsUser->save();
        }

        //更改分销账号记录状态,置为已付款
        if ($rsOrder['Order_Type'] != 'cloud') {
            $Flag_c = Dis_Account_Record::changeStatusByOrderID($orderid, 1);
            $Flag_d = Dis_Point_Record::updatePointStatus($orderid, 2);
            if ($Flag_c) {
                //支付成功修改分销状态
                $this->pay_successed($rsOrder);
            }
        }

        if ($Flag_b && $Flag_c && $Flag_d) {
            $rsConfig = $this->get_shopconfig();

            //获取订单中的产品id集
            $productids = array_keys($CartList);

            //分销商级别数组
            $dl_obj = new Dis_Level();
            $level_data = $dl_obj->get_dis_level();
            if ($rsOrder['Order_Type'] != 'cloud') {
                if ($rsUser["Is_Distribute"] == 0) {//不是分销商
                    $LevelID = 0;//分销商级别
                    if ($rsConfig["Distribute_Type"] == 2) {//购买商品
                        $LevelID = $this->get_user_distribute_level_buy($level_data, $rsConfig, $productids);
                    } elseif ($rsConfig["Distribute_Type"] == 1) {//消费金额
                        $LevelID = $this->get_user_distribute_level_cost($level_data, $rsConfig, $rsOrder['Order_TotalPrice']);
                        if (empty($LevelID)) {
                            $LevelID = $this->get_user_distribute_level($rsConfig, $level_data, $rsOrder['User_ID']);
                        }
                    }

                    if ($LevelID >= 1) {
                        $f = $this->create_distribute_acccount($rsConfig, $rsUser, $LevelID, '', 1);
                    }

                    //爵位晋级
                    if ($rsConfig["Pro_Title_Status"] == 2) {
                        $pro = new ServiceProTitle($rsUser["Owner_Id"]);
                        $flag_x = $pro->up_nobility_level();
                    }
                } else {//是分销商判定可提现条件
                    $da_obj = new Dis_Account();
                    $tixian = 0;
                    $rsAccount = $da_obj->select('User_ID', 'Enable_Tixian', 'Account_ID', 'Is_Dongjie', 'Is_Delete', 'Level_ID')
                        ->where('User_ID', $rsOrder["User_ID"])
                        ->first();
                    if ($rsAccount) {
                        //爵位晋级
                        if ($rsConfig["Pro_Title_Status"] == 2) {
                            $pro = new ServiceProTitle($rsUser["Owner_Id"]);
                            $flag_x = $pro->up_nobility_level();
                        }

                        $account_data = array();
                        if ($rsAccount["Enable_Tixian"] == 0) {
                            if ($rsConfig["Withdraw_Type"] == 0) {
                                $tixian = 1;
                            } elseif ($rsConfig["Withdraw_Type"] == 2) {
                                $arr_temp = explode("|", $rsConfig["Withdraw_Limit"]);
                                if ($arr_temp[0] == 0) {
                                    $tixian = 1;
                                } else {
                                    if (!empty($arr_temp[1])) {
                                        $productsid = explode(",", $arr_temp[1]);
                                        foreach ($productsid as $id) {
                                            if (!empty($CartList[$id])) {
                                                $tixian = 1;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            if ($tixian == 1) {
                                $account_data['Enable_Tixian'] = 1;
                            }
                        }

                        // 判定升级级别
                        if ($rsConfig["Distribute_Type"] == 1) { // 消费金额
                            $LevelID = $this->get_user_distribute_level_cost($level_data, $rsConfig, $rsOrder['Order_TotalPrice']);
                        } else {
                            $two_level_data = next($level_data);
                            if ($two_level_data['Update_switch']) {
                                $LevelID = $this->get_user_distribute_level_upgrade($level_data, $productids);
                            } else {
                                $LevelID = $this->get_user_distribute_level_buy($level_data, $rsConfig, $productids);
                            }
                        }

                        if ($LevelID > $rsAccount['Level_ID']) {
                            $account_data['Level_ID'] = $LevelID;
                        }

                        if (!empty($account_data)) {
                            $da_obj->where('Account_ID', $rsAccount["Account_ID"])->update($account_data);
                        }
                    }
                }

                $order = new UserOrder();
                $confirm_code = '';
                if ($rsOrder["Order_IsVirtual"] == 1) {

                    if ($rsOrder["Order_IsRecieve"] == 1) {
                        $Flag = $order->confirmReceive($orderid);
                    } else {
                        $confirm_code = $order->get_virtual_confirm_code($orderid);
                        $Data = array('Order_Code' => $confirm_code);
                        $order->update_order($orderid, $Data);
                    }
                }

                $orderidnumber = $order->getorderno($orderid);
                $sms_obj = new ServiceSMS();
                $setting = Setting::select('sms_enabled')->find(1);
                if ($rsOrder['addtype'] != 1) {
                    if ($setting["sms_enabled"] == 1) {
                        if ($rsConfig["SendSms"] == 1) {
                            if ($rsConfig["MobilePhone"]) {
                                $sms_mess = '您的商品有订单付款，订单号' . $orderidnumber . '请及时查看！';
                                $sms_obj->send_sms($rsConfig["MobilePhone"], $sms_mess);
                            }
                        }
                        if ($rsOrder["Biz_ID"] > 0) {
                            $biz = Biz::select('Biz_SmsPhone')->find($rsOrder["Biz_ID"]);
                            if (!empty($biz["Biz_SmsPhone"])) {
                                $sms_mess = '您的商城有新订单，订单号' . $orderidnumber . '，请及时查看';
                                $sms_obj->send_sms($biz["Biz_SmsPhone"], $sms_mess);
                            }
                        }
                        if ($rsOrder["Order_IsVirtual"] == 1 && $rsOrder["Order_IsRecieve"] == 0) {
                            $sms_mess = '您已成功购买商品，订单号' . $orderidnumber . '，消费券码为 ' . $confirm_code;
                            $sms_obj->send_sms($rsOrder["Address_Mobile"], $sms_mess);
                        } else {
                            $sms_mess = '您已成功购买商品，订单号' . $orderidnumber;
                            $sms_obj->send_sms($rsOrder["Address_Mobile"], $sms_mess);
                        }
                    }
                }

            }

            return array("status" => 1);
        } else {
            return array("status" => 0, "msg" => "订单支付失败");
        }
    }

    /**
     *  门槛商品支付成功之后的事情
     */
    private function pay_successed(UserOrder $order)
    {
        $rsConfig = $this->get_shopconfig();

        DB::beginTransaction();

        $flag_a = $this->handle_user_info($order);

        $flag_b = $flag_c = $flag_d = true;
        if ($order->disAccountRecord()->count() > 0) {
            //更改分销账户得钱记录状态，以及晋级操作
            $flag_b = $this->handle_dis_record_info($order);
            //处理分销账号信息，增加余额，总收入
            $flag_c = $this->handle_dis_account_info($order);
        }
        if ($rsConfig['Dis_Agent_Type'] != 0) {
            //处理区域代理分销记录
            $flag_d = $this->handle_area_dis_agent($order);
        }

        $flag = $flag_a && $flag_b && $flag_c && $flag_d;

        if (!$flag) {
            DB::rollBack();
        } else {
            DB::commit();
        }
    }

    //获得配置信息
    private function get_shopconfig()
    {
        $r = ShopConfig::find(USERSID)->toArray();
        $r1 = Dis_Config::find(1)->toArray();
        $r = array_merge($r, $r1);
        return $r;
    }


    //根据购买商品获得分销商级别
    private function get_user_distribute_level_buy($level_data, $shop_config, $productids)
    {
        $first_level_data = reset($level_data);
        $LevelID = $first_level_data['Level_LimitType'] == 3 ? $first_level_data['Level_ID'] : 0;
        if ($shop_config['Distribute_Type'] == 2) {//购买商品
            $level_array = array_reverse($level_data, true);
            foreach ($level_array as $id => $value) {
                if ($value['Level_LimitType'] <> 2) continue;
                $arr_temp = explode('|', $value['Level_LimitValue']);
                if ($arr_temp[0] == 0) {//购买任意商品
                    $LevelID = $value['Level_ID'];
                    break;
                } else {
                    $pids = $arr_temp[1] ? explode(',', $arr_temp[1]) : array();
                    $array_intersect = array_intersect($productids, $pids);
                    if (!empty($array_intersect)) {
                        $LevelID = $value['Level_ID'];
                        break;
                    }
                }
            }
        }
        return $LevelID;
    }


    //根据消费额获得分销商级别（一次性消费，状态为已付款计入）
    private function get_user_distribute_level_cost($level_data, $shop_config, $cost)
    {
        $first_level_data = reset($level_data);
        $LevelID = $first_level_data['Level_LimitType'] == 3 ? $first_level_data['Level_ID'] : 0;
        if ($shop_config['Distribute_Type'] == 1) {//消费额门槛
            $level_result = array();
            $level_array = array_reverse($level_data, true);
            foreach ($level_array as $id => $value) {
                if ($value['Level_LimitType'] <> 1) continue;
                $arr_temp = explode('|', $value['Level_LimitValue']);
                if ($arr_temp[0] == 1 && $arr_temp[2] == 2) {//一次性消费
                    $level_result[$id] = array(
                        'id' => $value['Level_ID'],
                        'money' => empty($arr_temp[1]) ? 0 : $arr_temp[1]
                    );
                }
            }

            if (!empty($level_result)) {
                foreach ($level_result as $key => $item) {
                    if ($item['money'] <= $cost) {
                        $LevelID = $item['id'];
                        break;
                    }
                }
            }
        }
        return $LevelID;
    }


    //根据消费额获得分销商级别（总消费额），用于生成分销商或更新
    function get_user_distribute_level($shop_config, $level_data, $UserID, $orderid = 0)
    {
        $first_level_data = reset($level_data);
        $LevelID = 0;
        if ($shop_config['Distribute_Type'] == 1) {//消费额门槛
            $level_result = array();
            $level_array = array_reverse($level_data, true);
            foreach ($level_array as $id => $value) {
                if ($value['Level_LimitType'] <> 1) continue;
                $arr_temp = explode('|', $value['Level_LimitValue']);
                if ($arr_temp[0] == 0) {//商城总消费额
                    $level_result[$id] = array(
                        'id' => $value['Level_ID'],
                        'money' => empty($arr_temp[1]) ? 0 : $arr_temp[1],
                        'status' => $arr_temp[2]
                    );
                }
            }
            $LevelID = $first_level_data['Level_LimitType'] == 3 ? $first_level_data['Level_ID'] : 0;
            $uo_obj = new UserOrder();
            if (!empty($level_result)) {
                $rTotalPrice = 0;
                if (!empty($orderid)) {
                    $rTotalPricearr = $uo_obj->select('Order_TotalPrice')
                        ->where('Order_ID', $orderid)->first();
                    $rTotalPrice = $rTotalPricearr['Order_TotalPrice'];
                }
                $r['money'] = $uo_obj->where('User_ID', $UserID)
                    ->where('Order_Status', '<>', 1)
                    ->where('Is_Backup', '<>', 1)
                    ->sum('Order_TotalPrice');
                $cost_status_2 = empty($r['money']) ? 0 : $r['money'];
                $cost_status_2 = $cost_status_2 + $rTotalPrice;

                $r['money'] = $uo_obj->where('User_ID', $UserID)
                    ->where('Order_Status', 4)
                    ->sum('Order_TotalPrice');
                $cost_status_4 = empty($r['money']) ? 0 : $r['money'];
                $yesuplevel = 0;
                foreach ($level_result as $key => $item) {
                    if (intval($item['status']) == 2 && $item['money'] <= $cost_status_2) {
                        $LevelID = $item['id'];
                        $yesuplevel = 1;
                        break;
                    }
                    if (intval($item['status']) == 4 && $item['money'] <= $cost_status_4) {
                        $LevelID = $item['id'];
                        $yesuplevel = 1;
                        break;
                    }
                }
                if (!$yesuplevel && $first_level_data['Level_LimitType'] != 3) {
                    Dis_Account::where('User_ID', $UserID)->update(['Is_Delete' => 1]);
                }
            }
        }
        return $LevelID;
    }


    /**
     *创建分销商
     */
    function create_distribute_acccount($rsConfig, $user_data, $LevelID, $mobile, $status = 0)
    {
        /*获取此店铺的配置信息*/
        $UsersID = $rsConfig['Users_ID'];
        $UserID = $user_data['User_ID'];

        //若不存在指定用户
        if (empty($user_data)) {
            return false;
        }

        //检测该用户是否有分销商账号
        $dis_account = Dis_Account::where('User_ID', $UserID)->first();

        //若此分销账户已存在，只需将其通过审核 Is_Audit
        if (!empty($dis_account)) {
            if ($dis_account->Is_Delete == 1) {//账号已被标识为删除状态
                $dis_account->Is_Delete = 0;
                $dis_account->Is_Dongjie = 0;
            }
            if ($status == 1 && $dis_account->Is_Audit == 0) {//未审核
                $dis_account->Is_Audit = 1;
            }
            if ($LevelID > $dis_account->Level_ID) {
                $dis_account->Level_ID = $LevelID;
            }
            $dis_account->save();

            if ($user_data["Is_Distribute"] == 0) {
                Member::find($UserID)->update(array('Is_Distribute' => 1));
            }

            return true;
        }

        //创建新的Dis_Account对象
        $disAccount = new Dis_Account();

        $disAccount->Users_ID = $UsersID;
        $disAccount->Level_ID = $LevelID;
        $disAccount->User_ID = $UserID;
        $disAccount->Real_Name = $user_data['User_Name'] ? $user_data['User_Name'] : $user_data['User_NickName'];
        $disAccount->Shop_Name = $user_data['User_NickName'];
        $disAccount->Shop_Logo = $user_data['User_HeadImg'];
        $disAccount->balance = 0;
        $disAccount->status = 1;
        $disAccount->Is_Audit = $status;
        $disAccount->Account_Mobile = empty($mobile) ? '' : $mobile;
        $disAccount->Account_CreateTime = time();
        $disAccount->Group_Num = 1;
        $disAccount->Fanxian_Remainder = empty($Fanben[0]) ? 0 : intval($Fanben[0]);
        $disAccount->invite_id = $user_data["Owner_Id"];

        $Dis_Path = $disAccount->generateDisPath();
        $disAccount->Dis_Path = $Dis_Path;

        $Flag = $disAccount->save();

        if ($Flag) {
            return true;
        } else {
            return false;
        }
    }


    //根据购买商品获得分销商升级级别
    private function get_user_distribute_level_upgrade($level_data, $productids)
    {
        $LevelID = 0;
        $level_array = array_reverse($level_data, true);
        foreach ($level_array as $id => $value) {
            if ($value['Level_UpdateType'] <> 1) continue;
            $pids = $value['Level_UpdateValue'] ? explode(',', $value['Level_UpdateValue']) : array();
            $array_intersect = in_array($productids[0], $pids) ? 1 : 0;
            if (!empty($array_intersect)) {
                $LevelID = $value['Level_ID'];
                break;
            }
        }
        return $LevelID;
    }


    /**
     * 处理用户信息
     * 更新用户积分，用户销售额等信息
     * 增加积分记录
     */
    private function handle_user_info(UserOrder $order)
    {
        // 用户级别设置
        $interval = $order ['Integral_Get'];
        $shop_config = $this->get_shopconfig();

        $user = $order->User()->getResults();
        $res = true;
        if ($user) {
            if ($shop_config ['Distribute_Type'] == 1) {//消费金额
                $dl_obj = new Dis_Level();
                $level_data = $dl_obj->get_dis_level();
                $LevelID = $this->get_user_distribute_level_confirmcost($level_data, $shop_config, $order ['Order_TotalPrice']);
                if ($LevelID >= 1) {
                    $this->create_distribute_acccount($shop_config, $user, $LevelID, '', 1);
                }
            }
            if ($order->Order_Type != 'offline' && $order->Order_Type != 'offline_qrcode' && $order->Order_Type != 'offline_st') {
                $user->User_Integral = $user->User_Integral + $interval;
            }
            $user->User_TotalIntegral = $user->User_TotalIntegral + $interval;
            $user->User_Cost = $user->User_Cost + $order->Order_TotalAmount;

            $res = $user->save();

            // 增加积分记录
            if ($interval > 0) {
                if ($order->Order_Type != 'offline_st' && $order->Order_Type != 'offline_qrcode' && $order->Order_Type != 'offline') {
                    $this->handle_integral_record($interval, $user);
                }
            }
        }

        return $res;
    }


    public function get_user_distribute_level_confirmcost($level_data, $shop_config, $cost)
    {
        $first_level_data = reset($level_data);
        $LevelID = $first_level_data['Level_LimitType'] == 3 ? $first_level_data['Level_ID'] : 0;
        if ($shop_config['Distribute_Type'] == 1) {//消费额门槛
            $level_result = array();
            $level_array = array_reverse($level_data, true);
            foreach ($level_array as $id => $value) {
                if ($value['Level_LimitType'] <> 1) continue;
                $arr_temp = explode('|', $value['Level_LimitValue']);
                if ($arr_temp[0] == 1 && $arr_temp[2] == 4) {//一次性消费
                    $level_result[$id] = array(
                        'id' => $value['Level_ID'],
                        'money' => empty($arr_temp[1]) ? 0 : $arr_temp[1]
                    );
                }
            }

            if (!empty($level_result)) {
                foreach ($level_result as $key => $item) {
                    if ($item['money'] <= $cost) {
                        $LevelID = $item['id'];
                        break;
                    }
                }
            }
        }
        return $LevelID;
    }


    /**
     * 增加用户积分记录
     */
    private function handle_integral_record($interval, $user)
    {
        $user_integral_record = new UserIntegralRecord();
        $user_integral_record->Record_Integral = $interval;
        $user_integral_record->Record_SurplusIntegral = $user['User_Integral'];
        $user_integral_record->Operator_UserName = '';
        $user_integral_record->Record_Type = 2;
        $user_integral_record->Record_Description = '购买商品送 ' . $interval . ' 个积分';
        $user_integral_record->Record_CreateTime = time();
        $user_integral_record->User_ID = $user['User_ID'];
        $user_integral_record->Users_ID = USERSID;
        $flag = $user_integral_record->save();

        return $flag;
    }


    /**
     * 处理分销记录信息
     */
    private function handle_dis_record_info(UserOrder $order)
    {
        $dr_obj = new Dis_Record();
        $dpr_obj = new Dis_Point_Record();
        $dar_obj = new Dis_Account_Record();

        $rsRecord = $dr_obj->select('Record_ID', 'Order_ID')
            ->where('Order_ID', $order['Order_ID'])
            ->get();
        $point_record = $dpr_obj->where('orderid', $order['Order_ID'])->get();

        if ($rsRecord) {

            $flag_a = $dr_obj->where('Order_ID', $order['Order_ID'])->update(['status' => 1]);

            foreach ($rsRecord as $key => $value) {
                $flag_b = $dar_obj->where('Ds_Record_ID', $value['Record_ID'])->update(['Record_Status' => 2]);

                //发送获取分销佣金系统消息
                $rst = $dar_obj->where('Ds_Record_ID', $value['Record_ID'])->get();
                foreach ($rst as $k => $v) {
                    $Data = array(
                        "Message_Title" => '恭喜您获得下级分销佣金' . $v['Record_Money'],
                        "Message_Description" => $v['Record_Description'],
                        "Message_CreateTime" => time(),
                        "Users_ID" => USERSID,
                        "User_ID" => $v["User_ID"]
                    );
                    User_Message::create($Data);
                }
            }

            $flag_c = true;
            if (!empty($point_record)) {
                $flag_c = $dpr_obj->where('orderid', $order['Order_ID'])->update(['status' => 2]);
            }
            return $flag_a && $flag_b && $flag_c;
        } else {
            return true;
        }
    }


    /**
     * 增加分销账号余额,总销售额
     */
    private function handle_dis_account_info(UserOrder $order)
    {
        $disAccountRecord = $order->disAccountRecord()->getResults();
        $point_record = $order->pointRecord()->getResults();
        //发放点位奖
        $point_list = [];
        $point_ids = [];
        foreach ($point_record as $pk => $pv) {
            //排除pointRecord中的重消奖记录和团队奖记录
            if ($pv->type <> 2 && $pv->type <> 4) {
                array_push($point_ids, $pv->User_ID);
                $ls_arr = [$pv->User_ID => $pv->money];
                $point_list = $point_list + $ls_arr;
            }
        }
        // 得到获得佣金的UserID
        $userID_List = $disAccountRecord->map(function ($disAccountRecord) {
            return $disAccountRecord->User_ID;
        })->all();

        $userIDS = array_unique(array_merge($userID_List, $point_ids));
        $flag = true;

        if (!empty ($userIDS)) {
            foreach ($userIDS as $key => $item) {
                $interest_list [$item] = 0;
                $nobi_list[$item] = 0;
                $sales_list [$item] = 0;

            }
            foreach ($disAccountRecord as $key => $accountRecord) {
                $interest_list [$accountRecord->User_ID] += $accountRecord->Record_Money;
                $nobi_list[$accountRecord->User_ID] += $accountRecord->Nobi_Money;
                $DisRecord = $accountRecord->DisRecord;
                $sales_list [$accountRecord->User_ID] += $DisRecord->Product_Price * $DisRecord->Qty;
            }
            $disAccoutn_list = Dis_Account::whereIn('User_ID', $userIDS)->get();

            // 取出所有获得佣金者的分销账号
            foreach ($disAccoutn_list as $disAccount) {
                $interest_money = (!empty($interest_list[$disAccount->User_ID]) ? $interest_list[$disAccount->User_ID] : 0);
                $nobi_money = (!empty($nobi_list[$disAccount->User_ID]) ? $nobi_list[$disAccount->User_ID] : 0);
                $point_money = (!empty($point_list[$disAccount->User_ID]) ? $point_list[$disAccount->User_ID] : 0);
                $point_balance = $disAccount->point_money + $point_money;
                $disAccount->point_money = $point_balance;
                $cur_blance = $disAccount->balance + $interest_money + $nobi_money + $point_money;
                $disAccount->balance = $cur_blance;
                $cur_total_income = $disAccount->Total_Income + $interest_money + $nobi_money + $point_money;
                $disAccount->Total_Income = $cur_total_income;
                $flag = $disAccount->save();

                if (!$flag) {
                    break;
                }
            }
        }

        return $flag;
    }


    /**
     * 处理地区分销代理信息
     */
    private function handle_area_dis_agent(UserOrder $order)
    {
        $daa_obj = new Dis_Agent_Area();
        $isareaok = $daa_obj->select('id')->where('Users_ID', USERSID)->first();
        if (!$isareaok) {
            return true;
        }

        $shop_config = $this->get_shopconfig();
        $area_rate = json_decode($shop_config['Agent_Rate'], TRUE);
        //省级配置
        $province_rate = $area_rate['pro']['Province'];
        //省会级配置
        $province_city_rate = $area_rate['procit']['Province'];
        //市级配置
        $city_rate = $area_rate['cit']['Province'];
        //县区级配置
        $area_agent_rate = $area_rate['cou']['Province'];

        $user = $order->User()->getResults();

        if ($order->Address_Province) {//有收货地址,按收货地址；否则，按微信地址
            $User_Province = $order->Address_Province;
            $User_City = $order->Address_City;
            $User_Area = $order->Address_Area;
            $area_agents = $daa_obj->whereIn('area_id', array($User_Province, $User_City, $User_Area))
                ->get();
        } else {
            $User_Province = trim($user->User_Province);
            $User_City = trim($user->User_City);
            $User_Area = trim($user->User_Area);
            $area_agents = $daa_obj->whereIn('area_name', array($User_Province, $User_City))
                ->get();
        }

        $cartProduct = json_decode($order->Order_CartList, true);
        if (!empty($order->back_qty_str)) {
            $back_qty_arr = json_decode($order->back_qty_str, true);
        }
        $flag = true;

        //获取门槛商品ids
        $member_products_ids_str = '';
        $dis_leve_config = Dis_Level::select('Level_LimitType', 'Level_LimitValue')->get();
        foreach ($dis_leve_config as $k => $v) {
            $v['Level_LimitValue'] = explode('|', $v['Level_LimitValue']);
            if ($v['Level_LimitType'] == 2 && $v['Level_LimitValue'][0] == 1) {
                $member_products_ids_str .= $v['Level_LimitValue'][1] . ',';
            }
        }
        $member_products_ids_arr = explode(',', $member_products_ids_str);
        $member_products_ids_arr = array_filter($member_products_ids_arr);

        if (!empty($cartProduct)) {
            foreach ($cartProduct as $k => $v) {
                if (in_array($k, $member_products_ids_arr)) {
                    foreach ($v as $p => $productInfo) {
                        if (!empty($order->muilti)) {
                            $orderweb_price = $productInfo['web_prie_shop'];
                        } else {
                            $orderweb_price = $order->Web_Price;
                        }

                        //循环给每个代理商级别发放佣金
                        foreach ($area_agents as $key => $agent_area) {
                            if ($agent_area->type == 1) {
                                //省代
                                $account_rate = $province_rate;
                            } else if ($agent_area->type == 2) {
                                //省会代理
                                $account_rate = $province_city_rate;
                            } else if ($agent_area->type == 3) {
                                //市代
                                $account_rate = $city_rate;
                            } else if ($agent_area->type == 4) {
                                //县区代
                                $account_rate = $area_agent_rate;
                            } else {
                                $account_rate = 0;
                            }

                            $account_id = $agent_area->Account_ID;
                            $areaid = $agent_area->area_id;
                            if ($account_rate > 0) {
                                //计算当前产品的佣金
                                if ($k < 0) {
                                    if (!isset($offarr) || empty($offarr['Profit'])) {
                                        return true;
                                    }
                                    $productInfo['area_Proxy_Reward'] = $offarr['area_Proxy_Reward'];
                                    $orderweb_price = $order->Web_Price;
                                    $product_record_money = ($orderweb_price * ($offarr['platForm_Income_Reward'] / 100)) * ($account_rate / 100);
                                } else {
                                    $product_record_money = ($orderweb_price * ($productInfo['platForm_Income_Reward'] / 100)) * ($account_rate / 100);
                                }

                                if (isset($back_qty_arr) && isset($back_qty_arr[$k][$p])) {
                                    $product_record_money = $product_record_money / ($productInfo['Qty'] + $back_qty_arr[$k][$p]) * $productInfo['Qty'];
                                }
                                $flag_a = $this->do_agent_award($account_id, $product_record_money);
                                $flag_b = $this->add_agent_award_record($account_id, $product_record_money, 3, $order, $productInfo, $areaid);
                                $flag = $flag_a && $flag_b;
                            }
                        }
                    }
                }

            }
        }

        return $flag;
    }

    /**
     *给代理人添加佣金
     *添加代理记录
     */
    private function do_agent_award($root_id, $record_money)
    {
        $flag = TRUE;
        $dis_account = Dis_Account::where('Account_ID', $root_id)->first();
        $balance = $dis_account->balance + $record_money;
        $Total_Income = $dis_account->Total_Income + $record_money;
        $dis_account->balance = $balance;
        $dis_account->Total_Income = $Total_Income;
        $flag = $dis_account->save();
        return $flag;
    }


    /**
     *添加代理奖励记录
     */
    private function add_agent_award_record($root_id, $record_money, $type = 1, UserOrder $order, $productInfo = array(), $careaid)
    {
        $flag = TRUE;
        $dis_agent_record = new Dis_Agent_Record();
        $dis_account = Dis_Account::where('Account_ID', $root_id)->first();

        $dis_agent_record->Users_ID = USERSID;
        $dis_agent_record->Account_ID = $root_id;
        $dis_agent_record->Record_Money = $record_money;
        $dis_agent_record->Record_CreateTime = time();
        $dis_agent_record->Record_Type = $type;
        $dis_agent_record->area_id = $careaid;
        $dis_agent_record->Order_CreateTime = $order->Order_CreateTime;
        $dis_agent_record->Order_ID = $order->Order_ID;
        $dis_agent_record->Products_Name = $productInfo['ProductsName'];
        $dis_agent_record->Products_Qty = $productInfo['Qty'];
        $dis_agent_record->Products_PriceX = $productInfo['ProductsPriceX'];
        $dis_agent_record->area_Proxy_Reward = $productInfo['area_Proxy_Reward'];
        if (!empty($dis_account->Real_Name)) {
            $dis_agent_record->Real_Name = $dis_account->Real_Name;
        }
        if (!empty($dis_account->Account_Mobile)) {
            $dis_agent_record->Account_Mobile = $dis_account->Account_Mobile;
        }

        $flag = $dis_agent_record->save();
        return $flag;
    }

}