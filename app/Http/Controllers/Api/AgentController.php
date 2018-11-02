<?php

namespace App\Http\Controllers\Api;

use App\Models\Agent_Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AgentController extends Controller
{
    /**
     * @api {get} /distribute/agent_apply_list  区域代理申请记录
     * @apiGroup 区域代理
     * @apiDescription 获取分销商区域代理申请记录
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/distribute/agent_apply_list
     *
     * @apiSampleRequest /api/distribute/agent_apply_list
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "成功",
     *          "data": [
     *              {
     *                  "Order_ID": 14,   //订单ID
     *                  "User_ID": 1,   //用户ID
     *                  "Area_Concat": "河北",   //申请区域
     *                  "Order_Status": 3,  //申请状态代码
     *                  "status": 3,  //申请状态
     *                  "Refuse_Be": "拒绝",   //拒绝原因
     *                  "Order_TotalPrice": "3000000.00",   //订单金额
     *                  "Order_CreateTime": "2018-08-29 13:13:10",   //订单创建时间
     *              },
     *          ],
     *     }
     */
    public function agent_apply_list(Request $request)
    {
        $input = $request->input();

        $ao_obj = new Agent_Order();
        $status = ['待审核','待付款','已完成','已拒绝'];

        $filter = ['User_ID','Order_ID','Area_Concat','Order_Status','Order_CreateTime','Refuse_Be','Order_TotalPrice'];
        $list = $ao_obj->select($filter)
            ->where('User_ID', $input['UserID'])
            ->where('Order_Status', '<>', 3)
            ->orderByDesc('Order_CreateTime')
            ->get();

        foreach($list as $key => $v){
            $v['Order_CreateTime'] = date('Y-m-d H:i:s', $v['Order_CreateTime']);
            $v['status'] = $status[$v['Order_Status']];
        }

        return json_encode(['status' => 1, 'msg' => 'success', 'data' => $list]);

    }
}
