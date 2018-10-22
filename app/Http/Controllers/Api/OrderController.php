<?php

namespace App\Http\Controllers\Api;

use App\Models\Area;
use App\Models\Dis_Level;
use App\Models\Dis_Record;
use App\Models\ShopConfig;
use App\Models\User_Back_Order;
use App\Models\User_Order_Commit;
use App\Models\UserOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * @api {get} /center/order_list  订单列表
     * @apiGroup 订单中心
     * @apiDescription 订单列表
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   [status]        订单列表（0:待确认，1:待付款，2:已付款，3:已发货，4:已完成）
     * @apiParam {Number}   cur_page=1      当前第几页
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        订单数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/order_list
     *
     * @apiSampleRequest /api/center/order_list
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "获取订单列表成功",
     *          "data": {
     *              "current_page": 1,  //当前页
     *              "data": [
     *                  {
     *                      "User_ID": "4",  //用户ID
     *                      "Order_ID": 4,  //订单ID
     *                      "Order_Status": 4, //订单状态（0:待确认，1:待付款，2:已付款，3:已发货，4:已完成）
     *                      "Order_CartList": {    //订单商品列表
     *                               "11": [   //商品ID
     *                                  {
     *                                      "ProductsName": "宝贝营养品",   //商品名称
     *                                      "ImgPath": "/uploadfiles/biz/2/image/5b188c7412.jpg",   //商品图片
     *                                      "ProductsPriceX": "38.00",   //商品现价
     *                                      "ProductsPriceY": "188.00",
     *                                      "Products_PriceS": "0.00",
     *                                      "ProductsPriceA": 0,
     *                                      "ProductsPriceAmax": 0,
     *                                      "Products_PayCoin": "0",   //商品所需积分
     *                                      "Products_Integration": "0",
     *                                      "user_curagio": 0,
     *                                      "Productsattrstrval": "",
     *                                      "Productsattrkeystrval": [],
     *                                      "ProductsWeight": "2.00",
     *                                      "Products_IsPaysBalance": "1",
     *                                      "Products_Shipping": null,
     *                                      "Products_Business": null,
     *                                      "Shipping_Free_Company": "0",
     *                                      "IsShippingFree": "0",
     *                                      "OwnerID": "3",
     *                                      "ProductsIsShipping": "0",
     *                                      "Qty": 1,   //商品数量
     *                                      "Products_FinanceType": "0",
     *                                      "Products_FinanceRate": "100.00",
     *                                      "Biz_FinanceType": 0,
     *                                      "Biz_FinanceRate": "100.00",
     *                                      "Property": [],   //商品属性（shu_pricesimp:属性价格，shu_value:商品属性值）
     *                                      "platForm_Income_Reward": "100",
     *                                      "area_Proxy_Reward": "0",
     *                                      "web_prie_shop": 38,
     *                                      "ProductsProfit": 38
     *                                  }
     *                              ]
     *                          },
     *                      "Order_TotalPrice": "1352.00",   //订单总价
     *                      "Order_Type": "shop",   //订单类型
     *                      "order_coin": 0,  //订单使用积分
     *                      "Order_ShippingID": "0",   //快递单号
     *                      "Is_Commit": 0,  //是否已评论（0:未评论，1:已评论）
     *                      "Order_No": "201807314",   //订单编号
     *                      "shipping_trace": "http://m.kuaidi100.com/index_all.html?type=&postid=&callbackurl=http://localhost:6002/index.php?UserID=4&status=0",  //快递100查询接口
     *                      "Shipping_Express": "",   //快递公司名称
     *                   }
     *              ],
     *              "from": 1,
     *              "last_page": 1,  //上一页
     *              "next_page_url": null,  //下一页
     *              "path": "http://localhost:6002/api/center/order_list",
     *              "per_page": 15,   //每页数量
     *              "prev_page_url": null,
     *              "to": 1,
     *              "total": 1   //消息总数
     *           },
     *     }
     */
    public function index(Request $request)
    {
        $input = $request->input();
        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'status' => 'nullable|in:0,1,2,3,4',
            'cur_page' => 'required|integer|min:1'
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'status.in' => '订单状态值不符合规则',
        ];
        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        //在订单列表中不显示申请退款订单
        $filter = ['User_ID', 'Order_ID', 'Order_Status', 'Order_CartList', 'Order_TotalPrice', 'Order_Type',
            'order_coin', 'Order_ShippingID', 'Is_Commit'];
        $cur_page = isset($input['cur_page']) ? $input['cur_page'] : 1;
        $uo_obj = new UserOrder();
        $uo_obj = $uo_obj->where('User_ID', $input['UserID'])
            ->where('Is_Backup', '<>', 1)
            ->whereRaw("Order_Type='shop' or Order_Type='offline_charge'")
            ->orderBy('Order_CreateTime', 'desc')
            ->orderBy('Order_Status', 'asc');

        if (!isset($input['status'])) {
            $lists = $uo_obj->paginate(15, $filter, $cur_page);
        } else {
            if ($input['status'] == 1) {
                $lists = $uo_obj->whereRaw("Order_Status= 1 or Order_Status=31")
                    ->paginate(15, $filter, $cur_page);
            } else {
                $lists = $uo_obj->where('Order_Status', $input['status'])
                    ->paginate(15, $filter, $cur_page);
            }
        }

        foreach ($lists as $key => $value) {
            $uo_obj2 = new UserOrder();
            $value['Order_CartList'] = json_decode($value['Order_CartList'], true);
            $value['Order_No'] = $uo_obj2->getorderno($value['Order_ID']);

            $shipping = json_decode(htmlspecialchars_decode($value['Order_Shipping']), true);
            if (!empty($shipping['Express'])) {
                $value['shipping_trace'] = 'http://m.kuaidi100.com/index_all.html?type=' . $shipping['Express'] . '&postid=' . $value["Order_ShippingID"] . '&callbackurl=' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
                $value['Shipping_Express'] = !empty($shipping['Express']) ? $shipping['Express'] . '-' : '';
            } else {
                $value['shipping_trace'] = 'javascript:void(0)';
                $value['Shipping_Express'] = '';
            }
        }

        $data = ['status' => 1, 'msg' => '获取订单列表成功', 'data' => $lists];
        return json_encode($data);

    }


    /**
     * @api {get} /center/order_detail  订单详情
     * @apiGroup 订单中心
     * @apiDescription 订单详情
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   OrderID         订单ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        订单数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/order_detail
     *
     * @apiSampleRequest /api/center/order_detail
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "获取订单详情成功",
     *          "data": {
     *                      "User_ID": "4",
     *                      "Order_ID": 4,
     *                      "Order_Type": "shop",   //订单类型
     *                      "Address_Name": "1",   //收货人姓名
     *                      "Address_Mobile": "13913141113",   //收货人手机号
     *                      "Address_Province": "10",
     *                      "Address_City": "166",
     *                      "Address_Area": "2066",
     *                      "Address_Detailed": "1",
     *                      "Address_TrueName": null,   //收货人真实姓名
     *                      "Address_Certificate": null,  //收货人身份证号
     *                      "Order_Remark": "",   //订单备注
     *                      "Order_ShippingID": "0",
     *                      "Order_TotalPrice": "1352.00",   //订单总价，含运费
     *                      "order_coin": 0,   //订单所需积分
     *                      "Order_CreateTime": "2018-07-31 10:53:43",   //订单创建时间
     *                      "Order_DefautlPaymentMethod": "",
     *                      "Order_PaymentMethod": "余额支付",   //支付方式
     *                      "Order_PaymentInfo": "",    //支付信息
     *                      "Order_Status": 4,   //订单状态代码
     *                      "Order_IsRead": 0,
     *                      "Order_TotalAmount": "1352.00",  //订单总价，含运费
     *                      "Owner_ID": 3,
     *                      "Is_Commit": 0,   //是否已评论
     *                      "Is_Backup": 0,   //是否是退货单
     *                      "Order_Code": "",    //消费券码
     *                      "Order_IsVirtual": 0,   //是否是虚拟订单
     *                      "Integral_Consumption": 0,
     *                      "Integral_Money": 0,   //积分抵现
     *                      "Integral_Get": 2000,   //获得积分
     *                      "Message_Notice": 0,
     *                      "Order_IsRecieve": 0,   //是否已收货
     *                      "Coupon_ID": 0,   //优惠券ID
     *                      "Coupon_Discount": "0.00",   //订单享受折扣
     *                      "Coupon_Cash": 0,   //抵现金
     *                      "deleted_at": null,
     *                      "Biz_ID": 2,
     *                      "Order_NeedInvoice": 0,
     *                      "Order_InvoiceInfo": "",
     *                      "Back_Amount": "0.00",
     *                      "Order_SendTime": "2018-07-31 10:54:07",   //订单发货时间
     *                      "Order_Virtual_Cards": null,   //虚拟卡
     *                      "Front_Order_Status": 0,
     *                      "transaction_id": "0",
     *                      "Is_Factorage": 0,
     *                      "Web_Price": "1352.00",
     *                      "Web_Pricejs": "1352.00",
     *                      "curagio_money": "0.00",
     *                      "Back_Integral": "0.00",
     *                      "muilti": 1,
     *                      "Is_Backup_js": 0,
     *                      "addtype": 0,
     *                      "All_Qty": 2,
     *                      "Is_User_Distribute": 0,
     *                      "Back_salems": null,
     *                      "back_qty": 0,
     *                      "back_qty_str": null,
     *                      "Back_Amount_Source": "0.00",
     *                      "cash_str": "{\"cash_str\":[]}",
     *                      "Web_Pricejs_new": "0.00",
     *                      "store_mention": 0,   //是否到店自提
     *                      "store_mention_time": "",   //到店自提时间
     *                      "status": "已完成",  //订单状态
     *                      "shipping": {
     *                                      "Express": "申通",   //物流公司
     *                                      "Price": 0   //运费
     *                          },
     *                      "CartList": {
     *                                      "11": [   //商品ID
     *                                          {
     *                                              "ProductsName": "宝贝营养品",   //商品名称
     *                                              "ImgPath": "/uploadfiles/biz/2/image/5b188c7412.jpg",   //商品图片
     *                                              "ProductsPriceX": "38.00",   //商品现价
     *                                              "ProductsPriceY": "188.00",   //商品原价
     *                                              "Products_PriceS": "0.00",
     *                                              "ProductsPriceA": 0,
     *                                              "ProductsPriceAmax": 0,
     *                                              "Products_PayCoin": "0",
     *                                              "Products_Integration": "0",
     *                                              "user_curagio": 0,
     *                                              "Productsattrstrval": "",
     *                                              "Productsattrkeystrval": [],
     *                                              "ProductsWeight": "2.00",
     *                                              "Products_IsPaysBalance": "1",
     *                                              "Products_Shipping": null,
     *                                              "Products_Business": null,
     *                                              "Shipping_Free_Company": "0",
     *                                              "IsShippingFree": "0",
     *                                              "OwnerID": "3",
     *                                              "ProductsIsShipping": "0",
     *                                              "Qty": 1,  //商品数量
     *                                              "Products_FinanceType": "0",
     *                                              "Products_FinanceRate": "100.00",
     *                                              "Biz_FinanceType": 0,
     *                                              "Biz_FinanceRate": "100.00",
     *                                              "Property": [],   //商品属性（shu_pricesimp:属性价格，shu_value:商品属性值）
     *                                              "platForm_Income_Reward": "100",
     *                                              "area_Proxy_Reward": "0",
     *                                              "web_prie_shop": 38,
     *                                              "ProductsProfit": 38
     *                                          }
     *                                      ],
     *                           },
     *                       "amount": 1352,   //订单总价，不含运费
     *                       "Province": "江苏,",   //收货地址：省
     *                       "City": "苏州市,",     //收货地址：市
     *                       "Area": "吴中区,",    //收货地址：区
     *                       "is_level_product": 0,   //是否是门槛商品（0:否，1:是）
     *                       "Confirm_Time": "7",  //还有几天自动收货
     *                       "Auto_Confirm_Time": "7",  //店家设置了几天自动收货时间
     *                       "Order_No": "201807314",   //订单编号
     *              }
     *     }
     */
    public function show(Request $request)
    {
        $input = $request->input();
        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'OrderID' => "required|exists:user_order,Order_ID,User_ID,{$input['UserID']}",
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'OrderID.required' => '缺少必要的参数OrderID',
            'OrderID.exists' => '此订单不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $uo_obj = new UserOrder();
        $rsOrder = $uo_obj->find($input['OrderID']);

        $Order_Status = array("待确认", "待付款", "已付款", "已发货", "已完成");
        $rsOrder['status'] = $Order_Status[$rsOrder["Order_Status"]];

        $rsOrder['shipping'] = json_decode(htmlspecialchars_decode($rsOrder["Order_Shipping"]), true);
        $rsOrder['CartList'] = json_decode(htmlspecialchars_decode($rsOrder["Order_CartList"]), true);

        $rsOrder['amount'] = $fee = 0;
        if ($rsOrder['store_mention'] == 1) {//到店自提订单不计算运费
            $rsOrder['amount'] = $rsOrder["Order_TotalAmount"];
        } else {
            $fee = empty($Shipping["Price"]) ? 0 : $Shipping["Price"];
            $rsOrder['amount'] = $rsOrder["Order_TotalAmount"] - $fee;
        }

        //收货地址
        $a_obj = new Area();
        if (!empty($rsOrder['Address_Province'])) {
            $province = $a_obj->select('area_name')->find($rsOrder['Address_Province']);
            $rsOrder['Province'] = $province['area_name'] . ',';
        }
        if (!empty($rsOrder['Address_City'])) {
            $City = $a_obj->select('area_name')->find($rsOrder['Address_City']);
            $rsOrder['City'] = $City['area_name'] . ',';
        }
        if (!empty($rsOrder['Address_Area'])) {
            $Area = $a_obj->select('area_name')->find($rsOrder['Address_Area']);
            $rsOrder['Area'] = $Area['area_name'];
        }

        /*判断商品是否是门槛商品，如果是则不能退款*/
        $dl_obj = new Dis_Level();
        $dis_level = $dl_obj->select('Level_LimitValue')->get();
        $is_level_product_id = array();
        foreach ($dis_level as $key => $value) {
            $is_level_product_id[] = explode('|', $value['Level_LimitValue'])[1];
        }
        $is_lp_id = implode(',', $is_level_product_id);
        $is_lp_id = explode(',', $is_lp_id);
        $CartListKey = array_keys($rsOrder['CartList'])[0];
        if (in_array($CartListKey, $is_lp_id)) {
            $rsOrder['is_level_product'] = 1;//门槛商品
        } else {
            $rsOrder['is_level_product'] = 0;//非门槛商品
        }

        //自动收货时间
        $sc_obj = new ShopConfig();
        $rsConfig = $sc_obj->find(USERSID);
        $rsOrder['Confirm_Time'] = number_format(($rsConfig['Confirm_Time'] - time() + $rsOrder["Order_SendTime"]) / 86400, 0, '.', '');
        $rsOrder['Auto_Confirm_Time'] = $rsConfig['Confirm_Time'] / 86400;

        $rsOrder['Order_SendTime'] = date('Y-m-d H:i:s', $rsOrder['Order_SendTime']);
        $rsOrder['Order_CreateTime'] = date('Y-m-d H:i:s', $rsOrder['Order_CreateTime']);
        $rsOrder['Order_No'] = $uo_obj->getorderno($rsOrder['Order_ID']);

        unset($rsOrder['Order_CartList']);
        unset($rsOrder['Order_Shipping']);

        $data = ['status' => 1, 'msg' => '获取订单详情成功', 'data' => $rsOrder];
        return json_encode($data);

    }


    /**
     * @api {post} /center/order_cancel  取消订单
     * @apiGroup 订单中心
     * @apiDescription 取消订单
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   OrderID         订单ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        订单数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/order_cancel
     *
     * @apiSampleRequest /api/center/order_cancel
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "取消订单失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "取消订单成功",
     *     }
     */
    public function cancel(Request $request)
    {
        $input = $request->input();
        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'OrderID' => "required|exists:user_order,Order_ID,User_ID,{$input['UserID']}",
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'OrderID.required' => '缺少必要的参数OrderID',
            'OrderID.exists' => '此订单不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $dr_obj = new Dis_Record();
        $uo_obj = new UserOrder();

        //若是分销订单，删除分销记录
        if ($uo_obj->is_distribute_order($input['OrderID'])) {
            $dr_obj->delete_distribute_record($input['OrderID']);
        }

        $Flag = $uo_obj->destroy($input['OrderID']);

        if ($Flag) {
            $data = ['status' => 1, 'msg' => '取消订单成功'];
        } else {
            $data = ['status' => 0, 'msg' => '取消订单失败'];
        }

        return json_encode($data);
    }


    /**
     * @api {post} /center/order_commit  提交评论
     * @apiGroup 订单中心
     * @apiDescription 提交评论
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   OrderID         订单ID
     * @apiParam {Number}   Score           卖家打分（1，2，3，4，5）
     * @apiParam {String}   Note            评论内容
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        订单数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/order_commit
     *
     * @apiSampleRequest /api/center/order_commit
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "提交评论失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "提交评论成功",
     *     }
     */
    public function commit(Request $request)
    {
        $input = $request->input();
        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'OrderID' => "required|exists:user_order,Order_ID,User_ID,{$input['UserID']}",
            'Score' => 'required|in:1,2,3,4,5',
            'Note' => 'required'
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'OrderID.required' => '缺少必要的参数OrderID',
            'OrderID.exists' => '此订单不存在',
            'Note.required' => '评论内容不能为空',
        ];
        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $sc_obj = new ShopConfig();
        $rsConfig = $sc_obj->select('Commit_Check')->find(USERSID);

        $uo_obj = new UserOrder();
        $rsOrder = $uo_obj->where('Order_Status', 4)->find($input['OrderID']);
        if (!$rsOrder) {
            $data = ["status" => 0, "msg" => "无此订单"];
        } else {
            if ($rsOrder["Is_Commit"] == 1) {
                $data = ["status" => 0, "msg" => "此订单已评论过，不可重复评论"];
            } else {
                $rsOrder->Is_Commit = 1;
                $rsOrder->save();

                $CartList = json_decode(htmlspecialchars_decode($rsOrder["Order_CartList"]), true);
                foreach ($CartList as $key => $v) {
                    $Data2 = array(
                        "MID" => $rsOrder["Order_Type"],
                        "Order_ID" => $input['OrderID'],
                        "Biz_ID" => $rsOrder["Biz_ID"],
                        "Product_ID" => $key,
                        "Score" => $input["Score"],
                        "Note" => $input["Note"],
                        "Status" => $rsConfig["Commit_Check"] == 1 ? 1 : 0,
                        "Users_ID" => USERSID,
                        "User_ID" => $input['UserID'],
                        "CreateTime" => time()
                    );
                    $uoc_obj = new User_Order_Commit();
                    $uoc_obj->create($Data2);
                }

                $data = ['status' => 1, 'msg' => '提交评论成功'];
            }
        }

        return json_encode($data);
    }


    /**
     * @api {post} /center/order_receive  确认收货
     * @apiGroup 订单中心
     * @apiDescription 确认收货
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID          用户ID
     * @apiParam {Number}   OrderID         订单ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        订单数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/order_receive
     *
     * @apiSampleRequest /api/center/order_receive
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "确认收货失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "确认收货成功",
     *     }
     */
    public function receive(Request $request)
    {
        $input = $request->input();
        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'OrderID' => "required|exists:user_order,Order_ID,User_ID,{$input['UserID']}",
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'OrderID.required' => '缺少必要的参数OrderID',
            'OrderID.exists' => '此订单不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $uo_obj = new UserOrder();
        $rsOrder = $uo_obj->select('Order_Status', 'Order_Type')->find($input['OrderID']);

        $ubo_obj = new User_Back_Order();
        $rsOrder_back = $ubo_obj->select('Back_Status')
            ->where('Back_Status', '<>', 4)
            ->where('Order_ID', $input['OrderID'])->first();
        if ($rsOrder_back) {
            $data = ["status" => 0, "msg" => '退款未结束，无法确认收货'];
            return json_encode($data);
        }

        if ($rsOrder["Order_Status"] <> 3) {
            $data = ["status" => 0, "msg" => '只有在‘已发货’状态下才可确认收货'];
        } else {
            $Flag = $uo_obj->confirmReceive($input['OrderID']);
            if ($Flag) {
                $data = ["status" => 1, "msg" => '确认收货成功'];
            } else {
                $data = ["status" => 0, "msg" => '确认收货失败'];
            }
        }
        return json_encode($data);

    }
}
