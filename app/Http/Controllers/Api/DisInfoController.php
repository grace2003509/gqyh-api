<?php

namespace App\Http\Controllers\Api;

use App\Models\Dis_Account;
use App\Models\Dis_Account_Record;
use App\Models\Dis_Config;
use App\Models\Dis_Level;
use App\Models\Member;
use App\Models\PermissionConfig;
use App\Models\ShopConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DisInfoController extends Controller
{
    /**
     * @api {get} /distribute/account_info  分销商账户信息
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
     *     curl -i http://localhost:6002/api/distribute/account_info
     *
     * @apiSampleRequest /api/distribute/account_info
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
     *                  "Level_ID": 1,  //分销商等级ID(1:普通会员，2:vip会员，3:全国总代)
     *                  "Total_Income": 1314.00,  //累计佣金
     *                  "Balance_Income": 430.00,  //可提现佣金
     *                  "Cost_Money": "1314.00",  //个人消费金额
     *              },
     *     }
     */
    public function account_info(Request $request)
    {
        $input = $request->input();

        $da_obj = new Dis_Account();
        $account = $da_obj->where('User_ID', $input['UserID'])->first();

        $sc_obj = new ShopConfig();
        $dl_obj = new Dis_Level();
        $dar_obj = new Dis_Account_Record();

        $rsConfig = $sc_obj->select('ShopLogo')->find(USERSID);
        $dis_level = $dl_obj->select('Level_ID', 'Level_Name')->get();
        $dis_level = get_dropdown_list($dis_level,'Level_ID', 'Level_Name');
        $headimg = empty($account['user']['User_HeadImg']) ? ADMIN_BASE_HOST.$rsConfig['ShopLogo'] : $_SERVER['HTTP_HOST'].$account['user']['User_HeadImg'];

        $info = [
            'User_ID' => $account['User_ID'],
            'Account_ID' => $account['Account_ID'],
            'Head_Img' => $headimg,
            'Account_Mobile' => $account['user']['User_Mobile'],
            'Account_Name' => $account['user']['User_NickName'],
            'Account_Level' => $dis_level[$account['Level_ID']],
            'Level_ID' => $account['Level_ID'],
            'Total_Income' => $dar_obj->get_my_leiji_income($input['UserID']),  //总计佣金
            'Balance_Income' => round_pad_zero($account['balance'], 2),  //可提现佣金
            'Cost_Money' => $account['user']['User_Cost'],
        ];

        $data = ['status' => 1, 'msg' => '成功', 'data' => $info];

        return json_encode($data);
    }


    /**
     * @api {get} /distribute/pop_info  分销推广设置信息
     * @apiGroup 分销中心
     * @apiDescription 获取分销推广设置信息
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
     *     curl -i http://localhost:6002/api/distribute/pop_info
     *
     * @apiSampleRequest /api/distribute/pop_info
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
     *                  "pop_url": "http://localhost:6002/api/login?OwnerID=18",  //推广链接
     *                  "pop_code_bg": "http://localhost:6001/uploadfiles/9nj50igwex/image/5ba3194682.jpg",  //推广二维码背景图
     *                  "shop_name": "观前一号",  //商城名称
     *                  "user_mobile": "132****7043",  //用户手机号
     *                  "is_need": "1",  //是否需要生成海报（1:是，2:否）
     *              },
     *     }
     */
    public function pop_info(Request $request)
    {
        $input = $request->input();

        $txtinfo = "http://" . $_SERVER['HTTP_HOST'] . "/api/login?OwnerID={$input['UserID']}";
        $dc_obj = new Dis_Config();
        $dis_config = $dc_obj->select('QrcodeBg')->find(1);
        $sc_obj = new ShopConfig();
        $shop_config = $sc_obj->select('ShopName')->find(USERSID);
        $m_obj = new Member();
        $user = $m_obj->select('User_Mobile')->find($input['UserID']);
        $da_obj = new Dis_Account();
        $rsAccount = $da_obj->select('Is_Regeposter')->where('User_ID', $input['UserID'])->first();
        $poster_path = $_SERVER["DOCUMENT_ROOT"].'/data/poster/'.USERSID.$input['UserID'].'pop.png';

        $info = [
            'pop_url' => $txtinfo,
            'pop_code_bg' => ADMIN_BASE_HOST . $dis_config['QrcodeBg'],
            'shop_name' => $shop_config['ShopName'],
            'user_mobile' => substr($user['User_Mobile'],0,3)."****".substr($user['User_Mobile'],7,4),
            'is_need' => !is_file($poster_path)||$rsAccount['Is_Regeposter'] ? 1 : 0,
        ];

        $data = ['status' => 1, 'msg' => '成功', 'data' => $info];

        return json_encode($data);
    }


    /**
     * @api {get} /distribute/group_info  团队设置信息
     * @apiGroup 分销中心
     * @apiDescription 获取分销商关于团队的设置信息
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
     *     curl -i http://localhost:6002/api/distribute/group_info
     *
     * @apiSampleRequest /api/distribute/group_info
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
     *               "myterm": "我的团队",   //管理称呼
     *               "childlevelterm": {   //分销团队等级称呼
     *                      "1": "一级分销商",
     *                      "2": "二级分销商",
     *                      "3": "三级分销商"
     *                 },
     *               "catcommission": "佣金",  //佣金称呼
     *               "cattuijian": "我的推荐人"   //推荐人称呼
     *          },
     *     }
     */
    public function group_info()
    {
        $dc_obj = new Dis_Config();
        $dis_config = $dc_obj->select('Index_Professional_Json')->find(1);

        $info = json_decode($dis_config['Index_Professional_Json'], true);

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
    public function dis_menu()
    {
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
