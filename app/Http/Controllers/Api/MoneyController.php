<?php

namespace App\Http\Controllers\Api;

use App\Models\Member;
use App\Models\UserCharge;
use App\Models\UserMoneyRecord;
use App\Services\ServicePay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yansongda\Pay\Pay;

class MoneyController extends Controller
{
    /**
     * @api {get} /center/charge_record  充值记录
     * @apiGroup 余额
     * @apiDescription 获取用户充值记录
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
     *     curl -i http://localhost:6002/api/center/charge_record
     *
     * @apiSampleRequest /api/center/charge_record
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
     *                      "Item_ID": 23,  //记录ID
     *                      "User_ID": 1,   //用户ID
     *                      "Amount": "100.00",
     *                      "Total": "3600.00",
     *                      "Operator": "余额支付充值积分 +100",  //记录描述
     *                      "Status": 2,
     *                      "CreateTime": "2018/08/14"   //日期
     *                   }
     *               ],
     *              "from": 1,
     *              "last_page": 1,  //上一页
     *              "next_page_url": null,  //下一页
     *              "path": "http://localhost:6002/api/center/charge_record",
     *              "per_page": 15,   //每页数量
     *              "prev_page_url": null,
     *              "to": 1,
     *              "total": 1   //消息总数
     *              },
     *     }
     */
    public function charge_record(Request $request)
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

        $cur_page = isset($input['cur_page']) ? $input['cur_page'] : 1;
        $uc_obj = new UserCharge();

        $list = $uc_obj->where('User_ID', $input['UserID'])
            ->orderByDesc('Item_ID')
            ->paginate(20, ['*'], $cur_page);

        foreach($list as $key => $value){
            $value['CreateTime'] = date('Y/m/d', $value['CreateTime']);
        }

        $data = ['status' => 1, 'msg'=> '成功', 'data' => $list];

        return json_encode($data);
    }


    /**
     * @api {get} /center/money_record  资金流水
     * @apiGroup 余额
     * @apiDescription 获取用户资金流水列表
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
     *     curl -i http://localhost:6002/api/center/money_record
     *
     * @apiSampleRequest /api/center/money_record
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
     *                      "Item_ID": 23,  //记录ID
     *                      "User_ID": 1,   //用户ID
     *                      "Amount": "-338.00",
     *                      "Total": "3600.00",
     *                      "Note": "商城购买支出 -338.00 (订单号:11)",  //记录描述
     *                      "Type": 0,
     *                      "CreateTime": "2018/08/14"   //日期
     *                   }
     *               ],
     *              "from": 1,
     *              "last_page": 1,  //上一页
     *              "next_page_url": null,  //下一页
     *              "path": "http://localhost:6002/api/center/money_record",
     *              "per_page": 15,   //每页数量
     *              "prev_page_url": null,
     *              "to": 1,
     *              "total": 1   //消息总数
     *              },
     *     }
     */
    public function money_record(Request $request)
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

        $cur_page = isset($input['cur_page']) ? $input['cur_page'] : 1;
        $umr_obj = new UserMoneyRecord();

        $list = $umr_obj->where('User_ID', $input['UserID'])
            ->orderByDesc('CreateTime')
            ->paginate(20, ['*'], $cur_page);

        foreach($list as $key => $value){
            $value['CreateTime'] = date('Y/m/d', $value['CreateTime']);
        }

        $data = ['status' => 1, 'msg'=> '成功', 'data' => $list];

        return json_encode($data);
    }


    /**
     * @api {post} /center/money_charge  余额充值
     * @apiGroup 余额
     * @apiDescription 用户充值余额
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID              用户ID
     * @apiParam {Number}   Operator            支付方式（1:微支付，2:支付宝）
     * @apiParam {Number}   PayAmount           支付金额（整数，最小值为1）
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/money_charge
     *
     * @apiSampleRequest /api/center/money_charge
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
    public function money_charge(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'Operator' => 'required|integer|in:1,2',
            'PayAmount' => 'required|integer|min:1',
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $PaymentMethod = array(
            "1" => "微支付",
            "2" => "支付宝",
        );

        $u_obj = new Member();
        $uc_obj = new UserCharge();
        $pay = new ServicePay();

        $rsUser = $u_obj->find($input['UserID']);
        if ($rsUser) {
            $charge_data = array(
                'Users_ID' => USERSID,
                'User_ID' => $input['UserID'],
                'Amount' => $input['PayAmount'],
                'Total' => $rsUser['User_Money'] + $input['PayAmount'],
                'Operator' => $PaymentMethod[$input["Operator"]] . "充值 +" . $input['PayAmount'],
                'CreateTime' => time()
            );
            $charge = $uc_obj->create($charge_data);

            if ($charge) {
                $data = ['status' => 1, 'msg' => '充值成功'];
                $pay_subject = "(会员:" . $charge['User_ID'] . ")在线充值余额，充值编号:" . $charge['Item_ID'];
                if ($input['Operator'] == 1) {

                    $data = $pay->wx_pay($input['PayAmount'], $charge['Item_ID'], 3, $pay_subject);

                } elseif ($input['Operator'] == 2) {

                    $data = $pay->ali_pay($input['PayAmount'], $charge['Item_ID'], 3, $pay_subject);

                }
            } else {
                $data = ['status' => 0, 'msg' => '充值失败'];
            }
        } else {
            $data = ['status' => 0, 'msg' => '充值失败'];
        }

        return json_encode($data);
    }


    //微信回调
    public function money_wx_notify(Request $request,$itemid)
    {
        $wxp_obj = new ServicePay();
        $notify_url = $_SERVER['HTTP_HOST'] . "/api/center/money_wx_notify/{$itemid}";
        $config = $wxp_obj->wx_config($notify_url);
        $pay = new Pay($config);
        $verify = $pay->driver('wechat')->gateway('wap')->verify($request->getContent());

        if ($verify) {
            $uc_obj = new UserCharge();
            $m_obj = new Member();
            $umr_obj = new UserMoneyRecord();

            $rsCharge = $uc_obj->find($itemid);
            $rsUser = $m_obj->find($rsCharge['User_ID']);
            $amount = $verify['total_fee'];

            try {
                DB::beginTransaction();
                //修改用户余额
                $rsUser['User_Money'] += $amount;
                $user_flag = $rsUser->save();

                $rsCharge['Status'] = 1;
                $rsCharge->save();

                $money_data = array(
                    'Users_ID' => USERSID,
                    'User_ID' => $rsCharge['User_ID'],
                    'Type' => 4,//充值账户余额
                    'Amount' => $rsCharge['Amount'],
                    'Total' => $rsCharge['Total'],
                    'Note' => "微信支付充值余额+".$rsCharge['Amount'],
                    'CreateTime' => time()
                );
                $money_record = $umr_obj->create($money_data);

                if ($user_flag && $money_record) {
                    DB::commit();
                    $data = ['status' => 1, 'msg' => '执行成功', 'data' => $verify];
                } else {
                    DB::rollBack();
                    $data = ['status' => 0, 'msg' => '执行失败'];
                }
            } catch (\Exception $e) {
                $data = ['status' => 0, 'msg' => $e->getMessage()];
            }
        } else {
            $data = ['status' => 0, 'msg' => '执行失败'];
//            file_put_contents(storage_path('notify.txt'), "收到异步通知\r\n", FILE_APPEND);
        }

        return json_encode($data);
    }


    //支付宝同步回调
    public function money_ali_return(Request $request,$itemid)
    {
        $wxp_obj = new ServicePay();
        $notify_url = $_SERVER['HTTP_HOST'] . "/api/center/money_ali_notify/{$itemid}";
        $return_url = $_SERVER['HTTP_HOST'] . "/api/center/money_ali_return/{$itemid}";
        $config = $wxp_obj->ali_config($notify_url,$return_url);
        $pay = new Pay($config);
        $verify = $pay->driver('alipay')->gateway()->verify($request->all());

        return $verify;
    }

    //支付宝回调
    public function money_ali_notify(Request $request,$itemid)
    {
        $wxp_obj = new ServicePay();
        $notify_url = $_SERVER['HTTP_HOST'] . "/api/center/money_ali_notify/{$itemid}";
        $return_url = $_SERVER['HTTP_HOST'] . "/api/center/money_ali_return/{$itemid}";
        $config = $wxp_obj->ali_config($notify_url, $return_url);
        $pay = new Pay($config);
        $verify = $pay->driver('alipay')->gateway()->verify($request->all());

        if ($verify) {
            $uc_obj = new UserCharge();
            $m_obj = new Member();
            $umr_obj = new UserMoneyRecord();

            $rsCharge = $uc_obj->find($itemid);
            $rsUser = $m_obj->find($rsCharge['User_ID']);
            $amount = $verify['total_fee'];

            try {
                DB::beginTransaction();
                //修改用户余额
                $rsUser['User_Money'] += $amount;
                $user_flag = $rsUser->save();

                $rsCharge['Status'] = 1;
                $rsCharge->save();

                $money_data = array(
                    'Users_ID' => USERSID,
                    'User_ID' => $rsCharge['User_ID'],
                    'Type' => 4,//充值账户余额
                    'Amount' => $rsCharge['Amount'],
                    'Total' => $rsCharge['Total'],
                    'Note' => "支付宝支付充值余额+".$rsCharge['Amount'],
                    'CreateTime' => time()
                );
                $money_record = $umr_obj->create($money_data);

                if ($user_flag && $money_record) {
                    DB::commit();
                    $data = ['status' => 1, 'msg' => '执行成功', 'data' => $verify];
                } else {
                    DB::rollBack();
                    $data = ['status' => 0, 'msg' => '执行失败'];
                }
            } catch (\Exception $e) {
                $data = ['status' => 0, 'msg' => $e->getMessage()];
            }
        } else {
            $data = ['status' => 0, 'msg' => '执行失败'];
//            file_put_contents(storage_path('notify.txt'), "收到异步通知\r\n", FILE_APPEND);
        }

        return json_encode($data);
    }
}
