<?php

namespace App\Http\Controllers\Api;

use App\Models\Dis_Account;
use App\Models\Dis_Account_Record;
use App\Models\Dis_Agent_Record;
use App\Models\Dis_Level;
use App\Models\PermissionConfig;
use App\Models\ShopConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DisInfoController extends Controller
{
    /**
     * @api {get} /distribute/dis_info  分销商信息
     * @apiGroup 分销中心
     * @apiDescription 获取分销商基本信息
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
     *     curl -i http://localhost:6002/api/distribute/dis_info
     *
     * @apiSampleRequest /api/distribute/dis_info
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
     *                  "User_ID": 18,  //用户ID
     *                  "Account_ID": 7,  //分销账号ID
     *                  "Head_Img": "http://localhost:6001/uploadfiles/9nj50igwex/image/5b87a19025.png",  //头像
     *                  "Account_Mobile": "13274507043",  //用户手机号
     *                  "Account_Name": "grace",  //账户昵称
     *                  "Account_Level": "普通会员",  //分销商等级
     *                  "Total_Income": 1314.00,  //累计佣金
     *                  "Balance_Income": 430.00,  //可提现佣金
     *              },
     *     }
     */
    public function dis_info(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:distribute_account,User_ID'
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $da_obj = new Dis_Account();
        $account = $da_obj->where('User_ID', $input['UserID'])->first();
        $sc_obj = new ShopConfig();
        $rsConfig = $sc_obj->select('ShopLogo')->find(USERSID);
        $dl_obj = new Dis_Level();
        $dis_level = $dl_obj->select('Level_ID', 'Level_Name')->get();
        $dis_level = get_dropdown_list($dis_level,'Level_ID', 'Level_Name');
        $dar_obj = new Dis_Account_Record();

        if($account->Is_Audit ==0 || $account->status == 0){
            $data = ['status' => 0, 'msg' => '您的分销账户暂不能用'];
            return json_encode($data);
        }

        $headimg = empty($account['user']['User_HeadImg']) ? ADMIN_BASE_HOST.$rsConfig['ShopLogo'] : $_SERVER['HTTP_HOST'].$account['user']['User_HeadImg'];
        $info = [
            'User_ID' => $account['User_ID'],
            'Account_ID' => $account['Account_ID'],
            'Head_Img' => $headimg,
            'Account_Mobile' => $account['user']['User_Mobile'],
            'Account_Name' => $account['user']['User_NickName'],
            'Account_Level' => $dis_level[$account['Level_ID']],
            'Total_Income' => $dar_obj->get_my_leiji_income($input['UserID']),  //总计佣金
            'Balance_Income' => round_pad_zero($account['balance'], 2),  //可提现佣金
        ];

        $data = ['status' => 1, 'msg' => '成功', 'data' => $info];

        return json_encode($data);
    }


    /**
     * @api {get} /distribute/dis_menu  分销中心菜单
     * @apiGroup 分销中心
     * @apiDescription 获取分销中心菜单
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
     *     curl -i http://localhost:6002/api/distribute/dis_menu
     *
     * @apiSampleRequest /api/distribute/dis_menu
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
     *                  "Perm_Name": "财富排行榜",   //菜单名称
     *                  "Perm_Picture": "http://localhost:6001/uploadfiles/9nj50igwex/image/5b4042127f.png",  //图标
     *                  "Perm_Url": "/api/shop/member/setting/"   //路径
     *              },
     *              {
     *                  "Perm_Name": "区域代理",
     *                  "Perm_Picture": "http://localhost:6001/uploadfiles/9nj50igwex/image/5b404231c2.png",
     *                  "Perm_Url": "/api/shop/member/backup/status/5/"
     *              },
     *          ]
     *     }
     */
    public function dis_menu(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:distribute_account,User_ID'
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $da_obj = new Dis_Account();
        $account = $da_obj->where('User_ID', $input['UserID'])->first();

        if($account->Is_Audit ==0 || $account->status == 0){
            $data = ['status' => 0, 'msg' => '您的分销账户暂不能用'];
            return json_encode($data);
        }

        $pc_obj = new PermissionConfig();
        $perm_config = $pc_obj->select('Perm_Name', 'Perm_Picture', 'Perm_Url')
            ->where('Perm_Tyle', 1)
            ->where('Is_Delete', 0)
            ->orderByDesc('Perm_Index')
            ->get();
        foreach($perm_config as $key => $value){
            $value['Perm_Picture'] = ADMIN_BASE_HOST.$value['Perm_Picture'];
        }

        $data = ['status' => 1,'msg' => '获取菜单列表成功', 'data' => $perm_config];
        return json_encode($data);
    }

}
