<?php

namespace App\Listeners;

use App\Events\OrderDistributeEvent;
use App\Models\Dis_Account;
use App\Models\Dis_Account_Record;
use App\Models\Dis_Config;
use App\Models\Dis_Record;
use App\Models\ShopConfig;
use App\Models\ShopProduct;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderDistributeEventListener
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
    public function handle(OrderDistributeEvent $event)
    {
        //判断商品是否可分销
        $order = $event->order;
        $Order_CartList = $order->Order_CartList ? json_decode($order->Order_CartList, true) : array();

        $ProductID = array_keys($Order_CartList);
        $fields = ['Products_ID', 'Products_PriceX', 'Products_Distributes', 'Biz_ID', 'Products_Name',
            'commission_ratio', 'platForm_Income_Reward'];
        $p_obj = new ShopProduct();
        $Products = $p_obj->select($fields)->whereIn('Products_ID', $ProductID)->get();

        //循环商品
        foreach($Products as $key => $value){

            if($value["commission_ratio"]<=0 || $value['platForm_Income_Reward'] <=0){//佣金比例
                return false;//产品利润为0不处理
            }

            $Products_Distributes = $value['Products_Distributes'] ? json_decode($value['Products_Distributes'], true) : array();
            //商品未设置佣金
            if(empty($Products_Distributes)){
                return false;
            }

            //增加分销记录
            $dr_obj = new Dis_Record();
            $dis_record_data['Users_ID'] = USERSID;
            $dis_record_data['Buyer_ID'] = $order->User_ID;
            $dis_record_data['Owner_ID'] = $order->Owner_ID;
            $dis_record_data['Order_ID'] = $order->Order_ID;
            $dis_record_data['Product_ID'] = $value->Products_ID;
            //todo 有属性价格的商品要验证价格和数量对不对
            $dis_record_data['Product_Price'] = $order->addtype == 1 ? $order->Order_TotalPrice : $value->Products_PriceX;
            $dis_record_data['Qty'] = $Order_CartList[$value->Products_ID][0]['Qty'];
            $dis_record_data['Record_CreateTime'] = time();
            $dis_record_data['status'] = 0;
            $dis_record_data['Biz_ID'] = $value["Biz_ID"];

            $dis_record = $dr_obj->create($dis_record_data);

            $dis_record['Distribute_List'] = $Products_Distributes;//佣金列表，现设置的是具体金额
            $dis_record['Web_Price'] = $order['Web_Price'];//用于分销的金额

            //如果有分销记录则创建佣金记录
            if($dis_record){
                $this->createDisAccountRecord($dis_record);
            }
        }

    }

    /**
     * 根据生成的分销记录创建佣金获取记录
     * @param Dis_Record $dis_Record
     */
    private function createDisAccountRecord(Dis_Record $dis_Record)
    {
        $Qty = $dis_Record->Qty;

        $dc_obj = new Dis_Config();
        $dis_config = $dc_obj->find(1);
        $level = $dis_config['Dis_Level'];
        $self = $dis_config['Dis_Self_Bonus'];
        $show_word = '奖金';

        //查询购买者的所有上级分销账号id
        $da_obj = new Dis_Account();
        //第一个参数$level变量为控制发几级佣金,如果传0的话,则代表不限制,第二个参数是否包含自己
        $dis_account = $da_obj->where('User_ID', $dis_Record->Buyer_ID)->first();
        if($dis_account){
            $ancestors = $dis_account->getAncestorIds(20,$self);
        }else{
            return false;
        }

        $sp_obj = new ShopProduct();
        $product = $sp_obj->find($dis_Record->Product_ID);

        $ancestors_meet = $da_obj->get_distribute_balance_userids($dis_Record->Buyer_ID,$ancestors,$dis_Record['Distribute_List']);
        $send_commi = 0; //已经发放的佣金
        foreach ($ancestors_meet as $mk => $mv) {
            //根据查询出来的佣金发放金额跟产品价格做对比,如果超过利润就不再发放
            if ($send_commi + $mv['bonus'] > $dis_Record['Web_Price'] || $mv['bonus'] == 0) {
                unset($ancestors_meet[$mk]);
                continue;
            }
            $send_commi += $mv['bonus'];
        }

        foreach($ancestors as $key => $value){

            if ($dis_Record->Buyer_ID == $value) {//自销
                $my_level = 0;
                //自己获取佣金
                $Record_Description = '自己销售自己购买' . $product['Products_Name'] . '&yen;' . $dis_Record['Product_Price']. '成功，获取'.$show_word;
                $Record_Money = !empty($product['Distribute_List'][$my_level][$level]) ? $product['Distribute_List'][$my_level][$level] : 0;
                $Record_Price = $Record_Money / $Qty;
                $level_show = 110;
            } else {
                if($dis_Record->Owner_ID == $value){
                    $Record_Description = '自己销售下属购买' . $product['Products_Name'] . '&yen;' .$dis_Record['Product_Price']. '成功，获取'.$show_word;
                }else{
                    $Record_Description = '下属分销商分销' . $Qty.'个'.$product['Products_Name'] . '&yen;' .$dis_Record['Product_Price']. '成功，获取'.$show_word;
                }
                //上级分销商获取的佣金
                if(!empty($ancestors_meet[$value])){
                    if($ancestors_meet[$value]['status']==1){//正常
                        $Record_Money = $ancestors_meet[$value]['bonus'] > 0 ? $ancestors_meet[$value]['bonus'] * $Qty : 0;//门槛商品数量
                        $Record_Price = $Record_Money / $Qty;
                    }else{
                        $Record_Money = 0;
                        $Record_Price = 0;
                        $Record_Description = $ancestors_meet[$value]['msg'];
                    }
                }else{
                    continue;
                }
                $level_show = $key + 1;
            }

            $dis_account_record_data = [
                'Users_ID' => USERSID,
                'Ds_Record_ID' => $dis_Record->Record_ID,
                'User_ID' => $value,
                'Record_Sn' => build_record_sn(),
                'level' => $level_show,
                'Record_Qty' => $Qty,
                'Record_Price' => $Record_Price,
                'Record_Money' => $Record_Money,
                'Record_CreateTime' => time(),
                'Record_Type' => 0,
                'Record_Status' => 0,
                'CartID' => 0,
                'yformat' => '',
                'Record_Description' => $Record_Description,
            ];

            $dar_obj = new Dis_Account_Record();
            $dar_obj->create($dis_account_record_data);

        }

    }
}