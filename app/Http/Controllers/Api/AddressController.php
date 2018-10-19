<?php

namespace App\Http\Controllers\Api;

use App\Models\Area;
use App\Models\User_Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * @api {get} /center/address_list 地址列表
     * @apiGroup 地址管理
     * @apiDescription 获取用户收货地址列表
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
     *     curl -i http://localhost:6002/api/center/address_list
     *
     * @apiSampleRequest /api/center/address_list
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
     *          "data": [
     *              {
     *                  "User_ID": "1",  //用户ID
     *                  "Address_ID": 1,  //地址ID
     *                  "Address_Name": "1",  //收货人名称
     *                  "Address_Mobile": "15501691825",   //收货人电话
     *                  "Address_Detailed": "1",  //详细地址
     *                  "Address_Is_Default": 1,   //是否是默认地址（1:是，0:否）
     *                  "Address_TrueName": null,   //收货人真实姓名（进口奶粉需要）
     *                  "Address_Certificate": null,  //收货人真实身份证号（进品奶粉需要）
     *                  "Province": "江苏",  //省
     *                  "City": "苏州市",   //市
     *                  "Area": "吴中区"   //区
     *              }
     *          ]
     *     }
     */
    public function index(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
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

        $ua_obj = new User_Address();
        $a_obj = new Area();

        $list = $ua_obj->where('User_ID', $input['UserID'])->get();
        foreach($list as $key => $value){
            $value['Province'] = $a_obj->find($value['Address_Province'])['area_name'];
            $value['City'] = $a_obj->find($value['Address_City'])['area_name'];
            $value['Area'] = $a_obj->find($value['Address_Area'])['area_name'];;
        }

        $data = ['status' => 1, 'msg' => '成功', 'data' => $list];
        return json_encode($data);

    }


    /**
     * @api {post} /center/address_edit 编辑、添加地址
     * @apiGroup 地址管理
     * @apiDescription 编辑、添加收货地址
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   [AddressID]     地址ID
     * @apiParam {String}   Name            收货人
     * @apiParam {String}   Mobile          收货人电话
     * @apiParam {Number}   Province        省
     * @apiParam {Number}   City            市
     * @apiParam {Number}   Area            区
     * @apiParam {String}   Detailed        详细地址
     * @apiParam {Boolean}  default         是否是默认地址（1:是，0:否）
     * @apiParam {String}   [TrueName]      收货人真实姓名
     * @apiParam {String}   [Certificate]   收货人真实身份证号
     * @apiParam {String}   [url]           原访问页面路径
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/address_edit
     *
     * @apiSampleRequest /api/center/address_edit
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "缺少必要的参数UserID",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "操作成功",
     *          "url": "/",
     *     }
     */
    public function update(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'AddressID' => 'nullable|exists:user_address,Address_ID',
            'Name' => 'required|string|min:1|max:50',
            'Mobile' => 'required|mobile',
            'Province' => 'required|exists:area,area_id',
            'City' => 'required|exists:area,area_id',
            'Area' => 'required|exists:area,area_id',
            'Detailed' => 'required|string|min:1|max:150',
            'default' => 'required|in:0,1',
            'TrueName' => 'nullable|string|min:1|max:10',
            'Certificate' => 'nullable|string|size:18',
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

        $ua_obj = new User_Address();
        $AddressID = empty($input['AddressID']) ? 0 : $input['AddressID'];

        $Data1 = array(
            "Address_Name" => $input['Name'],
            "Address_Mobile" => $input["Mobile"],
            "Address_Province" => empty($input['Province']) ? "" : $input['Province'],
            "Address_City" => empty($input['City']) ? "" : $input['City'],
            "Address_Area" => empty($input['Area']) ? "" : $input['Area'],
            "Address_Detailed" => $input["Detailed"],
            "Address_Is_Default" => !empty($input["default"]) ? $input["default"] : 0,
            "Address_TrueName" => !empty($input["TrueName"]) ? $input["TrueName"] : '',
            "Address_Certificate" => !empty($input["Certificate"]) ? $input["Certificate"] : '',
        );

        if (!$AddressID) {
            //增加
            if($Data1['Address_Is_Default'] = 1){
                $address = $ua_obj->where('User_ID', $input['UserID'])
                    ->where('Address_Is_Default', 1)
                    ->first();
                if($address){
                    $address['Address_Is_Default'] = 0;
                    $address->save();
                }
            }

            $Data1["Users_ID"] = USERSID;
            $Data1["User_ID"] = $input['UserID'];
            $Flag = $ua_obj->create($Data1);
        } else {
            //修改
            if ($Data1['Address_Is_Default'] == 1) {
                $address = $ua_obj->where('User_ID', $input['UserID'])
                    ->where('Address_ID', '<>', $AddressID)
                    ->where('Address_Is_Default', 1)
                    ->first();
                if($address){
                    $address['Address_Is_Default'] = 0;
                    $address->save();
                }
            }
            $ua_obj->where('Address_ID', $AddressID)->update($Data1);
            $Flag = true;
        }

        if ($Flag) {
            $url = empty($input['url']) ? '': $input['url'];
            $Data = array('status' => 1, 'msg' => '操作成功', 'url' => $url);
        } else {
            $Data = array('status' => 0, 'msg' => '操作失败');
        }

        return json_encode($Data);
    }


    /**
     * @api {post} /center/address_del 删除地址
     * @apiGroup 地址管理
     * @apiDescription 删除收货地址
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   AddressID       地址ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/address_del
     *
     * @apiSampleRequest /api/center/address_del
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "缺少必要的参数UserID",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "操作成功",
     *     }
     */
    public function del(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'AddressID' => 'nullable|exists:user_address,Address_ID',
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

        $ua_obj = new User_Address();
        $rsAddress=$ua_obj->find($input['AddressID']);

        //将另一个地址设置为默认地址
        $set_another_default = 0;
        if($rsAddress['Address_Is_Default'] == 1){
            $set_another_default = 1;
        }

        $Flag=$rsAddress->delete();

        if ($Flag) {
            if($set_another_default == 1){
                $this->set_anoter_default($input['UserID']);
            }
            $Data = array('status' => 1, 'msg' => '操作成功');
        } else {
            $Data = array('status' => 0, 'msg' => '操作失败');
        }

        return json_encode($Data);

    }



    /**
     *默认地址被删除，设置另一个地址为默认
     */
    private function set_anoter_default($User_ID)
    {
        $ua_obj = new User_Address();
        $rsAddress = $ua_obj->where('User_ID', $User_ID)->orderByDesc('Address_ID')->first();
        if($rsAddress){
            $rsAddress['Address_Is_Default'] = 1;
            $rsAddress->save();
        }
    }

}
