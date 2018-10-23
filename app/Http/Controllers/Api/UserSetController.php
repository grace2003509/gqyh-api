<?php

namespace App\Http\Controllers\Api;

use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserSetController extends Controller
{
    /**
     * @api {post} /center/change_mobile  更改手机号
     * @apiGroup 完善会员资料
     * @apiDescription 更改手机号
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {String}   mobile          手机号
     * @apiParam {String}   code            短信验证码
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/change_mobile
     *
     * @apiSampleRequest /api/center/change_mobile
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "更改手机号成功",
     *     }
     */
    public function change_mobile(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'mobile' => "required|mobile|unique:user,User_Mobile",
            'code' => 'required|string|size:4',
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

        $auth = new AuthController();
        $user = new Member();
        //验证短信验证码
        $rst_json = $auth->checkSMS($request);
        $rst = json_decode($rst_json, true);
        if($rst['status'] == 1){
            $user->where('User_ID', $input['UserID'])->update(['User_Mobile' => $input['mobile']]);

            $data = ['status' => 1, 'msg' => '更改手机号成功'];
            return json_encode($data);
        }else{
            return json_encode($rst);
        }
    }



    /**
     * @api {post} /center/change_password  修改登陆密码
     * @apiGroup 完善会员资料
     * @apiDescription 修改登陆密码
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID            用户ID
     * @apiParam {String}   old_password      原密码
     * @apiParam {String}   password          新密码(6-16位，字母，数字或下划线)
     * @apiParam {String}   password_confirmation       确认密码
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/change_password
     *
     * @apiSampleRequest /api/center/change_password
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "登陆密码修改成功",
     *     }
     */
    public function change_password(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'old_password' => 'required',
            'password' => 'required|alpha_dash|min:6|max:16|confirmed',
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $m_obj = new Member();

        $user = $m_obj->find($input['UserID']);
        if($user['User_Password'] != md5($input['old_password'])){
            $data = ['status' => 0, 'msg' => '原始登陆密码错误'];
        }else{
            $user['User_Password'] = md5($input['password']);
            $user->save();

            $data = ['status' => 1, 'msg' => '登陆密码修改成功'];
        }

        return json_encode($data);
    }


    /**
     * @api {post} /center/change_pay_password  修改支付密码
     * @apiGroup 完善会员资料
     * @apiDescription 修改用户支付密码
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID            用户ID
     * @apiParam {String}   old_paypassword      原支付密码
     * @apiParam {String}   paypassword          新支付密码(6-16位，字母，数字或下划线)
     * @apiParam {String}   paypassword_confirmation       确认密码
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/change_pay_password
     *
     * @apiSampleRequest /api/center/change_pay_password
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "支付密码修改成功",
     *     }
     */
    public function change_pay_password(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'old_paypassword' => 'required',
            'paypassword' => 'required|alpha_dash|min:6|max:16|confirmed',
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $m_obj = new Member();

        $user = $m_obj->find($input['UserID']);
        if($user['User_PayPassword'] != md5($input['old_paypassword'])){
            $data = ['status' => 0, 'msg' => '原始支付密码错误'];
        }else{
            $user['User_PayPassword'] = md5($input['paypassword']);
            $user->save();

            $data = ['status' => 1, 'msg' => '支付密码修改成功'];
        }

        return json_encode($data);
    }



    /**
     * @api {post} /center/change_name  修改昵称
     * @apiGroup 完善会员资料
     * @apiDescription 修改用户昵称
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID            用户ID
     * @apiParam {String}   nick_name         昵称
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/change_name
     *
     * @apiSampleRequest /api/center/change_name
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "用户昵称修改成功",
     *     }
     */
    public function change_name(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'nick_name' => "required|string|max:50|unique:user,User_NickName,{$input['UserID']},User_ID",
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $m_obj = new Member();

        $user = $m_obj->find($input['UserID']);
        $user['User_NickName'] = trim($input['nick_name']);
        $user->save();

        $data = ['status' => 1, 'msg' => '用户昵称修改成功'];

        return json_encode($data);
    }
}
