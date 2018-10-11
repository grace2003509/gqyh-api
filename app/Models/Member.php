<?php
/**
 * 用户Model
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Member extends Model {

	protected $primaryKey = "User_ID";
	protected $table = "user";
	public $timestamps = false;
	protected $fillable = array('Is_Distribute');

	//一个用户对应一个分销账号
	public function disAccount()
    {
		return $this->hasOne(Dis_Account::class, 'User_ID', 'User_ID');
	}

	//一个用户拥有多个订单
	public function UserOrder()
    {
		return $this->hasMany(UserOrder::class, 'Order_ID', 'User_ID');
	}
	
	//一个用户作为购买者拥有多个分销账户
	public function BuyerDisRecord()
    {
		return $this->hasMany(UserOrder::class, 'Buyder_ID', 'User_ID');
	}
	
	//一个用户作为店主拥有多个分销账户
	public function OwnerDisRecord()
    {
		return $this->hasMany(UserOrder::class, 'Owner_ID', 'User_ID');
	}

	public function MoneyRecord()
    {
        return $this->hasMany(UserMoneyRecord::class, 'User_ID', 'User_ID');
    }

    //清除会员
    function clearUser(array $UserID = [])
    {
        $flag = true;
        $ssp_obj = new ShopSalesPayment();
        $da_obj = new Dis_Account();
        $uo_obj = new UserOrder();
        if(!empty($UserID)){
            $result = $this->whereIn('User_ID', $UserID)->get(['User_ID'])->toArray();
        }else{
            $result = $this->get(['User_ID'])->toArray();

            $flag &= $ssp_obj->delete();
        }

        if(!empty($result)){
            $uid = array_map(function($val){
                return $val['User_ID'];
            }, $result);
            $result = $da_obj->whereIn('User_ID', $uid)->get(['Account_ID']);
            $Account_list = [];
            foreach($result as $value){
                $Account_list[] = $value->Account_ID;
            }
            $result = $uo_obj->whereIn('User_ID', $uid)->get(['Order_ID']);
            $orderlist = [];
            foreach($result as $value){
                $orderlist[] = $value->Order_ID;
            }

            $tables = ['action_num_record','agent_order','alipay_refund',
                'distribute_account','distribute_account_message','distribute_account_record','distribute_agent_areas',
                'distribute_agent_rec','distribute_order','distribute_order_record','distribute_point_record','distribute_record',
                'distribute_sales_record','distribute_sha_rec','distribute_withdraw_method','distribute_withdraw_methods',
                'distribute_withdraw_record','http_raw_post_data','shop_dis_agent_areas','shop_dis_agent_rec','shop_distribute_account',
                'shop_distribute_account_record','shop_distribute_config','shop_distribute_msg','shop_distribute_record',
                'shop_property','shop_sales_payment','shop_sales_record','shop_shipping_company','shop_shipping_template',
                'shop_user_withdraw_methods','shop_withdraw_method','shipping_orders','shipping_orders_commit',
                'sms','third_login_users','uploadfiles','user','user_address','user_back_order',
                'user_back_order_detail','user_card_benefits','user_charge','user_coupon','user_coupon_logs',
                'user_coupon_record','user_favourite_products','user_get_orders','user_get_product','user_gift',
                'user_gift_orders','user_integral_record','user_message','user_message_record','user_money_record',
                'user_operator','user_order','user_order_commit','user_peas','user_peas_orders','user_pre_order',
                'user_profile','user_recieve_address','user_reserve','user_yielist'];
            $item[] = 'User_ID';
            if (!empty($Account_list)) {
                $item[] = 'Account_ID';
            }
            if (!empty($orderlist)) {
                $item[] = 'Order_ID';
            }

            foreach ($item as $it) {
                if ($it == 'Account_ID') {
                    $kid = $Account_list;
                    $itemarray = array('distribute_agent_areas', 'distribute_agent_rec');
                } elseif ($it == 'Order_ID') {
                    $kid = $orderlist;
                    $itemarray = array('distribute_record', 'shop_sales_record');
                } else {
                    $kid = $uid;
                    $itemarray = 1;
                }
                foreach ($tables as $key) {
                    if ($itemarray == 1 || in_array($key, $itemarray)) {
                        $json = DB::select('Describe ' . $key . ' ' . $it . '');
                        if(isset($json[0])){
                            $json = $json[0];
                            if (count($json) > 0) {
                                if ($json->Field == $it) {
                                    DB::table($key)->whereIn($it, $kid)->delete();
                                }
                            }
                        }
                    }
                }
            }

            foreach ($uid as $v){
                $str = USERSID;
                $file = $_SERVER["DOCUMENT_ROOT"] . '/data/avatar/' . $str . $v . '.jpg';
                if(is_file($file) && file_exists($file)){
                    $flag &= unlink($file);
                }
                $file = $_SERVER["DOCUMENT_ROOT"] . '/data/temp/user_' . $v . '.jpg';
                if(is_file($file) && file_exists($file)){
                    $flag &= unlink($file);
                }
                $file = $_SERVER["DOCUMENT_ROOT"] . '/data/poster/user_'  . $str . $v .  '.png';
                if(is_file($file) && file_exists($file)){
                    $flag &= unlink($file);
                }
                $file = $_SERVER["DOCUMENT_ROOT"] . '/data/poster/user_'  . $str . $v .  'pop.png';
                if(is_file($file) && file_exists($file)){
                    $flag &= unlink($file);
                }
            }
        }
        return $flag;
    }

    /**
     * 获取用户所有下级ids
     * @param $userid
     */
    public function getDownUser($userid)
    {
        $dusers = $this->select('User_ID')->where('Owner_ID', $userid)->get();
        $ids = [];
        if($dusers){
            foreach($dusers as $key => $value){
                $ids[] = $value['User_ID'];
                $ids[] = $this->getDownUser($value['User_ID']);
            }
        }
        return $ids;
    }


}