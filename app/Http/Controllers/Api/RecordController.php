<?php

namespace App\Http\Controllers\Api;

use App\Models\Dis_Account;
use App\Models\Dis_Account_Record;
use App\Models\Dis_Agent_Record;
use App\Models\Dis_Point_Record;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RecordController extends Controller
{
    /**
     * @api {get} /distribute/dis_record  财务(分销)明细
     * @apiGroup 佣金明细
     * @apiDescription 获取分销商财务（即分销奖）明细列表
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
     *     curl -i http://localhost:6002/api/distribute/dis_record
     *
     * @apiSampleRequest /api/distribute/dis_record
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
     *          "data": {
     *              "current_page": 1,   //当前页数
     *              "data": [
     *                  {
     *                      "Record_ID": 107,   //记录ID
     *                      "Ds_Record_ID": 118,
     *                      "User_ID": 1,  //会员ID
     *                      "level": 3,   //获取第几级佣金
     *                      "Record_Description": "下属分销商分销1个钙片¥30.00成功，获取奖金",   //佣金描述
     *                      "Record_CreateTime": "2018/09/13 18:35:37",   //时间
     *                      "Record_Money": "3.00",   //佣金金额
     *                      "Record_Status": 2,   //状态
     *                      "yonjin_desrecord": "1获三级佣金",
     *                      "rsbuyer_mobile": "139****2404",   //购买者手机号
     *                      "Record_Sn": "20180913167",   //订单编号
     *                      "status": "已完成"   //状态
     *                  },
     *              ],
     *              "from": 1,
     *              "last_page": 1,
     *              "next_page_url": null,   //下一页
     *              "path": "http://localhost:6002/api/distribute/dis_record",   //路径
     *              "per_page": 20,   //每页数量
     *              "prev_page_url": null,  //上一页
     *              "to": 6,
     *              "total": 6   //数据总数
     *          },
     *     }
     */
    public function dis_record(Request $request)
    {
        $input = $request->input();
        $rules = [
            'cur_page' => 'required|integer|min:1'
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            return json_encode(['status' => 0, 'msg' => $validator->messages()->first()]);
        }

        $dar_obj = new Dis_Account_Record();
        $m_obj = new Member();
        $level_name_record = config('level.level_name_record');

        //获取记录
        $recode_stutus = array('已生成', '已付款', '已完成');
        $filter = ['Record_ID','Ds_Record_ID', 'User_ID','level','Record_Description','Record_CreateTime', 'Record_Money', 'Record_Status'];
        $distribute_record = $dar_obj->select($filter)
            ->where('User_ID', $input['UserID'])
            ->where('Record_Status', 2)
            ->orderBy('Record_CreateTime','desc')
            ->paginate(10, ['*'], $input['cur_page']);

        foreach($distribute_record as $key => $item)
        {
            $names = $item['level'] == 110 ? '' : $level_name_record[$item['level']];
            $item['yonjin_desrecord'] = ($item['User']['User_NickName'] ? $item['User']['User_NickName'] : $item['User']['User_Mobile']) . '获' . $names . '佣金';
            $item['Record_Description'] = str_replace('，获取佣金','',$item['Record_Description']);
            $rsbuyer = $m_obj->select('User_Mobile')->find($item['DisRecord']['Buyer_ID']);
            $item['rsbuyer_mobile'] = substr($rsbuyer['User_Mobile'],0,3)."****".substr($rsbuyer['User_Mobile'],7,4);
            $item['Record_CreateTime'] = ldate($item['Record_CreateTime']);
            if(!empty($item['DisRecord'])){
                $item['Record_Sn'] = date("Ymd",$item['DisRecord']["Record_CreateTime"]).$item['DisRecord']["Order_ID"];
            }else{
                $item['Record_Sn'] = '';
            }
            $item['Record_Money'] = round_pad_zero($item['Record_Money'], 2);
            $item['status'] = $recode_stutus[$item['Record_Status']];

            unset($item['User'], $item['DisRecord']);
        }

        return json_encode(['status' => 1, 'msg' => 'success', 'data' => $distribute_record]);

    }


    /**
     * @api {get} /distribute/point_record  爵位奖明细
     * @apiGroup 佣金明细
     * @apiDescription 获取分销商爵位奖明细列表
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
     *     curl -i http://localhost:6002/api/distribute/point_record
     *
     * @apiSampleRequest /api/distribute/point_record
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
     *          "data": {
     *              "current_page": 1,   //当前页数
     *              "data": [
     *                  {
     *                      "id": 14,
     *                      "User_ID": 1,   //用户ID
     *                      "type": 4,
     *                      "orderid": 0,
     *                      "money": "22.50",   //金额
     *                      "status": "已完成",   //状态
     *                      "descr": "高级经理--团队业绩发放",   //佣金描述
     *                      "created_at": "2018/09/27 09:42:34"   //获得时间
     *                  },
     *              ],
     *              "from": 1,
     *              "last_page": 1,
     *              "next_page_url": null,   //下一页
     *              "path": "http://localhost:6002/api/distribute/point_record",   //路径
     *              "per_page": 20,   //每页数量
     *              "prev_page_url": null,  //上一页
     *              "to": 6,
     *              "total": 6   //数据总数
     *          },
     *     }
     */
    public function point_record(Request $request)
    {
        $input = $request->input();
        $rules = [
            'cur_page' => 'required|integer|min:1'
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            return json_encode(['status' => 0, 'msg' => $validator->messages()->first()]);
        }

        $dpr_obj = new Dis_Point_Record();
        $recode_status = array('已生成', '已付款', '已完成');

        //获取记录
        $records = $dpr_obj->where('User_ID', $input['UserID'])
            ->where('status', 2)
            ->where('type',4)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], $input['cur_page']);
        foreach($records as $key => $item){
            $item['created_at'] = ldate($item['created_at']);
            $item['status'] = $recode_status[$item['status']];
            $item['money'] = round_pad_zero($item['money'], 2);
        }

        return json_encode(['status' => 1, 'msg' => 'success', 'data' => $records]);
    }


    /**
     * @api {get} /distribute/agent_record  区域代理奖明细
     * @apiGroup 佣金明细
     * @apiDescription 获取分销商区域代理奖明细列表
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
     *     curl -i http://localhost:6002/api/distribute/agent_record
     *
     * @apiSampleRequest /api/distribute/agent_record
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
     *          "data": {
     *              "current_page": 1,   //当前页数
     *              "data": [
     *                  {
     *                      "Record_ID": 14,   //记录ID
     *                      "Account_ID": 1,   //分销账户ID
     *                      "Order_Sn": "45591254",  //订单编号
     *                      "Order_ID": 158,   //订单ID
     *                      "Record_Money": "22.50",   //佣金金额
     *                      "Products_PriceX": "22.50",   //商品价格
     *                      "Products_Qty": "1",   //商品数量
     *                      "Products_Name": "VIP会员商品",   //商品名称
     *                      "Record_CreateTime": "2018/09/27 09:42:34"   //获得时间
     *                  },
     *              ],
     *              "from": 1,
     *              "last_page": 1,
     *              "next_page_url": null,   //下一页
     *              "path": "http://localhost:6002/api/distribute/agent_record",   //路径
     *              "per_page": 20,   //每页数量
     *              "prev_page_url": null,  //上一页
     *              "to": 6,
     *              "total": 6   //数据总数
     *          },
     *     }
     */
    public function agent_record(Request $request)
    {
        $input = $request->input();
        $rules = [
            'cur_page' => 'required|integer|min:1'
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            return json_encode(['status' => 0, 'msg' => $validator->messages()->first()]);
        }

        $dar_obj = new Dis_Agent_Record();
        $da_obj = new Dis_Account();
        $account = $da_obj->select('Account_ID')
            ->where('User_ID', $input['UserID'])
            ->first();

        $filter = [
            'Record_ID','Account_ID','Record_Money', 'Record_CreateTime', 'Order_ID',
            'Products_Name','Products_Qty','Products_PriceX'
        ];
        $records = $dar_obj->where('Account_ID', $account['Account_ID'])
            ->orderByDesc('Record_ID')
            ->paginate(10, $filter , $input['cur_page']);

        foreach($records as $key => $record){
            $record['Record_Money'] = round_pad_zero($record['Record_Money'],2);
            $record['Record_CreateTime'] = sdate($record['Record_CreateTime']);
            $record['Order_Sn'] = date("Ymd",$record["Order_CreateTime"]).$record['Order_ID'];
        }

        return json_encode(['status' => 1, 'msg' => 'success', 'data' => $records]);

    }



    /**
     * @api {get} /distribute/push_record  推荐奖明细
     * @apiGroup 佣金明细
     * @apiDescription 获取分销商推荐奖明细列表
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
     *     curl -i http://localhost:6002/api/distribute/push_record
     *
     * @apiSampleRequest /api/distribute/push_record
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
     *          "data": {
     *              "current_page": 1,   //当前页数
     *              "data": [
     *                  {
     *                      "id": 14,
     *                      "User_ID": 1,   //用户ID
     *                      "type": 4,
     *                      "orderid": 16,   //订单ID
     *                      "money": "22.50",   //金额
     *                      "status": "已完成",   //状态
     *                      "descr": "下级开通市级合伙人您获得佣金",   //佣金描述
     *                      "created_at": "2018/09/27 09:42:34"   //获得时间
     *                      "user_name": "grace"   //得奖者名称
     *                  },
     *              ],
     *              "from": 1,
     *              "last_page": 1,
     *              "next_page_url": null,   //下一页
     *              "path": "http://localhost:6002/api/distribute/push_record",   //路径
     *              "per_page": 20,   //每页数量
     *              "prev_page_url": null,  //上一页
     *              "to": 6,
     *              "total": 6   //数据总数
     *          },
     *     }
     */
    public function push_record(Request $request)
    {
        $input = $request->input();
        $rules = [
            'cur_page' => 'required|integer|min:1'
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            return json_encode(['status' => 0, 'msg' => $validator->messages()->first()]);
        }

        $dpr_obj = new Dis_Point_Record();
        $recode_status = array('已生成', '已付款', '已完成');

        //获取记录
        $records = $dpr_obj->where('User_ID', $input['UserID'])
            ->where('status', 2)
            ->where('type',3)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], $input['cur_page']);

        foreach($records as $key => $item){
            $item['created_at'] = ldate($item['created_at']);
            $item['status'] = $recode_status[$item['status']];
            $item['money'] = round_pad_zero($item['money'], 2);
            $item['user_name'] = $item['user']['User_NickName'] ? $item['user']['User_NickName'] : $item['user']['User_Mobile'];

            unset($item['user']);
        }

        return json_encode(['status' => 1, 'msg' => 'success', 'data' => $records]);
    }



    /**
     * @api {get} /distribute/resale_record  重消奖明细
     * @apiGroup 佣金明细
     * @apiDescription 获取分销商重消奖明细列表
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
     *     curl -i http://localhost:6002/api/distribute/resale_record
     *
     * @apiSampleRequest /api/distribute/resale_record
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
     *          "data": {
     *              "current_page": 1,   //当前页数
     *              "data": [
     *                  {
     *                      "id": 14,
     *                      "User_ID": 1,   //用户ID
     *                      "type": 4,
     *                      "orderid": 65,  //订单ID
     *                      "money": "22.50",   //金额
     *                      "status": "已完成",   //状态
     *                      "descr": "下级有会员充值积分,您获取重消奖",   //佣金描述
     *                      "created_at": "2018/09/27 09:42:34"   //获得时间
     *                      "user_name": "grace"   //得奖者名称
     *                  },
     *              ],
     *              "from": 1,
     *              "last_page": 1,
     *              "next_page_url": null,   //下一页
     *              "path": "http://localhost:6002/api/distribute/resale_record",   //路径
     *              "per_page": 20,   //每页数量
     *              "prev_page_url": null,  //上一页
     *              "to": 6,
     *              "total": 6   //数据总数
     *          },
     *     }
     */
    public function resale_record(Request $request)
    {
        $input = $request->input();
        $rules = [
            'cur_page' => 'required|integer|min:1'
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            return json_encode(['status' => 0, 'msg' => $validator->messages()->first()]);
        }

        $dpr_obj = new Dis_Point_Record();
        $recode_status = array('已生成', '已付款', '已完成');

        //获取记录
        $records = $dpr_obj->where('User_ID', $input['UserID'])
            ->where('status', 2)
            ->where('type',2)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], $input['cur_page']);

        foreach($records as $key => $item){
            $item['created_at'] = ldate($item['created_at']);
            $item['status'] = $recode_status[$item['status']];
            $item['money'] = round_pad_zero($item['money'], 2);
            $item['user_name'] = $item['user']['User_NickName'] ? $item['user']['User_NickName'] : $item['user']['User_Mobile'];

            unset($item['user']);
        }

        return json_encode(['status' => 1, 'msg' => 'success', 'data' => $records]);
    }
}
