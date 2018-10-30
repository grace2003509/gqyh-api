<?php

namespace App\Http\Controllers\Api;

use App\Models\Dis_Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PopularizeController extends Controller
{
    /**
     * @api {post} /distribute/pop_code  生成推广二维码
     * @apiGroup 推广分享
     * @apiDescription 生成分销商推广二维码
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
     *     curl -i http://localhost:6002/api/distribute/pop_code
     *
     * @apiSampleRequest /api/distribute/pop_code
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
     *               "code_url": "localhost:6002/data/temp/test577dc8167bf4630abc2c887f6882168f.png"  //推广二维码
     *          },
     *     }
     */
    public function pop_code(Request $request)
    {
        $input = $request->input();

        $product_url = "http://" . $_SERVER['HTTP_HOST'] . "api/login?Owner={$input['UserID']}";
        $qrcode_path = generate_qrcode($product_url);
        $data = ['status' => 1, 'msg' => '成功', 'data' => ['code_url' => $_SERVER['HTTP_HOST'] . $qrcode_path]];

        return json_encode($data);
    }


    /**
     * @api {post} /distribute/pop_poster  推广海报
     * @apiGroup 推广分享
     * @apiDescription 保存或获取分销商推广海报
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {String}   [dataUrl]       图片路径（type为0时必填）
     * @apiParam {Boolean}  type            1:获取海报，0:保存海报
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/distribute/pop_poster
     *
     * @apiSampleRequest /api/distribute/pop_poster
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
     *               "poster_path": "localhost:6002/data/poster/test577dc8167bf4630abc2c887f6882168f.png"  //推广海报
     *          },
     *     }
     */
    public function pop_poster(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:distribute_account,User_ID,status,1',
            'dataUrl' => 'required_if:type,0|image',
            'type' => 'required|boolean'
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $da_obj = new Dis_Account();
        $owner_id = $input['UserID'];
        $web_path = $_SERVER['HTTP_HOST'] . '/data/poster/' . USERSID . $owner_id . '.png';

        if(is_file($web_path) && $input['type'] == 1){
            $data = array('status' => 1, 'msg' => '成功', 'data' => ['poster_path' => $web_path]);
        }else{
            if((isset($input['dataUrl']) && $input['dataUrl'] == '') || !isset($input['dataUrl'])){
                $data = array('status' => 0, 'msg' => '请上传海报图片');
            }else{
                $Flag = generate_postere($input['dataUrl'], USERSID, $owner_id);
                if($Flag){
                    $da_obj->where('User_ID', $owner_id)->update(['Is_Regeposter' => 0]);
                    $data = array('status' => 1, 'msg' => '成功', 'data' => ['poster_path' => $web_path]);
                }else{
                    $data = array('status' => 0, 'msg' => '失败');
                }
            }
        }

        return json_encode($data);
    }
}
