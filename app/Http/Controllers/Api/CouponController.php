<?php

namespace App\Http\Controllers\Api;

use App\Models\User_Coupon;
use App\Models\User_Coupon_Record;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    /**
     * @api {get} /center/my_coupon 我的优惠券
     * @apiGroup 优惠券
     * @apiDescription 获取用户优惠券列表
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
     *     curl -i http://localhost:6002/api/center/my_coupon
     *
     * @apiSampleRequest /api/center/my_coupon
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
     *                      "User_ID": 1, //用户ID
     *                      "Record_ID": 3,  //记录ID
     *                      "Coupon_ID": 1,   //优惠券ID
     *                      "Coupon_UsedTimes": 0,   //优惠券使用次数（-1:不限次数）
     *                      "Record_CreateTime": "2018-07-31 19:17:38",
     *                      "Coupon_UseArea": 0,  //优惠券使用范围
     *                      "Coupon_UseType": 0,   //优惠券类型(0:折扣，1:现金)
     *                      "Coupon_Condition": 0,   //一次购满多少可用
     *                      "Coupon_Discount": "0.00",  //优惠券折扣
     *                      "Coupon_Cash": 0,  //优惠券可抵现金
     *                      "Coupon_StartTime": "2018-07-31 19:16:44",  //开始时间
     *                      "Coupon_EndTime": "2018-08-07 19:16:44",   //结束时间
     *                      "Biz_ID": 2,   //店铺ID
     *                      "Coupon_Subject": "奶粉类专用",  //标题
     *                      "Coupon_PhotoPath": "",  //优惠券图片
     *                      "Coupon_Description": ""   //信息描述
     *                   }
     *               ],
     *              "from": 1,
     *              "last_page": 1,  //上一页
     *              "next_page_url": null,  //下一页
     *              "path": "http://localhost:6002/api/center/my_coupon",
     *              "per_page": 15,   //每页数量
     *              "prev_page_url": null,
     *              "to": 1,
     *              "total": 1   //消息总数
     *              },
     *     }
     */
    public function my_coupon(Request $request)
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
        $ucr_obj = new User_Coupon_Record();

        $list = $ucr_obj->where('User_ID', $input['UserID'])
            ->where('Coupon_StartTime', '<', time())
            ->where('Coupon_EndTime', '>', time())
            ->orderByDesc('Record_CreateTime')
            ->paginate(20, ['*'], $cur_page);

        foreach($list as $key => $value){
            $value['Coupon_Subject'] = $value['coupon']['Coupon_Subject'];
            $value['Coupon_PhotoPath'] = $value['coupon']['Coupon_PhotoPath'];
            $value['Coupon_Description'] = $value['coupon']['Coupon_Description'];
            $value['Coupon_EndTime'] = date("Y-m-d H:i:s",$value['Coupon_EndTime']);
            $value['Coupon_StartTime'] = date("Y-m-d H:i:s",$value['Coupon_StartTime']);
            $value['Record_CreateTime'] = date("Y-m-d H:i:s",$value['Record_CreateTime']);
            unset($value['coupon']);
        }

        $data = ['status' => 1, 'msg' => '成功', 'data' => $list];
        return json_encode($data);

    }


    /**
     * @api {get} /center/coupon_list 优惠券列表
     * @apiGroup 优惠券
     * @apiDescription 获取优惠券列表
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
     *     curl -i http://localhost:6002/api/center/coupon_list
     *
     * @apiSampleRequest /api/center/coupon_list
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
     *                      "Coupon_ID": 1,   //优惠券ID
     *                      "Coupon_Keywords": null,
     *                      "Coupon_Title": null,
     *                      "Coupon_UserLevel": 0,
     *                      "Coupon_UsedTimes": 0,   //优惠券使用次数（-1:不限次数）
     *                      "Coupon_CreateTime": "1533035858",
     *                      "Coupon_UseArea": 0,  //优惠券使用范围
     *                      "Coupon_UseType": 0,   //优惠券类型(0:折扣，1:现金)
     *                      "Coupon_Condition": 0,   //一次购满多少可用
     *                      "Coupon_Discount": "0.00",  //优惠券折扣
     *                      "Coupon_Cash": 0,  //优惠券可抵现金
     *                      "Coupon_StartTime": "2018-07-31 19:16:44",  //开始时间
     *                      "Coupon_EndTime": "2018-08-07 19:16:44",   //结束时间
     *                      "Biz_ID": 2,   //店铺ID
     *                      "Coupon_Subject": "奶粉类专用",  //标题
     *                      "Coupon_PhotoPath": "",  //优惠券图片
     *                      "Coupon_Description": ""   //信息描述
     *                   }
     *               ],
     *              "from": 1,
     *              "last_page": 1,  //上一页
     *              "next_page_url": null,  //下一页
     *              "path": "http://localhost:6002/api/center/coupon_list",
     *              "per_page": 15,   //每页数量
     *              "prev_page_url": null,
     *              "to": 1,
     *              "total": 1   //消息总数
     *              },
     *     }
     */
    public function coupon_list(Request $request)
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
        $ucr_obj = new User_Coupon_Record();
        $uc_obj = new User_Coupon();

        $cids = $ucr_obj->select('Coupon_ID')->where('User_ID', $input['UserID'])->get();

        $list = $uc_obj->whereNotIn('Coupon_ID', $cids)
            ->where('Coupon_StartTime', '<', time())
            ->where('Coupon_EndTime', '>', time())
            ->where('Is_Delete', 0)
            ->orderByDesc('Coupon_CreateTime')
            ->paginate(20, ['*'], $cur_page);

        foreach($list as $key => $value){
            $value['Coupon_EndTime'] = date("Y-m-d H:i:s",$value['Coupon_EndTime']);
            $value['Coupon_StartTime'] = date("Y-m-d H:i:s",$value['Coupon_StartTime']);
        }

        $data = ['status' => 1, 'msg' => '成功', 'data' => $list];
        return json_encode($data);
    }


    /**
     * @api {get} /center/lose_coupon 失效、过期优惠券
     * @apiGroup 优惠券
     * @apiDescription 获取用户失效、过期优惠券列表
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
     *     curl -i http://localhost:6002/api/center/lose_coupon
     *
     * @apiSampleRequest /api/center/lose_coupon
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
     *                      "User_ID": 1, //用户ID
     *                      "Record_ID": 3,  //记录ID
     *                      "Coupon_ID": 1,   //优惠券ID
     *                      "Coupon_UsedTimes": 0,   //优惠券使用次数（-1:不限次数）
     *                      "Record_CreateTime": "2018-07-31 19:17:38",
     *                      "Coupon_UseArea": 0,  //优惠券使用范围
     *                      "Coupon_UseType": 0,   //优惠券类型(0:折扣，1:现金)
     *                      "Coupon_Condition": 0,   //一次购满多少可用
     *                      "Coupon_Discount": "0.00",  //优惠券折扣
     *                      "Coupon_Cash": 0,  //优惠券可抵现金
     *                      "Coupon_StartTime": "2018-07-31 19:16:44",  //开始时间
     *                      "Coupon_EndTime": "2018-08-07 19:16:44",   //结束时间
     *                      "Biz_ID": 2,   //店铺ID
     *                      "Coupon_Subject": "奶粉类专用",  //标题
     *                      "Coupon_PhotoPath": "",  //优惠券图片
     *                      "Coupon_Description": ""   //信息描述
     *                   }
     *               ],
     *              "from": 1,
     *              "last_page": 1,  //上一页
     *              "next_page_url": null,  //下一页
     *              "path": "http://localhost:6002/api/center/lose_coupon",
     *              "per_page": 15,   //每页数量
     *              "prev_page_url": null,
     *              "to": 1,
     *              "total": 1   //消息总数
     *              },
     *     }
     */
    public function lose_coupon(Request $request)
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
        $ucr_obj = new User_Coupon_Record();
        $uc_obj = new User_Coupon();

        $cids = $uc_obj->select('Coupon_ID')
            ->where('Coupon_EndTime', '<', time())
            ->where('Is_Delete', 0)
            ->get();

        $list = $ucr_obj->where('User_ID', $input['UserID'])
            ->whereIn('Coupon_ID', $cids)
            ->orderByDesc('Record_CreateTime')
            ->paginate(20, ['*'], $cur_page);

        foreach($list as $key => $value){
            $value['Coupon_Subject'] = $value['coupon']['Coupon_Subject'];
            $value['Coupon_PhotoPath'] = $value['coupon']['Coupon_PhotoPath'];
            $value['Coupon_Description'] = $value['coupon']['Coupon_Description'];
            $value['Coupon_EndTime'] = date("Y-m-d H:i:s",$value['Coupon_EndTime']);
            $value['Coupon_StartTime'] = date("Y-m-d H:i:s",$value['Coupon_StartTime']);
            $value['Record_CreateTime'] = date("Y-m-d H:i:s",$value['Record_CreateTime']);
            unset($value['coupon']);
        }

        $data = ['status' => 1, 'msg' => '成功', 'data' => $list];
        return json_encode($data);
    }


    /**
     * @api {post} /center/get_coupon 领取优惠券
     * @apiGroup 优惠券
     * @apiDescription 领取优惠券
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID      用户ID
     * @apiParam {Number}   CouponID    优惠券ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/get_coupon
     *
     * @apiSampleRequest /api/center/get_coupon
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
     *     }
     */
    public function get_coupon(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'CouponID' => 'required|exists:user_coupon,Coupon_ID',
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'CouponID.required' => '缺少必要的参数CouponID',
            'CouponID.exists' => '此优惠券不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $ucr_obj = new User_Coupon_Record();
        $uc_obj = new User_Coupon();

        $UserCoupon = $ucr_obj->where('User_ID', $input['UserID'])
            ->where('Coupon_ID', $input['CouponID'])
            ->first();

        if (!empty($UserCoupon)) {
            $Data = ['status' => 0, 'msg' => '您已经领取过该优惠券，请勿重复领取！'];
        } else {
            $rsCoupon = $uc_obj->find($input['CouponID']);
            if ($rsCoupon) {
                $cou_Data = array(
                    'Coupon_ID' => $rsCoupon['Coupon_ID'],
                    'Coupon_UsedTimes' => $rsCoupon['Coupon_UsedTimes'],
                    'Coupon_UseArea' => $rsCoupon['Coupon_UseArea'],
                    'Coupon_UseType' => $rsCoupon['Coupon_UseType'],
                    'Coupon_Condition' => $rsCoupon['Coupon_Condition'],
                    'Coupon_Discount' => $rsCoupon['Coupon_Discount'],
                    'Coupon_Cash' => $rsCoupon['Coupon_Cash'],
                    'Coupon_StartTime' => $rsCoupon['Coupon_StartTime'],
                    'Coupon_EndTime' => $rsCoupon['Coupon_EndTime'],
                    'Record_CreateTime' => time(),
                    'Users_ID' => USERSID,
                    'User_ID' => $input['UserID'],
                    'Biz_ID' => $rsCoupon['Biz_ID']
                );
                $Flag = $ucr_obj->create($cou_Data);

                if ($Flag) {
                    $Data = ['status' => 1, 'msg' => '操作成功！'];
                } else {
                    $Data = ['status' => 0, 'msg' => '操作失败！'];
                }
            } else {
                $Data = ['status' => 0, 'msg' => '请勿非法操作！'];
            }
        }

        return json_encode($Data);
    }


}
