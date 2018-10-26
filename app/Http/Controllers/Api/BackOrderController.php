<?php

namespace App\Http\Controllers\Api;

use App\Models\Area;
use App\Models\User_Back_Order;
use App\Models\User_Recieve_Address;
use App\Models\UserOrder;
use App\Services\ServiceBackOrder;
use App\Services\ServiceOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BackOrderController extends Controller
{
    /**
     * @api {get} /center/backorder_list 我的退款单
     * @apiGroup 退款、售后
     * @apiDescription 获取用户退款单列表
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   cur_page=1      当前页
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        退款单列表数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/backorder_list
     *
     * @apiSampleRequest /api/center/backorder_list
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
     *                      "Back_ID": 2,
     *                      "Back_Sn": "",
     *                      "ProductID": 11,
     *                      "Back_Json":
     *                          {
     *                              "ProductsName": "宝贝营养品",   //商品名称
     *                              "ImgPath": "/uploadfiles/biz/2/image/5b188c7412.jpg",   //商品图片
     *                              "ProductsPriceX": "38.00",   //现价
     *                              "ProductsPriceY": "188.00",   //原价
     *                              "Products_PriceS": "0.00",
     *                              "ProductsPriceA": 0,
     *                              "ProductsPriceAmax": 0,
     *                              "Products_PayCoin": "0",
     *                              "Products_Integration": "0",
     *                              "user_curagio": 0,
     *                              "Productsattrstrval": "",
     *                              "Productsattrkeystrval": [],
     *                              "ProductsWeight": "2.00",   //重量
     *                              "Products_IsPaysBalance": "1",
     *                              "Products_Shipping": null,
     *                              "Products_Business": null,
     *                              "Shipping_Free_Company": "0",
     *                              "IsShippingFree": "0",
     *                              "OwnerID": "3",
     *                              "ProductsIsShipping": "0",
     *                              "Qty": "1",  //商品数量
     *                              "Products_FinanceType": "0",
     *                              "Products_FinanceRate": "100.00",
     *                              "Biz_FinanceType": 0,
     *                              "Biz_FinanceRate": "100.00",
     *                              "Property": [], //商品属性（shu_pricesimp:属性价格，shu_value:商品属性值）
     *                              "nobi_ratio": "0",
     *                              "platForm_Income_Reward": "100",
     *                              "area_Proxy_Reward": "0",
     *                              "sha_Reward": "0",
     *                              "web_prie_shop": 38,
     *                              "ProductsProfit": 38
     *                          },
     *                      "Back_Status": 0,
     *                      "ProductsPriceX": "38.00",   //商品原价
     *                      "status": "申请中"   //退款单状态
     *                      "ImgPath": "http://localhost:6001/uploadfiles/biz/2/image/5b188c7412.jpg"   //处理后的商品图片路径
     *                  },
     *              ],
     *              "from": 1,
     *              "last_page": 1,  //上一页
     *              "next_page_url": null,  //下一页
     *              "path": "http://localhost:6002/api/center/backorder_list",
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
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $ubo_obj = new User_Back_Order();
        $_STATUS = array('申请中','卖家同意','买家发货','卖家收货并确定退款价格','完成','卖家拒绝退款');
        $filter = ['Back_ID','Back_Sn','ProductID','Back_Json','Back_Status'];

        $list = $ubo_obj->where('User_ID', $input['UserID'])
            ->whereNotIn('Back_Type', ['pintuan','dangou'])
            ->orderByDesc('Back_CreateTime')
            ->orderByDesc('Back_UpdateTime')
            ->paginate(20, $filter, $input['cur_page']);

        foreach($list as $key => $value){
            $value['Back_Json'] = json_decode($value['Back_Json'],true);
            if(!empty($value['Back_Json']["Property"]) && isset($value['Back_Json']["Property"]['shu_pricesimp'])){
                $value['ProductsPriceX'] = $value['Back_Json']["Property"]['shu_pricesimp'];
            }else{
                $value['ProductsPriceX'] = $value['Back_Json']["ProductsPriceX"];
            }
            $value['status'] = $_STATUS[$value["Back_Status"]];
            $value['ImgPath'] = ADMIN_BASE_HOST.$value['Back_Json']['ImgPath'];

        }

        $data = ['status' => 1, 'msg' => '成功', 'data' => $list];

        return json_encode($data);
    }


    /**
     * @api {get} /center/backorder_detail 退款单详情
     * @apiGroup 退款、售后
     * @apiDescription 获取用户退款单详情
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   BackID          退款单ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        退款单详情数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/backorder_detail
     *
     * @apiSampleRequest /api/center/backorder_detail
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
     *               "Back_ID": 2,   //退款单ID
     *               "Back_Sn": "",  //退款单编号
     *               "ProductID": 11,   //商品ID
     *               "User_ID": 18,  //用户ID
     *               "Back_Json":
     *                     {
     *                         "ProductsName": "宝贝营养品",   //商品名称
     *                         "ImgPath": "/uploadfiles/biz/2/image/5b188c7412.jpg",   //商品图片
     *                         "ProductsPriceX": "38.00",   //现价
     *                         "ProductsPriceY": "188.00",   //原价
     *                         "Products_PriceS": "0.00",
     *                         "ProductsPriceA": 0,
     *                         "ProductsPriceAmax": 0,
     *                         "Products_PayCoin": "0",
     *                         "Products_Integration": "0",
     *                         "user_curagio": 0,
     *                         "Productsattrstrval": "",
     *                         "Productsattrkeystrval": [],
     *                         "ProductsWeight": "2.00",   //重量
     *                         "Products_IsPaysBalance": "1",
     *                         "Products_Shipping": null,
     *                         "Products_Business": null,
     *                         "Shipping_Free_Company": "0",
     *                         "IsShippingFree": "0",
     *                         "OwnerID": "3",
     *                         "ProductsIsShipping": "0",
     *                         "Qty": "1",  //商品数量
     *                         "Products_FinanceType": "0",
     *                         "Products_FinanceRate": "100.00",
     *                         "Biz_FinanceType": 0,
     *                         "Biz_FinanceRate": "100.00",
     *                         "Property": [], //商品属性（shu_pricesimp:属性价格，shu_value:商品属性值）
     *                         "platForm_Income_Reward": "100",
     *                         "web_prie_shop": 38,
     *                         "ProductsProfit": 38
     *                     },
     *               "Back_Status": 0,
     *               "Back_Qty": 1,   //退货数量
     *               "Back_Amount": "38.00",   //退款金额
     *               "Back_Account": "sgeges",     //退款账户
     *               "status": "申请中"   //退款单状态
     *               "Back_Shipping": "dfs"   //物流方式
     *               "Back_ShippingID": "sf2146544798"   //物流单号
     *               "Back_CreateTime": "2018-10-22 17:35:06"   //退款单创建时间
     *               "ImgPath": "http://localhost:6001/uploadfiles/biz/2/image/5b188c7412.jpg"   //处理后的商品图片路径
     *               "recieve_info": {  //商家收货信息
     *                          "RecieveProvince": "内蒙古",  //省
     *                          "RecieveCity": "呼和浩特",  //市
     *                          "RecieveArea": "中山区", //区
     *                          "RecieveAddress": "sgesg",  //详细地址
     *                          "RecieveName": "dfsf",  //收货人
     *                          "RecieveMobile": "122656"   //收货电话
     *                      },
     *               "details": [   //操作记录
     *                  {
     *                      "itemid": 1,
     *                      "backid": 2,
     *                      "detail": "买家申请退款，退款金额：38.00，退款原因：sgegse",  //描述
     *                      "status": 0,
     *                      "createtime": "2018-10-22 17:35:06"  //操作时间
     *                  },
     *                  {
     *                      "itemid": 2,
     *                      "backid": 2,
     *                      "detail": "买家已发货，物流方式：dfs，物流单号：sf2146544798",
     *                      "status": 1,
     *                      "createtime": "2018-10-23 10:25:56"
     *                  }
     *               ]
     *           },
     *     }
     */
    public function show(Request $request)
    {
        $input = $request->input();
        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'BackID' => "required|exists:user_back_order,Back_ID,User_ID,{$input['UserID']}",
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'BackID.exists' => '此订单不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $ubo_obj = new User_Back_Order();
        $ura_obj = new User_Recieve_Address();
        $a_obj = new Area();

        $filter = [
            'Back_ID', 'User_ID', 'Back_Sn', 'Back_Json', 'ProductID', 'Back_Shipping', 'Back_ShippingID',
            'Back_CreateTime','Back_Status','Back_Qty','Back_Amount','Back_Account'
        ];
        $back = $ubo_obj->select($filter)->find($input['BackID']);
        $back['Back_Json'] = json_decode(htmlspecialchars_decode($back['Back_Json']),TRUE);
        $back['ImgPath'] = ADMIN_BASE_HOST.$back['Back_Json']['ImgPath'];
        $_STATUS = ['申请中','卖家同意','买家发货','卖家收货并确定退款价格','完成','卖家拒绝退款'];
        $back['status'] = $_STATUS[$back["Back_Status"]];
        $back["Back_CreateTime"] = date("Y-m-d H:i:s",$back["Back_CreateTime"]);
        //退款单操作记录
        foreach($back['details'] as $key => $value){
            $value['createtime'] = date("Y-m-d H:i:s",$value["createtime"]);
        }
        //商家收货信息
        $Recieve_Address = $ura_obj->find(USERSID);
        if(!empty($Recieve_Address['RecieveProvince'])){
            $Recieve_Address['RecieveProvince'] = $a_obj->find($Recieve_Address['RecieveProvince'])['area_name'];
        }
        if(!empty($Recieve_Address['RecieveCity'])){
            $Recieve_Address['RecieveCity'] = $a_obj->find($Recieve_Address['RecieveCity'])['area_name'];
        }
        if(!empty($Recieve_Address['RecieveArea'])){
            $Recieve_Address['RecieveArea'] = $a_obj->find($Recieve_Address['RecieveArea'])['area_name'];
        }
        $back['recieve_info'] = $Recieve_Address;

        $data = ['status' => 1, 'msg' => '成功', 'data' => $back];
        return json_encode($data);
    }


    /**
     * @api {post} /center/order_apply_back  申请退货
     * @apiGroup 退款、售后
     * @apiDescription 申请退货
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   OrderID         订单ID
     * @apiParam {Number}   ProductsID      商品ID
     * @apiParam {Number}   KEY=0           订单cartlist数组的第二层Key值
     * @apiParam {Number}   Qty             退货数量
     * @apiParam {String}   Reason          退货原因
     * @apiParam {String}   Account         退款账号和户名
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/order_apply_back
     *
     * @apiSampleRequest /api/center/order_apply_back
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "申请退货失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "申请退货成功",
     *     }
     */
    public function apply_back(Request $request)
    {
        $input = $request->input();
        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'OrderID' => "required|exists:user_order,Order_ID,User_ID,{$input['UserID']}",
            'ProductsID' => "required|exists:shop_products,Products_ID",
            'KEY' => "required|integer|min:0",
            'Qty' => "required|integer|min:1",
            'Reason' => "required|string",
            'Account' => "required|string",
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'OrderID.exists' => '此订单不存在',
            'Qty.required' => '退货数量不能为空',
            'Reason.required' => '退货原因不能为空',
            'Account.required' => '退款账号和户名不能为空',
        ];
        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $uo_obj = new UserOrder();
        $rsOrder = $uo_obj->find($input['OrderID']);

        if($rsOrder["Order_Status"]<>2 && $rsOrder["Order_Status"]<>3){
            $Data=array("status"=>0,"msg"=>'只有“已付款”状态下的订单才可申请退款');
        }else{
            $CartList=json_decode(htmlspecialchars_decode($rsOrder["Order_CartList"]),true);
            if(empty($CartList[$input["ProductsID"]][$input["KEY"]])){
                $Data=array("status"=>0,"msg"=>'退款的商品不存在');
            }else{
                $item = $CartList[$input["ProductsID"]][$input["KEY"]];
                if($item["Qty"] < $input["Qty"]){
                    $Data=array("status"=>0, "msg"=>'退款的退款数量大于商品总数量');
                }else{
                    $backup = new ServiceBackOrder();
                    $backup->add_backup($rsOrder, $input["ProductsID"], $input["KEY"], $input["Qty"], $input["Reason"], $input["Account"]);
                    $Data=array("status"=>1,"msg"=>'申请退货成功');
                }
            }
        }
        return json_encode($Data);
    }


    /**
     * @api {post} /center/backorder_send  买家发货
     * @apiGroup 退款、售后
     * @apiDescription 买家发货
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   BackID          订单ID
     * @apiParam {String}   Shipping        物流方式
     * @apiParam {String}   ShippingID      物流单号
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/backorder_send
     *
     * @apiSampleRequest /api/center/backorder_send
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": '只有“卖家同意状态下的退货单才可发货操作”',
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "发货成功",
     *     }
     */
    public function send(Request $request)
    {
        $input = $request->input();
        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'BackID' => "required|exists:user_back_order,Back_ID,User_ID,{$input['UserID']}",
            'Shipping' => 'required|string|max:20',
            'ShippingID' => 'required|string|max:20',
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'BackID.required' => '缺少必要的参数',
            'Shipping.required' => '退货数量不能为空',
            'ShippingID.required' => '退货原因不能为空',
        ];
        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $ubo_obj = new User_Back_Order();

        $rsBack = $ubo_obj->where('Back_ID', $input['BackID'])->first();
        if($rsBack["Back_Status"]<>1){
            $Data=array("status"=>0,"msg"=>'只有“卖家同意状态下的退货单才可发货操作”');
        }else{
            $backup = new ServiceOrder();
            $backup->update_backup("buyer_send", $input['BackID'], $input["Shipping"].'||%$%'.$input["ShippingID"]);
            $Data = array("status"=>1,"msg"=>"发货成功");
        }

        return json_encode($Data);
    }

}
