<?php

namespace App\Listeners;

use App\Events\OrderConfirmEvent;
use App\Events\OrderDistributeEvent;
use App\Models\Dis_Account;
use App\Models\Dis_Agent_Area;
use App\Models\Dis_Agent_Record;
use App\Models\Dis_Config;
use App\Models\Dis_Level;
use App\Models\ShopConfig;
use App\Models\UserOrder;
use App\Services\ServiceBalance;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class OrderConfirmEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderDistributeEvent  $event
     * @return void
     */
    public function handle(OrderConfirmEvent $event)
    {
        $sc_obj = new ShopConfig();
        $dc_obj = new Dis_Config();

        $rsConfig = $sc_obj->find(USERSID);
        $dis_config = $dc_obj->find(1);
        $shop_config = array_merge($rsConfig,$dis_config);//合并参数

        $order = $event->order;

        DB::beginTransaction();

        //获取本店分销配置，处理普通商品代理佣金
        $flag_d = true;
        if($shop_config['Dis_Agent_Type'] != 0){
            $flag_d = $this->handle_area_dis_agent($order);
        }

        if(!$flag_d){
            DB::rollback();
        }else{
            $balance_sales= new ServiceBalance();
            $balance_sales->add_sales($order['Order_ID']);
            DB::commit();
        }

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


    //获得配置信息
    private function get_shopconfig()
    {
        $r = ShopConfig::find(USERSID)->toArray();
        $r1 = Dis_Config::find(1)->toArray();
        $r = array_merge($r, $r1);
        return $r;
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