<?php

namespace App\Http\Controllers\Api;

use App\Models\UserCharge;
use App\Models\UserMoneyRecord;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

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
}
