<?php

namespace App\Http\Controllers\Api\Center;

use App\Models\Dis_Level;
use App\Models\Member;
use App\Models\ShopConfig;
use App\Models\User_Message;
use App\Models\User_Message_Record;
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



    /**
     * @api {get} /center/sys_message_num  系统消息数量
     * @apiGroup 用户中心
     * @apiDescription 系统消息总数量和未读数量
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID      用户ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        系统消息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/sys_message_num
     *
     * @apiSampleRequest /api/center/sys_message_num
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "获取系统消息数量成功",
     *          "data": {
     *                  "total": 17,  //用户的系统消息总数
     *                  "read": 15,  //用户已读消息数
     *                  "unread": 2,  //用户未读消息数
     *              },
     *     }
     */
    public function sys_message_num(Request $request)
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

        $um_obj = new User_Message();
        $umr_obj = new User_Message_Record();

        $total = $um_obj->where('User_ID', 0)
            ->orWhere('User_ID', $input['UserID'])
            ->count();

        $read = $umr_obj->where('User_ID', $input['UserID'])->count();

        $unread = $total - $read;

        $data = [
            'status' => 1,
            'msg' => '获取系统消息数量成功',
            'data' => ['total' => $total, 'read' => $read, 'unread' => $unread]
        ];

        return json_encode($data);
    }



    /**
     * @api {get} /center/sys_message_list  系统消息列表
     * @apiGroup 用户中心
     * @apiDescription 系统消息列表
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   [cur_page=1]    当前第几页
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        系统消息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/sys_message_list
     *
     * @apiSampleRequest /api/center/sys_message_list
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "获取系统消息列表成功",
     *          "data": {
     *              "current_page": 1,  //当前页
     *              "data": [
     *                  {
     *                      "Message_ID": 1,   //消息ID
     *                      "Message_Title": "欢迎关注观前一号商城",  //消息标题
     *                      "Message_Description": "欢迎关注观前一号商城",   //消息内容
     *                      "Message_CreateTime": "2018-07-17 14:41:40",   //消息发布时间
     *                      "User_ID": 0,   //用户ID,为0时表示此消息是群发消息
     *                      "is_read": 0   //消息是否已读（0:未读，1:已读）
     *                   }
     *              ],
     *              "from": 1,
     *              "last_page": 1,  //上一页
     *              "next_page_url": null,  //下一页
     *              "path": "http://localhost:6002/api/center/sys_message_list",
     *              "per_page": 15,   //每页数量
     *              "prev_page_url": null,
     *              "to": 1,
     *              "total": 1   //消息总数
     *           },
     *     }
     */
    public function sys_message_list(Request $request)
    {
        $input = $request->input();
        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'cur_page' => 'nullable|integer|min:1'
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

        $um_obj = new User_Message();
        $umr_obj = new User_Message_Record();
        $cur_page = isset($input['cur_page']) ? intval($input['cur_page']) : 1;

        $list = $um_obj->where('User_ID', $input['UserID'])
            ->orWhere('User_ID', 0)
            ->orderBy('Message_CreateTime', 'desc')
            ->paginate(15, ['*'], $cur_page);

        foreach($list as $key => $value){
            $is_read = $umr_obj->where('User_ID', $input['UserID'])
                ->where('Message_ID', $value['Message_ID'])
                ->first();
            if($is_read){
                $value['is_read'] = 1;
            }else{
                $value['is_read'] = 0;
            }
            $value['Message_CreateTime'] = date('Y-m-d H:i:s', $value['Message_CreateTime']);
        }

        $data = [
            'status' => 1,
            'msg' => '获取系统消息列表成功',
            'data' => $list
        ];

        return json_encode($data);

    }
}
