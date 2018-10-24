<?php

namespace App\Http\Controllers\Api;

use App\Models\Dis_Account;
use App\Models\Dis_Point_Record;
use App\Models\Member;
use App\Models\ShopConfig;
use App\Models\User_Config;
use App\Models\User_Message;
use App\Models\UserCharge;
use App\Models\UserIntegralRecord;
use App\Models\UserMoneyRecord;
use App\Models\UsersPayConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IntegralController extends Controller
{
    /**
     * @api {get} /center/integral_info  积分信息
     * @apiGroup 积分
     * @apiDescription 获取用户积分信息
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID      用户ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/integral_info
     *
     * @apiSampleRequest /api/center/integral_info
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "缺少必要的参数UserID",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "成功",
     *          "data": {
     *                      "is_sign": 1,   //是否开启签到功能
     *                      "today_sign": 0,   //今天是否已签到
     *                      "sign_num": 0,   //签到总次数
     *                      "integral": 20   //当前积分数
     *                      "integral_rate": 5   //积分充值比例1:5，即1元=5积分
     *                      "integral_payment": {
     *                          "PaymentWxpayEnabled": 1,  //是否启用微信支付（1:是，0:否）
     *                          "Payment_AlipayEnabled": 0    //是否启用支付宝支付（1:是，0:否）
     *                       }
     *                  },
     *     }
     */
    public function integral_info(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID'
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $uc_obj = new User_Config();
        $m_obj = new Member();
        $uir_obj = new UserIntegralRecord();
        $sc_obj = new ShopConfig();
        $upc_obj = new UsersPayConfig();

        $rsConfig = $uc_obj->select('IsSign')->find(USERSID);
        $user = $m_obj->select('User_Integral')->find($input['UserID']);
        $sign = $uir_obj->where('Record_Type', 0)->where('User_ID', $input['UserID'])->get();
        $is_sign = $rsConfig['IsSign'];
        $sign_num = count($sign);
        if(isset($sign[$sign_num-1]) > 0 && $sign[$sign_num-1]['Record_CreateTime'] > strtotime(date("Y-m-d 00:00:00"))){
            $today_sign = 1;
        }else{
            $today_sign = 0;
        }
        $rsConfig = $sc_obj->select('moneytoscore')->find(USERSID);
        $rsPay = $upc_obj->select('PaymentWxpayEnabled','Payment_AlipayEnabled')->find(USERSID);

        $data = [
            'status' => 1,
            'msg' => '成功',
            'data' => [
                'is_sign' => $is_sign,
                'today_sign' => $today_sign,
                'sign_num' => $sign_num,
                'integral' => $user['User_Integral'],
                'integral_rate' => $rsConfig['moneytoscore'],
                'integral_payment' => $rsPay,
            ]
        ];
        return json_encode($data);

    }


    /**
     * @api {get} /center/integral_record  积分明细
     * @apiGroup 积分
     * @apiDescription 获取用户积分明细
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   cur_page=1      当前页数
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/integral_record
     *
     * @apiSampleRequest /api/center/integral_record
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "缺少必要的参数UserID",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "成功",
     *          "data": {
     *              "current_page": 1,  //当前页
     *              "data": [
     *                  {
     *                      "User_ID": "17",   //用户ID
     *                      "Record_ID": 47,   //明细ID
     *                      "Record_Integral": 20,   //积分数
     *                      "Operator_UserName": "",   //后台手动修改积分操作员名称
     *                      "Record_Type": 7,   //积分类型
     *                      "Record_Description": "注册得积分",
     *                      "Record_CreateTime": "2018-10-15 10:53:54",   //记录时间
     *                   }
     *               ],
     *              "from": 1,
     *              "last_page": 1,  //上一页
     *              "next_page_url": null,  //下一页
     *              "path": "http://localhost:6002/api/center/integral_record",
     *              "per_page": 15,   //每页数量
     *              "prev_page_url": null,
     *              "to": 1,
     *              "total": 1   //消息总数
     *              },
     *     }
     */
    public function integral_record(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'cur_page' => 'required|integer|min:1',
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $uir_obj = new UserIntegralRecord();
        $cur_page = isset($input['cur_page']) ? intval($input['cur_page']) : 1;
        $lists = $uir_obj->where('User_ID', $input['UserID'])
            ->orderByDesc('Record_ID')
            ->paginate(20, ['*'], $cur_page);
        foreach($lists as $key => $value){
            $value['Record_CreateTime'] = date("Y-m-d H:i:s", $value['Record_CreateTime']);
        }

        $data = ['status' => 1, 'msg' => '成功', 'data' => $lists];
        return json_encode($data);

    }



    /**
     * @api {post} /center/do_sign  签到
     * @apiGroup 积分
     * @apiDescription 用户签到获取积分
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID      用户ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/do_sign
     *
     * @apiSampleRequest /api/center/do_sign
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "缺少必要的参数UserID",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "成功"
     *     }
     */
    public function do_sign(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'cur_page' => 'nullable|integer|min:1',
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $uir_obj = new UserIntegralRecord();
        $now_times = strtotime(date("Y-m-d 00:00:00"));
        $rsSign = $uir_obj->where('Record_CreateTime', '>', $now_times)
            ->where('Record_Type', 0)
            ->where('User_ID', $input['UserID'])
            ->orderByDesc('Record_CreateTime')
            ->first();
        if ($rsSign) {
            $data = ['status' => 0, 'msg' => '今天已签到'];
            return json_encode($data);
        } else {
            $uc_obj = new User_Config();
            $m_obj = new Member();

            $rsConfig = $uc_obj->select('IsSign', 'SignIntegral')->find(USERSID);
            $user = $m_obj->find($input['UserID']);
            //增加
            $int_Data = array(
                'Record_Integral' => $rsConfig['SignIntegral'],
                'Record_SurplusIntegral' => $user['User_Integral'] + $rsConfig['SignIntegral'],
                'Operator_UserName' => '',
                'Record_Type' => 0,
                'Record_Description' => '每日签到领取积分',
                'Record_CreateTime' => time(),
                'Users_ID' => USERSID,
                'User_ID' => $input['UserID']
            );
            $Flag = $uir_obj->create($int_Data);


            $user['User_TotalIntegral'] += $rsConfig['SignIntegral'];
            $user['User_Integral'] += $rsConfig['SignIntegral'];
            $Flag1 = $user->save();

            if ($Flag1 && $Flag) {
                $data = array(
                    'status' => 1,
                    'msg' => '成功',
                    'data' => [
                        'integral' => $user['User_Integral']
                    ],
                );
            } else {
                $data = ['status' => 0, 'msg' => '失败'];
            }
        }

        return json_encode($data);

    }


    /**
     * @api {post} /center/integral_largess  积分转赠
     * @apiGroup 积分
     * @apiDescription 积分转赠
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {String}   to_mobile       对方手机号
     * @apiParam {Number}   Amount          转赠积分数
     * @apiParam {String}   pay_password    用户支付密码
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/integral_largess
     *
     * @apiSampleRequest /api/center/integral_largess
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "缺少必要的参数UserID",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "成功"
     *     }
     */
    public function integral_largess(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'to_mobile' => "required|exists:user,User_Mobile",
            'Amount' => 'required|integer|min:1',
            'pay_password' => 'required',
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'to_mobile.required' => '对方手机号不能为空',
            'to_mobile.exists' => '对方手机号错误',
            'Amount.required' => '转赠积分数不能为空',
            'Amount.integer' => '转赠积分数必需是大于0的整数',
            'pay_password.required' => '支付密码不能为空',
        ];
        $validator = Validator::make($input, $rules, $message);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $m_obj = new Member();
        $uir_obj = new UserIntegralRecord();

        $rsUser = $m_obj->find($input['UserID']);
        $reciever = $m_obj->where('User_Mobile', $input['to_mobile'])->first();

        //检查是否为自己给自己转账
        if ($reciever['User_ID'] == $rsUser['User_ID']) {
            $Data = array(
                'status' => 0,
                'msg' => '不能给自己转账',
            );
            return json_encode($Data);
        }

        $amount = intval($input["Amount"]);
        if ($amount > $rsUser["User_Integral"]) {
            $Data = array(
                'status' => 0,
                'msg' => '转帐积分数不能大于你的当前积分'
            );
            return json_encode($Data);
        }

        if ($rsUser["User_PayPassword"] != md5($input["pay_password"])) {
            $Data = array(
                'status' => 0,
                'msg' => '支付密码不正确'
            );
            return json_encode($Data);
        }

        //更新用户余额
        $rsUser['User_Integral'] -= $amount;
        $user_Set = $rsUser->save();

        //减少自己余额记录
        $Data1 = array(
            'Record_Integral' => $amount,
            'Record_SurplusIntegral' => $rsUser['User_Integral'],
            'Operator_UserName' => '',
            'Record_Type' => 6,
            'Record_Description' => '转赠给会员'.$reciever['User_Mobile'].'积分',
            'Record_CreateTime' => time(),
            'Users_ID' => USERSID,
            'User_ID' => $input['UserID']
        );
        $user_Add = $uir_obj->create($Data1);

        //增加对方余额
        $Data2 = array(
            'Record_Integral' => $amount,
            'Record_SurplusIntegral' => $reciever['User_Integral'] + $amount,
            'Operator_UserName' => '',
            'Record_Type' => 6,
            'Record_Description' => '会员'.$rsUser['User_Mobile'].'转赠积分',
            'Record_CreateTime' => time(),
            'Users_ID' => USERSID,
            'User_ID' => $reciever['User_ID']
        );
        $Add = $uir_obj->create($Data2);

        //更新对方用户余额
        $reciever['User_Integral'] += $amount;
        $reciever['User_TotalIntegral'] += $amount;
        $Set = $reciever->save();

        if ($Add && $Set && $user_Add && $user_Set) {
            $data = ["status" => 1, "msg" => '转帐成功'];
        } else {
            $data = ["status" => 0, "msg" => '转帐失败'];
        }

        return json_encode($data);

    }


    /**
     * @api {post} /center/integral_charge  积分充值
     * @apiGroup 积分
     * @apiDescription 用户充值积分
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID              用户ID
     * @apiParam {Number}   Operator            支付方式（1:微支付，2:支付宝，3:余额支付）
     * @apiParam {Number}   PayAmount           支付金额（整数，最小值为1）
     * @apiParam {String}   [PayPassword]       用户支付密码(余额支付时必填)
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/integral_charge
     *
     * @apiSampleRequest /api/center/integral_charge
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "缺少必要的参数UserID",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "成功"
     *     }
     */
    public function integral_charge(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'Operator' => 'required|integer|in:1,2,3',
            'PayAmount' => 'required|integer|min:1',
            'PayPassword' => 'required_if:Operator,3',
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $PaymentMethod = array(
            "1" => "微支付",
            "2" => "支付宝",
            "3" => "余额支付",
        );

        $m_obj = new Member();
        $rsUser = $m_obj->find($input['UserID']);

        $charge_data = array(
            'Users_ID' => USERSID,
            'User_ID' => $input['UserID'],
            'Amount' => $input['PayAmount'],
            'Total' => $rsUser['User_Integral'] + $input['PayAmount'],
            'Operator' => $PaymentMethod[$input["Operator"]] . "充值积分 +" . $input['PayAmount'],
            'Status' => 2,
            'CreateTime' => time()
        );
        $uc_obj = new UserCharge();
        $charge = $uc_obj->create($charge_data);

        if($charge){
            if($input['Operator'] == 1){
                $data = $this->wx_pay($input['PayAmount'], $rsUser, $input['PayPassword'],$charge['Item_ID']);
            }elseif($input['Operator'] == 2){
                $data = $this->ali_pay($input['PayAmount'], $rsUser, $input['PayPassword'],$charge['Item_ID']);
            }elseif($input['Operator'] == 3){
                $data = $this->balance_pay($input['PayAmount'], $rsUser, $input['PayPassword'],$charge['Item_ID']);
            }else{
                $data = ['status' => 0, 'msg' => '充值方式不存在'];
            }
        }else{
            $data = ['status' => 0, 'msg' => '积分充值失败'];
        }

        return json_encode($data);
    }

    //余额充值积分，相关处理
    private function balance_pay($amount, $rsUser, $pay_password, $ItemID)
    {
        if($amount > $rsUser['User_Money']){
            $data = ['status' => 0, 'msg' => '您的余额不足，请先充值余额'];
            return $data;
        }
        if(md5($pay_password) != $rsUser['User_PayPassword']){
            $data = ['status' => 0, 'msg' => '支付密码不正确，请重新输入'];
            return $data;
        }

        $sc_obj = new ShopConfig();
        $rsConfig=$sc_obj->select('moneytoscore','Shop_Resale_Reward_Json')->find(USERSID);
        $integral = intval($amount * $rsConfig['moneytoscore']);
        try {
            DB::beginTransaction();
            //修改用户积分和余额
            $rsUser['User_Integral'] += $integral;
            $rsUser['User_Money'] -= $amount;
            $user_flag = $rsUser->save();
            //生成资金流水记录
            $money_record_data = array(
                'Users_ID' => USERSID,
                'User_ID' => $rsUser['User_ID'],
                'Type' => 5,//使用余额充值积分
                'Amount' => $amount,
                'Total' => $rsUser['User_Money'],
                'Note' => "使用余额充值积分-".number_format($amount,2,".",""),
                'CreateTime' => time()
            );
            $umr_obj = new UserMoneyRecord();
            $money_record = $umr_obj->create($money_record_data);
            //生成积分充值记录
            $integral_record_data = array(
                'Record_Integral' => $integral,
                'Record_SurplusIntegral' => $rsUser['User_Integral'],
                'Operator_UserName' => '',
                'Record_Type' => 5,
                'Record_Description' => '使用余额充值积分',
                'Record_CreateTime' => time(),
                'Users_ID' => USERSID,
                'User_ID' => $rsUser['User_ID']
            );
            $uir_obj = new UserIntegralRecord();
            $record_flag = $uir_obj->create($integral_record_data);

            //重消奖
            if (!empty($rsConfig['Shop_Resale_Reward_Json'])) {
                $this->resale_bonus($rsConfig['Shop_Resale_Reward_Json'], $rsUser,$amount,$ItemID);
            }

            if($user_flag && $money_record && $record_flag){
                DB::commit();
                $data = ['status' => 1, 'msg' => '执行成功'];
            } else {
                DB::rollBack();
                $data = ['status' => 0, 'msg' => '执行失败'];
            }
        } catch (\Exception $e) {
            $data = ['errorCode' => 0, 'msg' => $e->getMessage()];
        }

        return $data;
    }


    /**
     * 微信充值积分，相关处理
     */
    private function wx_pay($amount, $rsUser, $pay_password, $ItemID)
    {

    }


    /**
     * 支付宝充值积分，相关处理
     */
    private function ali_pay($amount, $rsUser, $pay_password, $ItemID)
    {

    }


    /**
     * 重消奖
     */
    private function resale_bonus($resale_json,$rsUser,$amount,$ItemID)
    {
        $resale_config = json_decode($resale_json, true);
        //写入重消奖记录开始
        $da_obj = new Dis_Account();

        //new 获取所有上级分销商
        if($rsUser['Owner_Id'] > 0){
            $ids_str = $da_obj->getUserAncestorIds($rsUser['Owner_Id']);
            $ance_id = explode(',',$ids_str);
            $ance_id = array_slice($ance_id, 0,count($ance_id)-1);
        }else{
            $ance_id = [];
        }

        //可用于发放重消奖的金额
        $all_money = $amount * $resale_config['all_cent'] / 100;
        //已经发放了多少金额
        $from_money = 0;
        //用来记数,判断是否全部更新成功
        $dis_from = 0;
        //分销商总数
        $ance_id = array_unique($ance_id);//去除数组中重复的值

        foreach ($ance_id as $k => $v) {
            if ($all_money <= $from_money) {
                break;
            }
            $money = ($all_money - $from_money) * $resale_config['one_cent'] / 100;
            $cx_one = [
                'Users_ID' => USERSID,
                'User_ID' => $v,
                'orderid' => $ItemID,
                'money' => $money,
                'type' => 2,
                'status' => 2,
                'descr' => '下级有会员充值积分,您获取重消奖',
                'created_at' => time()
            ];

            $from_money += $money;
            //循环给分销商写入可提现佣金
            $dis_update_flag =  $da_obj->where('User_ID', $v)->increment('balance', $money);
            if (false !== $dis_update_flag) {
                $dis_from++;
            }

            $dis_point_model = new Dis_Point_Record();
            $dis_point_model->create($cx_one);

            //循环发送得奖消息
            $message_data = array(
                "Message_Title"=>'恭喜您获取重消奖'.$money.'元',
                "Message_Description"=>'下级有会员充值积分,您获取重消奖'.$money.'元',
                "Message_CreateTime"=>time(),
                "Users_ID"=>USERSID,
                "User_ID"=>$v
            );
            $um_obj = new User_Message();
            $um_obj->create($message_data);
        }
    }

}
