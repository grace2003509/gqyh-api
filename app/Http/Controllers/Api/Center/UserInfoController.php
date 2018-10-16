<?php

namespace App\Http\Controllers\Api\Center;

use App\Models\Dis_Level;
use App\Models\Member;
use App\Models\ShopConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserInfoController extends Controller
{
    /**
     * @api {get} /center/user_info  用户信息
     * @apiGroup 用户中心
     * @apiDescription 获取用户信息
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
     *     curl -i http://localhost:6002/api/center/user_info
     *
     * @apiSampleRequest /api/center/user_info
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
     *                  "User_ID": 17,  //用户ID
     *                  "User_No": 600017,  //用户编号
     *                  "User_Mobile": "13274507043",  //用户手机号
     *                  "User_NickName": null,  //用户昵称
     *                  "User_Level": 0,  //用户等级
     *                  "User_HeadImg": "http://localhost:6001//uploadfiles/9nj50igwex/image/5b87a19025.png",  //用户头像
     *                  "Is_Distribute": 0,  //是否是分销商（0:普通账户，1:分销账户）
     *                  "User_CreateTime": "2018-10-15 10:53:54",  //注册时间
     *              },
     *     }
     */
    public function user_info(Request $request)
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
        $sc_obj = new ShopConfig();
        $dl_obj = new Dis_Level();
        $dis_level = $dl_obj->select('Level_Name')->get();
        $dis_level = get_dropdown_list($dis_level, 'Level_ID', 'Level_Name');
        $shopconfig = $sc_obj->select('ShopLogo')->find(USERSID);

        $filter = ['User_ID', 'User_No', 'User_Mobile', 'User_NickName', 'User_Level',
            'User_HeadImg', 'Is_Distribute', 'User_CreateTime'];
        $user = $m_obj->select($filter)->find($input['UserID']);
        if($user->Is_Distribute == 1){
            $user->User_Level = $dis_level[@$user->disAccount->Level_ID];
        }else{
            $user->User_Level = '普通用户';
        }
        if($user->User_HeadImg == ''){
            $user->User_HeadImg = ADMIN_BASE_HOST.$shopconfig->ShopLogo;
        }
        $user->User_CreateTime = date('Y-m-d H:i:s', $user->User_CreateTime);

        $data = [
            'status' => 1,
            'msg' => '获取用户信息成功',
            'data' => $user,
        ];
        return json_encode($data);
    }

}
