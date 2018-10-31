<?php

namespace App\Http\Controllers\Api;

use App\Models\Dis_Account;
use App\Models\Dis_Account_Message;
use App\Models\Dis_Config;
use App\Models\Dis_Level;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    /**
     * @api {get} /distribute/my_team_num  团队数量
     * @apiGroup 我的团队
     * @apiDescription 获取分销商三级团队数量
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
     *     curl -i http://localhost:6002/api/distribute/my_team_num
     *
     * @apiSampleRequest /api/distribute/my_team_num
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
     *              "list": {
     *                  "1": 2,   // 一级分销商数量
     *                  "2": 2,   // 二级分销商数量
     *                  "3": 1   // 三级分销商数量
     *                  },
     *              "is_top_level": 1,   //是否可以查看分销商明细（1:是，0:否）
     *              "team_num": 5   // 三级分销商总数
     *          },
     *     }
     */
    public function my_team_num(Request $request)
    {
        $input = $request->input();

        $da_obj = new Dis_Account();
        $dc_obj = new Dis_Config();

        $account = $da_obj->where('User_ID', $input['UserID'])->first();

        $rsConfig = $dc_obj->select('Dis_Level', 'Dis_Mobile_Level')->find(1);
        $level_config = $rsConfig['Dis_Level'];
        $posterity_list = $account->getLevelList($level_config);
        $posterity_list = array_slice($posterity_list,0,$rsConfig['Dis_Mobile_Level'],TRUE);
        $teamnum = 0;
        $list = [];
        foreach ($posterity_list as $key => $sub_list) {
            $teamnum += count($sub_list);
            $list[$key] = count($sub_list);
        }

        //分销商级别(用于判断只有总代级别才能查看团队明细)
        $is_top_level = $account['Level_ID'] == 3 ? 1 : 0;

        $info = [
            'list' => $list,
            'is_top_level' => $is_top_level,  //成为总代方可查看明细
            'team_num' => $teamnum,
        ];

        return json_encode(['status' => 1, 'msg' => '成功', 'data' => $info]);
    }


    /**
     * @api {get} /distribute/my_team_list  团队明细
     * @apiGroup 我的团队
     * @apiDescription 获取分销商三级团队明细
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID        用户ID
     * @apiParam {Number}   LevelID       第几级分销商(1:一级分销商，2:二级分销商，3:三级分销商)
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/distribute/my_team_list
     *
     * @apiSampleRequest /api/distribute/my_team_list
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
     *              "team_data": [    //当前级别分销商列表
     *                  {
     *                      "Account_ID": 3,   //分销账号ID
     *                      "User_ID": 3,   //用户ID
     *                      "dis_level_name": "全国总代",   //分销商等级
     *                      "childern_num": 1,   //分销商下级数量
     *                      "User_Mobile": "18896953657",   //手机号
     *                      "Account_CreateTime": "2018/07/31 10:45:17",   //日期
     *                      "mess_count": 0,   //未读聊天信息数量（只一级分销商显示）
     *                  },
     *                  {
     *                      "Account_ID": 5,   //分销账号ID
     *                      "User_ID": 6,   //用户ID
     *                      "dis_level_name": "VIP会员",   //分销商等级
     *                      "childern_num": 1,   //分销商下级数量
     *                      "User_Mobile": "13696960295",   //手机号
     *                      "Account_CreateTime": "2018/07/31 10:45:17",
     *                      "mess_count": 0,
     *                  }
     *              ],
     *              "team_num": 5   // 当前级别分销商数量
     *          },
     *     }
     */
    public function my_team_list(Request $request)
    {
        $input = $request->input();

        $rules = [
            'LevelID' => 'required|in:1,2,3',
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            return ['status' => 0, 'msg' => $validator->messages()->first()];
        }

        $da_obj = new Dis_Account();
        $dc_obj = new Dis_Config();
        $dl_obj = new Dis_Level();
        $dam_obj = new Dis_Account_Message();

        $account = $da_obj->where('User_ID', $input['UserID'])->first();

        $rsConfig = $dc_obj->select('Dis_Level', 'Dis_Mobile_Level')->find(1);
        $level_config = $rsConfig['Dis_Level'];
        $posterity_list = $account->getLevelList($level_config);
        $posterity_list = array_slice($posterity_list,0,$rsConfig['Dis_Mobile_Level'],TRUE);
        $dis_level = $dl_obj->select('Level_ID', 'Level_Name')->get();
        $dis_level = get_dropdown_list($dis_level, 'Level_ID', 'Level_Name');

        $list = [];
        foreach ($posterity_list as $key => $sub_list) {
            if($key == $input['LevelID']){
                $l = [];
                foreach($sub_list as $k => $v){
                    $l['Account_ID'] = $v['Account_ID'];
                    $l['User_ID'] = $v['User_ID'];
                    $l['dis_level_name'] = $dis_level[$v['Level_ID']];
                    $l['childern_num'] = $da_obj->where('invite_id', $v['User_ID'])->count();
                    $l['User_Mobile'] = $da_obj->find($v['User_ID'])['user']['User_Mobile'];
                    $l['Account_CreateTime'] = ldate($v['Account_CreateTime']);

                    if($input['LevelID'] == 1){
                        //查看当前登录用户的未读消息
                        $l['mess_count'] = $dam_obj->where('Receiver_User_ID', $input['UserID'])
                            ->where('Mess_Status', 0)
                            ->where('User_ID', $v['User_ID'])
                            ->count();
                    }

                    $list['team_data'][] = $l;
                }
                $list['team_num'] = count($sub_list);
            }
        }

        return json_encode(['status' => 1, 'msg' => '成功', 'data' => $list]);
    }


    /**
     * @api {get} /distribute/my_user_list  我的会员列表
     * @apiGroup 我的团队
     * @apiDescription 获取分销商全部下级会员信息
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
     *     curl -i http://localhost:6002/api/distribute/my_user_list
     *
     * @apiSampleRequest /api/distribute/my_user_list
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
     *                      "User_ID": 3,  //会员ID
     *                      "User_NickName": null,  //昵称
     *                      "User_Mobile": "18896953657",  //手机号
     *                      "User_HeadImg": null,  //头像
     *                      "User_CreateTime": "2018/07/31 10:42:31",
     *                      "mess_num": 0   //未读聊天消息数
     *                  },
     *                  {
     *                      "User_ID": 4,
     *                      "User_NickName": null,
     *                      "User_Mobile": "13913141113",
     *                      "User_HeadImg": null,
     *                      "User_CreateTime": "2018/07/31 10:47:44",
     *                      "mess_num": 0
     *                  },
     *              ],
     *              "from": 1,
     *              "last_page": 1,
     *              "next_page_url": null,   //下一页
     *              "path": "http://localhost:6002/api/distribute/my_user_list",   //路径
     *              "per_page": 20,   //每页数量
     *              "prev_page_url": null,  //上一页
     *              "to": 6,
     *              "total": 6   //数据总数
     *          },
     *     }
     */
    public function my_user_list(Request $request)
    {
        $input = $request->input();

        $rules = [
            'cur_page' => 'required|integer|min:1',
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            return ['status' => 0, 'msg' => $validator->messages()->first()];
        }

        $User_ID = $input['UserID'];
        $m_obj = new Member();
        $dam_obj = new Dis_Account_Message();

        $root_user = $m_obj->select('Root_ID')->find($User_ID);
        if($root_user['Root_ID'] == 0){
            $level = $m_obj->select('User_ID')->where('Root_ID', $User_ID)->count();
        }else{
            $level = $m_obj->select('User_ID')->where('Root_ID', $root_user['Root_ID'])->count();
        }
        $result = $user_id = [$User_ID];
        for ($i = 0; $i < $level; $i++) {
            $dataUserID = $this->getSonUserID($user_id);
            if (empty($dataUserID)) {
                break;
            } else {
                $result = array_merge($result, $dataUserID);
                $user_id = $dataUserID;
            }
        }

        $user_list = $m_obj->select('User_ID', 'User_NickName', 'User_Mobile', 'User_HeadImg', 'User_CreateTime')
            ->whereIn('Owner_Id', $result)->paginate(20, $input['cur_page']);

        foreach($user_list as $key => $value){
            $num = $dam_obj->where('Receiver_User_ID', $User_ID)
                ->where('User_ID', $value['User_ID'])
                ->where('Mess_Status', 0)
                ->count();
            $value['mess_num'] = $num;
            $value['User_CreateTime'] = ldate($value['User_CreateTime']);
        }

        return json_encode(['status' => 1, 'msg' => '成功', 'data' => $user_list]);

    }



    /**
     * 返回下级用户user_id
     */
    private function getSonUserID($invite_user_id, $is_distribute = 1)
    {
        $m_obj2 = new Member();
        if($is_distribute == 1){
            $m_obj2 = $m_obj2->where('Is_Distribute', 1);
        }
        $rows = $m_obj2->select('User_ID')->whereIn('Owner_Id', $invite_user_id)->get()->toArray();

        return $rows;
    }
}
