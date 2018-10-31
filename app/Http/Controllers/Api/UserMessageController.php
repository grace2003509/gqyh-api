<?php

namespace App\Http\Controllers\Api;

use App\Models\Dis_Account_Message;
use App\Models\ShopConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserMessageController extends Controller
{
    /**
     * @api {post} /distribute/send_mess  发送消息
     * @apiGroup 聊天室
     * @apiDescription 向自己的下级会员发送聊天消息
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID      用户ID
     * @apiParam {Number}   Receiver_UserID      接收用户ID
     * @apiParam {String}   Mess_Content         消息内容
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/distribute/send_mess
     *
     * @apiSampleRequest /api/distribute/send_mess
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
     *     }
     */
    public function send_mess(Request $request)
    {
        $input = $request->input();

        $rules = [
            'Receiver_UserID' => "required|exists:user,User_ID,User_ID,!{$input['UserID']}",
            'Mess_Content' => 'required|string'
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            return ['status' => 0, 'msg' => $validator->messages()->first()];
        }

        $dam_obj = new Dis_Account_Message();

        $currentTime = time();
        $vdata['Mess_Content'] = $input['Mess_Content'];
        $vdata['Mess_Status'] = 0;
        $vdata['UsersID'] = USERSID;
        $vdata['Mess_CreateTime'] = $currentTime;
        $vdata['Receiver_User_ID'] = intval($input['Receiver_UserID']);
        $vdata['User_ID'] = intval($input['UserID']);

        $flag = $dam_obj->create($vdata);
        if ($flag) {
            return json_encode(array('status' => 1, 'msg' => '消息发送成功'));
        }else {
            return json_encode(array('status' => 0, 'msg' => '消息发送失败'));
        }
    }


    /**
     * @api {get} /distribute/mess_list  聊天消息列表
     * @apiGroup 聊天室
     * @apiDescription 获取聊天消息列表
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID               用户ID
     * @apiParam {Number}   Receiver_UserID      接收用户ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/distribute/mess_list
     *
     * @apiSampleRequest /api/distribute/mess_list
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
     *                  "Mes_ID": 3,
     *                  "User_ID": 1,   //发送方账号ID
     *                  "Receiver_User_ID": 18,  //接收方ID
     *                  "Mess_Content": "sgegsegt",    //消息内容
     *                  "Mess_Status": 0,   //消息状态（0:未读，1:已读）
     *                  "Mess_CreateTime": 1540966134,   //发送时间
     *                  "send_user": {    //发送消息用户基本信息
     *                      "User_Mobile": "15501691825",
     *                      "User_NickName": "1",
     *                      "User_HeadImg": "/uploadfiles/userfile/1/image/5b5fcec74a.jpg"
     *                  }
     *                  "position": "r"    //r:表示为登陆账号发送的信息，在右侧显示；l:表示为对话者发送的信息，在左侧显示
     *              },
     *          ]
     *     }
     */
    public function mess_list(Request $request)
    {
        $input = $request->input();

        $rules = [
            'Receiver_UserID' => "required|exists:user,User_ID,User_ID,!{$input['UserID']}",
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            return ['status' => 0, 'msg' => $validator->messages()->first()];
        }

        $dam_obj = new Dis_Account_Message();
        $sc_obj = new ShopConfig();

        $list = $dam_obj->whereRaw("User_ID = {$input['UserID']} and Receiver_User_ID = {$input['Receiver_UserID']}")
            ->orWhereRaw("User_ID = {$input['Receiver_UserID']} and Receiver_User_ID = {$input['UserID']}")
            ->orderBy('Mess_CreateTime')
            ->get();

        foreach($list as $key => $value){
            $value['send_user'] = $value->send_user()->select('User_Mobile','User_NickName', 'User_HeadImg')->first();
            if($value['User_ID'] == $input['UserID']){
                $value['position'] = 'r';
            }else{
                $value['position'] = 'l';
            }
            if(!$value['send_user']['User_HeadImg']){
                $value['send_user']['User_HeadImg'] = $sc_obj->select('ShopLogo')->find(USERSID)['ShopLogo'];
            }
            $value['Mess_CreateTime'] = ldate($value['Mess_CreateTime']);
        }

        return json_encode(['status' => 1, 'msg' => 'success', 'data' => $list]);

    }


    /**
     * @api {post} /distribute/read_mess  修改消息状态
     * @apiGroup 聊天室
     * @apiDescription 将消息列表中未读消息的状态改为已读
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID              用户ID
     * @apiParam {Number}   Receiver_UserID     对话用户ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/distribute/read_mess
     *
     * @apiSampleRequest /api/distribute/read_mess
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
     *     }
     */
    public function read_mess(Request $request)
    {
        $input = $request->input();

        $rules = [
            'Receiver_UserID' => "required|exists:user,User_ID,User_ID,!{$input['UserID']}",
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            return ['status' => 0, 'msg' => $validator->messages()->first()];
        }

        $dam_obj = new Dis_Account_Message();

        $dam_obj->where('Receiver_User_ID', $input['UserID'])
            ->where('User_ID', $input['Receiver_UserID'])
            ->where('Mess_Status', 0)
            ->update(['Mess_Status' => 1]);

        return json_encode(array('status' => 1, 'msg' => '消息读取成功'));

    }
}
