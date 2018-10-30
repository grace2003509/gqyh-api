<?php

namespace App\Http\Controllers\Api;

use App\Models\User_Favourite_Products;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FavouriteController extends Controller
{
    /**
     * @api {get} /center/favourite_list 商品收藏列表
     * @apiGroup 收藏夹
     * @apiDescription 获取用户商品收藏列表
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   cur_page=1      当前页
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/favourite_list
     *
     * @apiSampleRequest /api/center/favourite_list
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "缺少必要的参数UserID",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "成功",
     *          "data": {
     *              "current_page": 1,  //当前页
     *              "data": [
     *                  {
     *                      "FAVOURITE_ID": 1,   //收藏ID
     *                      "Products_ID": 28,  //商品ID
     *                      "Products_Name": "孕妈妈连衣裙",   //商品名称
     *                      "Products_PriceX": "80.00",   //现价
     *                      "Products_Img": "http://localhost:6001/uploadfiles/biz/2/image/5b35be0189.jpg",   //商品图片
     *                      "User_ID": 18,   //用户ID
     *                   }
     *              ],
     *              "from": 1,
     *              "last_page": 1,  //上一页
     *              "next_page_url": null,  //下一页
     *              "path": "http://localhost:6002/api/center/favourite_list",
     *              "per_page": 15,   //每页数量
     *              "prev_page_url": null,
     *              "to": 1,
     *              "total": 1   //总数
     *           },
     *     }
     */
    public function index(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'cur_page' => 'required|integer|min:1',
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $ufp_obj = new User_Favourite_Products();

        $list = $ufp_obj->where('User_ID', $input['UserID'])
            ->orderByDesc('FAVOURITE_ID')
            ->paginate(20, ['*'], $input['cur_page']);

        foreach($list as $key => $value){
            $value['Products_Name'] = $value['product']['Products_Name'];
            $value['Products_PriceX'] = $value['product']['Products_PriceX'];
            $JSON = json_decode($value['product']['Products_JSON'],TRUE);
            $value['Products_Img'] = ADMIN_BASE_HOST.$JSON["ImgPath"][0];
            unset($value['product']);
        }

        $data = ['status' => 1, 'msg' => '成功', 'data' => $list];

        return json_encode($data);

    }


    /**
     * @api {post} /center/favourite_del 取消收藏
     * @apiGroup 收藏夹
     * @apiDescription 取消用户收藏的商品
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   FavouriteID     收藏ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/favourite_del
     *
     * @apiSampleRequest /api/center/favourite_del
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "缺少必要的参数UserID",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "成功",
     *     }
     */
    public function del(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'FavouriteID' => "required|exists:user_favourite_products,FAVOURITE_ID,User_ID,{$input['UserID']}",
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'FavouriteID.required' => '缺少必要的参数',
            'FavouriteID.exists' => '此收藏不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $ufp_obj = new User_Favourite_Products();

        $flag = $ufp_obj->destroy($input['FavouriteID']);

        if($flag){
            $data = ['status' => 1, 'msg' => '成功'];
        }else{
            $data = ['status' => 0, 'msg' => '失败'];
        }

        return json_encode($data);
    }
}
