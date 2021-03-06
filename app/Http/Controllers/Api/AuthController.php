<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dis_Config;
use App\Models\Member;
use App\Models\ShopConfig;
use App\Models\Sms;
use App\Models\UserIntegralRecord;
use App\Services\ServiceSMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @api {post} /login  用户登陆
     * @apiGroup 用户认证
     * @apiDescription 用户登陆
     *
     * @apiParam {String}   mobile          手机号
     * @apiParam {String}   [password]      密码（普通登陆时为必填）
     * @apiParam {String}   [code]          验证码（验证码登陆时为必填）
     * @apiParam {Boolean}  type            登陆类型（0:验证码登陆，1:普通登陆）
     * @apiParam {Number}   [OwnerID]       推荐人ID
     * @apiParam {String}   [history_url]   原浏览页url
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {String} token       用户认证TOKEN
     * @apiSuccess {String} url         原浏览页路径
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/login
     *
     * @apiSampleRequest /api/login
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "登陆失败",
     *          "url": ""
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "登陆成功",
     *          "data": {
     *              "token": "586dfb3fbb3d145e1707b21b6c2dbe35.MTU3MTEyNDc4Mg==.7908ef5dfffc38017a3260941272bcf5",
     *              "expire_time": "2019-10-15 15:33:02"
     *          },
     *          "url": "",
     *     }
     */
    public function login(Request $request)
    {
        $input = $request->input();
        $m_obj = new Member();

        $rules = [
            'mobile' => 'required|mobile',
            'password' => 'required_if:type,1',
            'code' => 'required_if:type,0|string|size:4',
            'type' => 'required|in:0,1',
            'history_url' => 'nullable|url',
            'OwnerID' => 'nullable|exists:user,User_ID,Is_Distribute,1',
        ];
        $message = [
            'mobile.required' => '手机号码不能为空',
            'mobile.mobile' => '手机号码格式错误',
            'password.required_if' => '登陆密码不能为空',
            'code.required_if' => '验证码不能为空',
            'code.size' => '验证码格式不正确',
            'type.required' => '登陆类型不是允许的值',
            'history_url.url' => '原浏览页路径格式错误',
            'OwnerID.exists' => '此推荐人不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $user = $m_obj->where('User_Mobile', $input['mobile'])->first();
        $remark_token = md5(time().$input['mobile']);
        $token_data = $this->create_token($remark_token);
        if(isset($input['history_url'])){
            $url = $input['history_url'];
        }else{
            $url = '';
        }

        //普通登陆
        if($input['type'] == 1){

            if(!$user){
                $data = ['status' => 0, 'msg' => '账户不存在，请先注册', 'url' => '/api/register'];
                return json_encode($data);
            }
            if(md5($input['password']) != $user['User_Password']){
                $data = ['status' => 0, 'msg' => '登陆密码不正确'];
                return json_encode($data);
            }

            if($user['User_Status'] == 1){
                $user->remark_token = $remark_token;
                $flag = $user->save();

                if($flag){
                    $data = ['status' => 1, 'msg' => '登陆成功', 'data' => $token_data, 'url' => $url];
                }else{
                    $data = ['status' => 0, 'msg' => '登陆失败'];
                }

            }else{
                $data = ['status' => 0, 'msg' => '帐号锁定，禁止登录'];
            }

            return json_encode($data);
        }

        //快捷登陆
        if($input['type'] == 0){
            //验证短信验证码
            $rst_json = $this->checkSMS($request);
            $rst = json_decode($rst_json, true);
            if($rst['status'] == 0){
                return json_encode($rst);
            }

            if(!$user){
                //用户不存在则注册新用户
                $user = $this->create_user($input);
            }

            if($user && $user['User_Status'] == 1){
                $user->remark_token = $remark_token;
                $flag = $user->save();

                if($flag){
                    $data = ['status' => 1, 'msg' => '登陆成功', 'data' => $token_data, 'url' => $url];
                }else{
                    $data = ['status' => 0, 'msg' => '登陆失败'];
                }

            }else{
                $data = ['status' => 0, 'msg' => '帐号锁定，禁止登录'];
            }

            return json_encode($data);
        }

    }



    /**
     * @api {post} /register 新用户注册
     * @apiGroup 用户认证
     * @apiDescription 新用户注册
     *
     * @apiParam {String} mobile        手机号
     * @apiParam {String} code          验证码
     * @apiParam {String} password      密码（字母数字下划线，6-16位）
     * @apiParam {String} password_confirmation      确认密码
     * @apiParam {Number} [OwnerID]     推荐人ID
     *
     * @apiSuccess {Number} status       1:成功，0:失败
     * @apiSuccess {String} msg          状态说明
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/register
     *
     * @apiSampleRequest /api/register
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "注册失败"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "注册成功",
     *          "data": {
     *              "token": "586dfb3fbb3d145e1707b21b6c2dbe35.MTU3MTEyNDc4Mg==.7908ef5dfffc38017a3260941272bcf5",
     *              "expire_time": "2019-10-15 15:33:02"
     *          },
     *     }
     */
    public function register(Request $request)
    {
        $input = $request->input();
        $m_obj = new Member();

        $rules = [
            'mobile' => 'required|mobile|unique:user,User_Mobile',
            'password' => 'required|confirmed|alpha_dash|min:6|max:16',
            'code' => 'required|string|size:4',
            'OwnerID' => 'nullable|exists:user,User_ID,Is_Distribute,1',
        ];
        $message = [
            'mobile.required' => '手机号码不能为空',
            'mobile.mobile' => '手机号码格式错误',
            'mobile.unique' => '该手机号码已注册，请直接登陆',
            'password.required' => '登陆密码不能为空',
            'password.confirmed' => '登陆密码与确认密码不一致',
            'password.min' => '登陆密码为6-16位字符串',
            'password.max' => '登陆密码为6-16位字符串',
            'code.required' => '验证码不能为空',
            'code.size' => '验证码格式不正确',
            'OwnerID.exists' => '此推荐人不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        //验证短信验证码
        $rst_json = $this->checkSMS($request);
        $rst = json_decode($rst_json, true);
        if($rst['status'] == 0){
            return json_encode($rst);
        }

        $remark_token = md5(time().$input['mobile']);
        $token_data = $this->create_token($remark_token);

        $user = $this->create_user($input);
        if($user){
            //新用户登陆
            $user_info = $m_obj->find($user['User_ID']);
            $user_info->remark_token = $remark_token;
            $flag = $user_info->save();

            if($flag){
                $data = ['status' => 1, 'msg' => '注册成功', 'data' => $token_data];
            }else{
                $data = ['status' => 0, 'msg' => '登陆失败'];
            }
        }else{
            $data = ['status' => 0, 'msg' => '注册失败'];
        }

        return json_encode($data);

    }



    /**
     * @api {get} /logout 退出登陆
     * @apiGroup 用户认证
     * @apiDescription 退出登陆
     *
     * @apiParam {Number} UserID         用户ID
     *
     * @apiSuccess {Number} status       1:成功，0:失败
     * @apiSuccess {String} msg          状态说明
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/logout
     *
     * @apiSampleRequest /api/logout
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "退出登陆失败"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "退出登陆成功"
     *     }
     */
    public function logout(Request $request)
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

        $m_obj = new Member();
        $flag = $m_obj->where('User_ID', $input['UserID'])->update(['remark_token' => '']);

        if($flag){
            $data = ['status' => 1, 'msg' => '退出登陆成功'];
        }else{
            $data = ['status' => 0, 'msg' => '退出登陆失败'];
        }
        return json_encode($data);

    }


    /**
     * @api {get} /send_sms  发送验证码
     * @apiGroup 用户认证
     * @apiDescription 发送验证码
     *
     * @apiParam {String} mobile          手机号
     *
     * @apiSuccess {Number} status       1:成功，0:失败
     * @apiSuccess {String} msg          状态说明
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/send_sms
     *
     * @apiSampleRequest /api/send_sms
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "发送失败"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "发送成功"
     *     }
     */
    public function sendSMS(Request $request)
    {
        $obj = new ServiceSMS();
        $input = $request->input();

        $rules = [
            'mobile' => 'required|mobile'
        ];
        $message = [
            'mobile.required' => '手机号不能为空',
            'mobile.mobile' => '手机号格式错误'
        ];
        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $mobile = $input['mobile'];
        $code = sprintf("%'.04d", rand(0, 9999));
        $message = "您的手机验证码为：" . $code . "，60秒内有效，过期请重新获取。";

        $rst = $obj->send_sms($mobile, $message);
        if ($rst) {
            session(['login_sms_code' => $code]);
            $data = ['status' => 1, 'msg' => '发送成功'];
        } else {
            $data = ['status' => 0, 'msg' => '发送失败'];
        }

        return json_encode($data);
    }


    /**
     * @api {post} /forget_pwd  忘记密码
     * @apiGroup 用户认证
     * @apiDescription 忘记密码
     *
     * @apiParam {String}   mobile          手机号
     * @apiParam {String}   code            短信验证码
     * @apiParam {String}   User_Password        登陆密码(包括字母数字下划线)
     * @apiParam {String}   User_Password_confirmation       确认登陆密码
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/forget_pwd
     *
     * @apiSampleRequest /api/forget_pwd
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "密码重置失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "密码重置成功",
     *     }
     */
    public function forget_pwd(Request $request)
    {
        $input = $request->input();

        $rules = [
            'mobile' => "required|exists:user,User_Mobile",
            'code' => 'required|string|size:4',
            'User_Password' => 'required|alpha_dash|min:6|max:16|confirmed',
        ];
        $validator = Validator::make($input, $rules);
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
            $password = md5($input['User_Password']);
            $user->where('User_Mobile', $input['mobile'])->update(['User_Password' => $password]);

            $data = ['status' => 1, 'msg' => '密码重置成功'];
            return json_encode($data);
        }else{
            return json_encode($rst);
        }
    }


    /**
     * @api {get} /check_sms 验证短信验证码
     * @apiGroup 用户认证
     * @apiDescription 验证短信验证码
     *
     * @apiParam {String} mobile        手机号
     * @apiParam {String} code          验证码
     *
     * @apiSuccess {Number} status       1:成功，0:失败
     * @apiSuccess {String} msg          状态说明
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/check_sms
     *
     * @apiSampleRequest /api/check_sms
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "验证失败"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "验证成功"
     *     }
     */
    public function checkSMS(Request $request)
    {
        $s_obj = new Sms();
        $input = $request->input();

        $rules = [
            'mobile' => 'required|mobile',
            'code' => 'required|string|size:4'
        ];
        $message = [
            'mobile.required' => '手机号不能为空',
            'mobile.mobile' => '手机号格式错误',
            'code.required' => '验证码不能为空',
            'code.size' => '验证码格式错误',
        ];
        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $rsCode = $s_obj->where('mobile', $input['mobile'])
            ->orderBy('itemid', 'desc')
            ->first();

        $times = time();

        if($times - $rsCode['sendtime'] > 60 ){
            $data = ['status' => 0, 'msg' => '验证码已过期'];
        }else{
            if ($input['code'] == session('login_sms_code')) {
                $request->session()->forget('login_sms_code');
                $data = ['status' => 1, 'msg' => '验证成功'];
            } else {
                $data = ['status' => 0, 'msg' => '验证失败'];
            }
        }

        return json_encode($data);

    }


    //创建新用户
    private function create_user($input)
    {
        $dc_obj = new Dis_Config();
        $m_obj = new Member();
        $sc_obj = new ShopConfig();

        //用户不存在则注册新用户
        $user_data = [
            'Users_ID' => USERSID,
            'User_Mobile' => $input['mobile'],
            'User_Profile' => 1,	//手机认证过
            'User_CreateTime' => time(),
            'User_Status' => 1,
            'User_From' => 1,
            'User_No' => '2',
            "User_PayPassword" => md5('123456'),
            "User_Password" => md5('123456'),
        ];

        //推荐人
        $dis_config = $dc_obj->select('Distribute_Limit')->find(1);
        if($dis_config->Distribute_Limit == 1){
            if(!isset($input['OwnerID']) || (isset($input['OwnerID']) && $input['OwnerID'] == 0)){
                $data = ['status' => 0, 'msg' => '必须通过推荐人成为会员'];
                return json_encode($data);
            }
        }

        if(isset($input['OwnerID']) && $input['OwnerID'] > 0){
            $shop_config = $sc_obj->find(USERSID);

            $rsroot = $m_obj->select('Root_ID', 'Owner_Id')->find($input['OwnerID']);
            $root_id = $rsroot['Root_ID'] == 0 ? $input['OwnerID'] : $rsroot['Root_ID'];

            $user_data["Owner_Id"] = $input['OwnerID'];
            $user_data["Root_ID"] = $root_id;
            $user_data['User_Integral'] = $shop_config['Popularize_Integral'];
            $user_data['User_TotalIntegral'] = $shop_config['Popularize_Integral'];
            unset($rsroot);
        }

        $user = $m_obj->create($user_data);

        $OpenID = md5(session_id() . $user['User_ID']);
        $userno = 600000 + $user['User_ID'];
        $update_arr = [
            'User_OpenID' => $OpenID,
            'User_No'=>$userno
        ];
        $m_obj->where('User_ID', $user['User_ID'])->update($update_arr);

        if(isset($input['OwnerID']) && $input['OwnerID'] > 0){
            //推荐人得积分
            $rsOwner = $m_obj->select('User_ID', 'User_Integral', 'User_TotalIntegral')->find(intval($input['OwnerID']));
            $integral = $shop_config['Popularize_Integral'] + $rsOwner['User_Integral'];
            $rsOwner->User_Integral = $integral;
            $rsOwner->User_TotalIntegral = $shop_config['Popularize_Integral'] + $rsOwner['User_TotalIntegral'];
            $rsOwner->save();

            $uir_obj = new UserIntegralRecord();

            //生成推荐人积分记录
            $integral_record_data1 = array(
                'Record_Integral' => $shop_config['Popularize_Integral'],
                'Record_SurplusIntegral' => $integral,
                'Operator_UserName' => '',
                'Record_Type' => 7,
                'Record_Description' => '推广下线得积分',
                'Record_CreateTime' => time(),
                'Users_ID' => USERSID,
                'User_ID' => intval($input['OwnerID'])
            );
            $uir_obj->create($integral_record_data1);

            //生成被推荐人积分记录
            $integral_record_data2 = array(
                'Record_Integral' => $shop_config['Popularize_Integral'],
                'Record_SurplusIntegral' => $shop_config['Popularize_Integral'],
                'Operator_UserName' => '',
                'Record_Type' => 7,
                'Record_Description' => '注册得积分',
                'Record_CreateTime' => time(),
                'Users_ID' => USERSID,
                'User_ID' => $user['User_ID']
            );
            $uir_obj->create($integral_record_data2);
        }

        return $user;
    }


    //生成token
    private function create_token($remark_token)
    {
        $expire_time = time()+60*60*24*365;
        $token = $remark_token.'.'.base64_encode($expire_time).'.'.md5(USERSID);

        $data = [
            'token' => $token,
            'expire_time' => date('Y-m-d H:i:s', $expire_time),
        ];

        return $data;
    }


}
