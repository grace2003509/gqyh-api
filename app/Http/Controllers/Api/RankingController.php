<?php

namespace App\Http\Controllers\Api;

use App\Models\Dis_Account;
use App\Models\Dis_Account_Record;
use App\Models\Dis_Config;
use App\Models\ShopConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RankingController extends Controller
{
    /**
     * @api {get} /distribute/home_income_list  总部分销商排行
     * @apiGroup 财富排行榜
     * @apiDescription 获取总部分销商佣金排行列表
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/distribute/home_income_list
     *
     * @apiSampleRequest /api/distribute/home_income_list
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "error",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "成功",
     *          "data": [
     *              {
     *                  "Account_ID": 14,   //分销账户ID
     *                  "User_ID": 1,   //用户ID
     *                  "Total_Income": "13845.70",   //累计佣金
     *                  "Professional_Title": 3,  //分销商爵位级别
     *                  "protitle_name": "高级经理",   //爵位名称
     *                  "user_headimg": "localhost:6002/uploadfiles/userfile/1/image/5b5fcec74a.jpg",   //头像
     *                  "user_mobile": "15501691825",   //手机号
     *              },
     *          ],
     *     }
     */
    public function home_income_list(Request $request)
    {
        $input = $request->input();

        $da_obj = new Dis_Account();
        $dc_obj = new Dis_Config();
        $dar_obj = new Dis_Account_Record();
        $sc_obj = new ShopConfig();

        $sConfig = $sc_obj->select('ShopLogo')->find(USERSID);
        $rsConfig = $dc_obj->select('H_Incomelist_Limit')->find(1);

        //获取总店排行榜
        $filter = ['User_ID','Account_ID','Total_Income','Professional_Title'];
        $list = $da_obj->select($filter)
            ->where('Total_Income', '>=', $rsConfig['H_Incomelist_Limit'])
            ->orderBy('Total_Income','desc')
            ->limit(10)
            ->get();

        $dis_title_level = $dc_obj->get_dis_pro_rate_title();

        foreach($list as $key=>$item) {
            $Total_Income = $dar_obj->get_my_leiji_income($item['User_ID']);
            if($Total_Income != $item['Total_Income']){
                $da_obj->where('User_ID', $item['User_ID'])->update(['Total_Income' => $Total_Income]);
            }
            $item['protitle_name'] = $item['Professional_Title'] > 0 ? $dis_title_level[$item['Professional_Title']]['Name'] : '无';
            if($item['user']['User_HeadImg'] != ''){
                $item['user_headimg'] = $_SERVER['HTTP_HOST'] . $item['user']['User_HeadImg'];
            }else{
                $item['user_headimg'] = ADMIN_BASE_HOST.$sConfig['ShopLogo'];
            }
            $item['user_mobile'] = $item['user']['User_Mobile'];
            unset($item['user']);
        }

        if($rsConfig["HIncomelist_Open"] == 1){
            $in_list = true;;
        }else{
            $in_list = $list->contains('User_ID',$input['UserID']);
        }

        if(!$in_list){
            $msg = '无权查看，需入榜后才能查看。';
            return json_encode(['status' => 2, 'msg' => $msg, 'data' => $list]);
        }else{
            return json_encode(['status' => 1, 'msg' => 'success', 'data' => $list]);
        }
    }



    /**
     * @api {get} /distribute/posterity_income_list  下级分销商排行
     * @apiGroup 财富排行榜
     * @apiDescription 获取下级分销商佣金排行列表
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/distribute/posterity_income_list
     *
     * @apiSampleRequest /api/distribute/posterity_income_list
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
     *                  "Account_ID": 14,   //分销账户ID
     *                  "User_ID": 1,   //用户ID
     *                  "Total_Income": "13845.70",   //累计佣金
     *                  "Professional_Title": 3,  //分销商爵位级别
     *                  "protitle_name": "高级经理",   //爵位名称
     *                  "user_headimg": "localhost:6002/uploadfiles/userfile/1/image/5b5fcec74a.jpg",   //头像
     *                  "user_mobile": "15501691825",   //手机号
     *              },
     *          ],
     *     }
     */
    public function posterity_income_list(Request $request)
    {
        $input = $request->input();

        $da_obj = new Dis_Account();
        $dc_obj = new Dis_Config();
        $dar_obj = new Dis_Account_Record();
        $sc_obj = new ShopConfig();

        $sConfig = $sc_obj->select('ShopLogo')->find(USERSID);
        $rsConfig = $dc_obj->select('H_Incomelist_Limit')->find(1);

        //获取此用户下属排行榜
        $account = $da_obj->where('User_ID', $input['UserID'])->first();
        $posterity = $account->getPosterity($rsConfig['Dis_Level']);
        $posterity_ids = get_dropdown_list($posterity, 'Account_ID');
        $filter = ['User_ID','Account_ID','Total_Income','Professional_Title'];
        $list = $da_obj->select($filter)
            ->whereIn('Account_ID', array_keys($posterity_ids))
            ->orderBy('Total_Income','desc')
            ->limit(100)
            ->get();

        $dis_title_level = $dc_obj->get_dis_pro_rate_title();

        foreach($list as $key=>$item) {
            $Total_Income = $dar_obj->get_my_leiji_income($item['User_ID']);
            if($Total_Income != $item['Total_Income']){
                $da_obj->where('User_ID', $item['User_ID'])->update(['Total_Income' => $Total_Income]);
            }
            $item['protitle_name'] = $item['Professional_Title'] > 0 ? $dis_title_level[$item['Professional_Title']]['Name'] : '无';
            if($item['user']['User_HeadImg'] != ''){
                $item['user_headimg'] = $_SERVER['HTTP_HOST'] . $item['user']['User_HeadImg'];
            }else{
                $item['user_headimg'] = ADMIN_BASE_HOST.$sConfig['ShopLogo'];
            }
            $item['user_mobile'] = $item['user']['User_Mobile'];
            unset($item['user']);
        }

        return json_encode(['status' => 1, 'msg' => 'success', 'data' => $list]);

    }
}
