<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/11
 * Time: 16:26
 */

namespace App\Services;


use App\Models\Dis_Account;
use App\Models\Dis_Config;
use App\Models\Dis_Level;
use App\Models\Dis_Point_Record;
use App\Models\Member;
use App\Models\User_Message;
use App\Models\UserOrder;

class ServiceProTitle
{
    private $User_ID;

    public function __construct($UserID)
    {
        $this->User_ID = $UserID;
    }

    public function up_nobility_level($Sales_Groupnow = 0)
    {
        $da_obj = new Dis_Account();
        $dis_config = Dis_Config::find(1);
        if (empty($dis_config->Pro_Title_Rate)) {
            return true;
        }

        $protitles = json_decode($dis_config->Pro_Title_Rate, true);
        $protitles_temp = array_reverse($protitles, true);

        //过滤不启用的爵位(名称为空)
        $protitless = array();
        foreach ($protitles_temp as $pk => $pv) {
            if ($pk != 'Level_Num') {
                if (!empty($pv['Name'])) {
                    $protitless[$pk] = $pv;
                }
            }
        }

        //爵位全部未启用
        if (empty($protitless)) return true;

        $disaccount = $da_obj->where('User_ID', $this->User_ID)
            ->where('status', 1)
            ->where('Level_ID', '>=', 2)
            ->first();
        if (empty($disaccount->Users_ID)) {
            return true;
        }

        //查询所有的上级
        $ancestors = $disaccount->getAncestorIds();
        array_push($ancestors, $this->User_ID);//加上上级分销商用户ID
        $ancestors = array_reverse($ancestors);

        foreach ($ancestors as $UID) {
            $user_distribute_account = $da_obj->where( 'User_ID', $UID)
                ->first(array('Professional_Title'));

            //团队当月销售额
            $accountObj = $da_obj->where('User_ID', $UID)->first();
            $posterity = $accountObj->getPosterity(0);
            $Sales_Group = $this->get_my_leiji_vip_sales($UID, $posterity, 1);
            //更新分销商的当月团队销售额
            $da_obj->where('User_ID',$UID)->update(array('Month_Group_Sale' => $Sales_Group));

            $level = 0;
            foreach ($protitless as $key => $item) {
                //获取下级分销商经理数量
                $ownercount = $this->get_next_pro_count($UID, $key);
                if (($item['check_money'] > 0 && $item['check_money'] <= $Sales_Group) || ($item['check_next'] > 0 && $item['check_next'] <= $ownercount)) {
                    $level = $key;
                    break;
                }
            }

            //更新分销商等级
            if ($level > $user_distribute_account->Professional_Title) {
                $da_obj->where('User_ID', $UID)->update(array('Professional_Title' => $level));
                continue;
            }
        }

        return true;
    }


    //获取下级分销商经理数量
    public function get_next_pro_count($userid, $protitle)
    {
        $ownercount = Dis_Account::where('status', 1)
            ->where('Professional_Title', $protitle - 1)
            ->where('Dis_Path', 'like', '%' . $userid . '%')
            ->where('Level_ID', '>=', 2)
            ->count();
        return $ownercount;
    }



    /**
     * 我的团队累计销售额,只识别门槛商品
     * @params UsersID string 对应管理员的唯一标识
     * @params UserID int 分销商的UserID
     * @params posterity object 根据分销商ID查询出的分销商对象,存储的是distribute_account表的部分字段
     * @param $time int 0代表累计销售额,1代表当月销售额
     * @return float 返回销售额金额
     */
    public function get_my_leiji_vip_sales($UserID, $posterity, $time = 0, $type = 0)
    {
        $uo_obj = new UserOrder();

        $first_date_str = date('Y-m-01', time());
        $first_date_unix = strtotime($first_date_str);//当月第一天时间戳
        $end_date_unix = strtotime("$first_date_str +1 month -1 day");//当月最后一天时间戳
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
        $dis_arr = Dis_Level::where('Level_LimitType', 2)
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
            $status = Dis_Config::select('Pro_Title_Status')->find(1);
        }
        if(isset($status) && $status['Pro_Title_Status'] == 4){
            $uo_obj = $uo_obj->where('Order_Status', 4);
        }else{
            $uo_obj = $uo_obj->where('Order_Status', '>=', 2);
        }

        $r2 = $uo_obj->select('Order_CartList')
            ->whereIn('Owner_ID', $posterityids)
            ->where('Is_Backup', 0)
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




    /**
     * 爵位奖金发放(会员提现审核通过后，其上级提取爵位奖)
     * @param $amount
     * @param $UsersID
     * @param $UserID
     */
    public function send_pro_bouns($amount)
    {
        $m_obj = new Member();
        $da_obj = new Dis_Account();
        $dc_obj = new Dis_Config();
        $um_obj = new User_Message();
        $dpr_obj = new Dis_Point_Record();

        $rsUser = $m_obj->find($this->User_ID);
        $rsDisAccount = $da_obj->where('User_ID', $this->User_ID)->first();

        $rsDisConfig = $dc_obj->select('Pro_Title_Rate')->find(1);
        $rsDisConfig = json_decode($rsDisConfig['Pro_Title_Rate'], true);
        $pro_record_arr = array();
        //提现用户必需有上级
        if (isset($rsUser) && $rsUser['Owner_Id'] > 0) {
            $ownerLast = $da_obj->where('User_ID', $rsUser['Owner_Id'])->first();

            if (isset($ownerLast)) {
                //一级会员被提奖励
                if ($ownerLast['Professional_Title'] > 0) {
                    //上级符合奖励条件，不管提现用户是什么爵位都按一级奖励
                    $strRate = $rsDisConfig[$ownerLast['Professional_Title']]['check_rate'];
                    $arrRate = explode(',', $strRate);
                    $pro_record_arr[] = [
                        'name' => $rsDisConfig[$ownerLast['Professional_Title']]['Name'],
                        'user_id' => $ownerLast['User_ID'],
                        'money' => $amount * ($arrRate[0] / 100)
                    ];
                }
                //一级以上有爵位的分销商提奖励
                $last_user = $m_obj->find($ownerLast['User_ID']);
                if (isset($last_user) && $last_user['Owner_Id'] > 0) {

                    //查找上级所有用户只限vip和总代
                    $ancestorids_str = $da_obj->getUserAncestorIds($last_user['Owner_Id']);
                    $ancestorids = explode(',', $ancestorids_str);
                    $ancestorids = array_unique($ancestorids);
                    $ancestorids = array_filter($ancestorids);

                    foreach ($ancestorids as $k => $v) {
                        $ownerAccount = $da_obj->where('User_ID', $v)->first();

                        //一级以上提现用户本身爵位级别要比奖励用户低
                        if (isset($rsDisAccount) && isset($ownerAccount) && $rsDisAccount['Professional_Title'] > 0 && $ownerAccount['Professional_Title'] > 0) {
                            $strRate = $rsDisConfig[$ownerAccount['Professional_Title']]['check_rate'];
                            $arrRate = explode(',', $strRate);
                            if ($rsDisAccount['Professional_Title'] < $ownerAccount['Professional_Title']) {
                                $pro_record_arr[] = [
                                    'name' => $rsDisConfig[$ownerAccount['Professional_Title']]['Name'],
                                    'user_id' => $ownerAccount['User_ID'],
                                    'money' => $amount * ($arrRate[$rsDisAccount['Professional_Title']] / 100)
                                ];
                            }
                        }

                    }

                }
            }
        }

        $record = array();
        $record_account_flag = true;
        foreach ($pro_record_arr as $key => $value) {
            $record_account_flag = $da_obj->where('User_ID', $value['user_id'])
                ->increment('balance', $value['money']);
            $record[] = [
                'Users_ID' => USERSID,
                'User_ID' => $value['user_id'],
                'orderid' => 0,
                'type' => 4,
                'money' => $value['money'],
                'status' => 2,
                'descr' => $value['name'] . '--团队业绩发放',
                'created_at' => time()
            ];

            //循环发送得奖消息
            $Data=array(
                "Message_Title"=>'恭喜您获取团队奖'.$value['money'].'元',
                "Message_Description"=>'下级有会员提现,您获取团队奖'.$value['money'].'元',
                "Message_CreateTime"=>time(),
                "Users_ID"=>USERSID,
                "User_ID"=>$value['user_id']
            );
            $um_obj->create($Data);

        }

        $flag_pro_record = $dpr_obj->insert($record);

        if ($flag_pro_record && $record_account_flag) {
            return true;
        }
        return false;

    }
}