<?php

namespace App\Http\Controllers\Api\Center;

use App\Models\User_Message;
use App\Models\User_Message_Record;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{

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


    /**
     * @api {get} /center/sys_message_read  读取系统消息
     * @apiGroup 用户中心
     * @apiDescription 读取系统消息
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   MessageID       消息ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        系统消息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/sys_message_read
     *
     * @apiSampleRequest /api/center/sys_message_read
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "成功读取系统消息",
     *          "data": {
     *              "current_page": 1,  //当前页
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
    public function sys_message_read(Request $request)
    {
        $input = $request->input();
        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'MessageID' => 'required|exists:user_message,Message_ID'
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'MessageID.required' => '缺少必要的参数MessageID',
            'MessageID.exists' => '此消息不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $umr_obj = new User_Message_Record();

        $msg = $umr_obj->where('User_ID', $input['UserID'])
            ->where('Message_ID', $input['MessageID'])
            ->first();
        if($msg){
            $data = ['status' => 1, 'msg' => '消息已读'];
        }else {
            $msg_data = [
                'Users_ID' => USERSID,
                'User_ID' => $input['UserID'],
                'Message_ID' => $input['MessageID'],
                'Record_CreateTime' => time(),
            ];
            $msg = $umr_obj->create($msg_data);
            $msg['is_read'] = 1;

            if($msg){
                $data = ['status' => 1, 'msg' => '读取消息成功', 'data' => $msg];
            }else{
                $data = ['status' => 0, 'msg' => '读取消息失败'];
            }
        }

        return json_encode($data);
    }
}
