define({ "api": [
  {
    "type": "get",
    "url": "/test/:id",
    "title": "测试接口",
    "name": "____",
    "group": "test",
    "description": "<p>测试接口</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": true,
            "field": "access_token",
            "description": "<p>Users unique access_token.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>用户ID</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "firstname",
            "description": "<p>Firstname of the Admin.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n  \"status\": \"1\"\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/test/4711",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/test/:id"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 404 Not Found\n{\n  \"error\": \"UserNotFound\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "App/Http/Controllers/Api/TestController.php",
    "groupTitle": "test"
  },
  {
    "type": "get",
    "url": "/center/order_detail",
    "title": "订单详情",
    "group": "用户中心",
    "description": "<p>订单详情</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>用户登陆认证token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "UserID",
            "description": "<p>用户ID</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "OrderID",
            "description": "<p>订单ID</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>状态码（0:失败，1:成功, -1:需要重新登陆）</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "msg",
            "description": "<p>返回状态说明信息</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "data",
            "description": "<p>订单数据</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"获取订单详情成功\",\n     \"data\": {\n                 \"User_ID\": \"4\",\n                 \"Order_ID\": 4,\n                 \"Order_Type\": \"shop\",   //订单类型\n                 \"Address_Name\": \"1\",   //收货人姓名\n                 \"Address_Mobile\": \"13913141113\",   //收货人手机号\n                 \"Address_Province\": \"10\",\n                 \"Address_City\": \"166\",\n                 \"Address_Area\": \"2066\",\n                 \"Address_Detailed\": \"1\",\n                 \"Address_TrueName\": null,   //收货人真实姓名\n                 \"Address_Certificate\": null,  //收货人身份证号\n                 \"Order_Remark\": \"\",   //订单备注\n                 \"Order_ShippingID\": \"0\",\n                 \"Order_TotalPrice\": \"1352.00\",   //订单总价，含运费\n                 \"order_coin\": 0,   //订单所需积分\n                 \"Order_CreateTime\": \"2018-07-31 10:53:43\",   //订单创建时间\n                 \"Order_DefautlPaymentMethod\": \"\",\n                 \"Order_PaymentMethod\": \"余额支付\",   //支付方式\n                 \"Order_PaymentInfo\": \"\",    //支付信息\n                 \"Order_Status\": 4,   //订单状态代码\n                 \"Order_IsRead\": 0,\n                 \"Order_TotalAmount\": \"1352.00\",  //订单总价，含运费\n                 \"Owner_ID\": 3,\n                 \"Is_Commit\": 0,   //是否已评论\n                 \"Is_Backup\": 0,   //是否是退货单\n                 \"Order_Code\": \"\",    //消费券码\n                 \"Order_IsVirtual\": 0,   //是否是虚拟订单\n                 \"Integral_Consumption\": 0,\n                 \"Integral_Money\": 0,   //积分抵现\n                 \"Integral_Get\": 2000,   //获得积分\n                 \"Message_Notice\": 0,\n                 \"Order_IsRecieve\": 0,   //是否已收货\n                 \"Coupon_ID\": 0,   //优惠券ID\n                 \"Coupon_Discount\": \"0.00\",   //订单享受折扣\n                 \"Coupon_Cash\": 0,   //抵现金\n                 \"deleted_at\": null,\n                 \"Biz_ID\": 2,\n                 \"Order_NeedInvoice\": 0,\n                 \"Order_InvoiceInfo\": \"\",\n                 \"Back_Amount\": \"0.00\",\n                 \"Order_SendTime\": \"2018-07-31 10:54:07\",   //订单发货时间\n                 \"Order_Virtual_Cards\": null,   //虚拟卡\n                 \"Front_Order_Status\": 0,\n                 \"transaction_id\": \"0\",\n                 \"Is_Factorage\": 0,\n                 \"Web_Price\": \"1352.00\",\n                 \"Web_Pricejs\": \"1352.00\",\n                 \"curagio_money\": \"0.00\",\n                 \"Back_Integral\": \"0.00\",\n                 \"muilti\": 1,\n                 \"Is_Backup_js\": 0,\n                 \"addtype\": 0,\n                 \"All_Qty\": 2,\n                 \"Is_User_Distribute\": 0,\n                 \"Back_salems\": null,\n                 \"back_qty\": 0,\n                 \"back_qty_str\": null,\n                 \"Back_Amount_Source\": \"0.00\",\n                 \"cash_str\": \"{\\\"cash_str\\\":[]}\",\n                 \"Web_Pricejs_new\": \"0.00\",\n                 \"store_mention\": 0,   //是否到店自提\n                 \"store_mention_time\": \"\",   //到店自提时间\n                 \"status\": \"已完成\",  //订单状态\n                 \"shipping\": {\n                                 \"Express\": \"申通\",   //物流公司\n                                 \"Price\": 0   //运费\n                     },\n                 \"CartList\": {\n                                 \"11\": [   //商品ID\n                                     {\n                                         \"ProductsName\": \"宝贝营养品\",   //商品名称\n                                         \"ImgPath\": \"/uploadfiles/biz/2/image/5b188c7412.jpg\",   //商品图片\n                                         \"ProductsPriceX\": \"38.00\",   //商品现价\n                                         \"ProductsPriceY\": \"188.00\",   //商品原价\n                                         \"Products_PriceS\": \"0.00\",\n                                         \"ProductsPriceA\": 0,\n                                         \"ProductsPriceAmax\": 0,\n                                         \"Products_PayCoin\": \"0\",\n                                         \"Products_Integration\": \"0\",\n                                         \"user_curagio\": 0,\n                                         \"Productsattrstrval\": \"\",\n                                         \"Productsattrkeystrval\": [],\n                                         \"ProductsWeight\": \"2.00\",\n                                         \"Products_IsPaysBalance\": \"1\",\n                                         \"Products_Shipping\": null,\n                                         \"Products_Business\": null,\n                                         \"Shipping_Free_Company\": \"0\",\n                                         \"IsShippingFree\": \"0\",\n                                         \"OwnerID\": \"3\",\n                                         \"ProductsIsShipping\": \"0\",\n                                         \"Qty\": 1,  //商品数量\n                                         \"Products_FinanceType\": \"0\",\n                                         \"Products_FinanceRate\": \"100.00\",\n                                         \"Biz_FinanceType\": 0,\n                                         \"Biz_FinanceRate\": \"100.00\",\n                                         \"Property\": [],   //商品属性（shu_pricesimp:属性价格，shu_value:商品属性值）\n                                         \"platForm_Income_Reward\": \"100\",\n                                         \"area_Proxy_Reward\": \"0\",\n                                         \"web_prie_shop\": 38,\n                                         \"ProductsProfit\": 38\n                                     }\n                                 ],\n                      },\n                  \"amount\": 1352,   //订单总价，不含运费\n                  \"Province\": \"江苏,\",   //收货地址：省\n                  \"City\": \"苏州市,\",     //收货地址：市\n                  \"Area\": \"吴中区,\",    //收货地址：区\n                  \"is_level_product\": 0,   //是否是门槛商品（0:否，1:是）\n                  \"Confirm_Time\": \"7\",  //还有几天自动收货\n                  \"Auto_Confirm_Time\": \"7\",  //店家设置了几天自动收货时间\n                  \"Order_No\": \"201807314\",   //订单编号\n         }\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/order_detail",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/order_detail"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"失败\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "App/Http/Controllers/Api/Center/OrderController.php",
    "groupTitle": "用户中心",
    "name": "GetCenterOrder_detail"
  },
  {
    "type": "get",
    "url": "/center/order_list",
    "title": "订单列表",
    "group": "用户中心",
    "description": "<p>订单列表</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>用户登陆认证token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "UserID",
            "description": "<p>用户ID</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "status",
            "defaultValue": "0",
            "description": "<p>订单列表（0:全部，1:待付款，2:已付款，3:已发货，4:已完成）</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "cur_page",
            "defaultValue": "1",
            "description": "<p>当前第几页</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>状态码（0:失败，1:成功, -1:需要重新登陆）</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "msg",
            "description": "<p>返回状态说明信息</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "data",
            "description": "<p>订单数据</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"获取订单列表成功\",\n     \"data\": {\n         \"current_page\": 1,  //当前页\n         \"data\": [\n             {\n                 \"User_ID\": \"4\",  //用户ID\n                 \"Order_ID\": 4,  //订单ID\n                 \"Order_Status\": 4, //订单状态（1:待付款，2:已付款，3:已发货，4:已完成）\n                 \"Order_CartList\": {    //订单商品列表\n                          \"11\": [   //商品ID\n                             {\n                                 \"ProductsName\": \"宝贝营养品\",   //商品名称\n                                 \"ImgPath\": \"/uploadfiles/biz/2/image/5b188c7412.jpg\",   //商品图片\n                                 \"ProductsPriceX\": \"38.00\",   //商品现价\n                                 \"ProductsPriceY\": \"188.00\",\n                                 \"Products_PriceS\": \"0.00\",\n                                 \"ProductsPriceA\": 0,\n                                 \"ProductsPriceAmax\": 0,\n                                 \"Products_PayCoin\": \"0\",   //商品所需积分\n                                 \"Products_Integration\": \"0\",\n                                 \"user_curagio\": 0,\n                                 \"Productsattrstrval\": \"\",\n                                 \"Productsattrkeystrval\": [],\n                                 \"ProductsWeight\": \"2.00\",\n                                 \"Products_IsPaysBalance\": \"1\",\n                                 \"Products_Shipping\": null,\n                                 \"Products_Business\": null,\n                                 \"Shipping_Free_Company\": \"0\",\n                                 \"IsShippingFree\": \"0\",\n                                 \"OwnerID\": \"3\",\n                                 \"ProductsIsShipping\": \"0\",\n                                 \"Qty\": 1,   //商品数量\n                                 \"Products_FinanceType\": \"0\",\n                                 \"Products_FinanceRate\": \"100.00\",\n                                 \"Biz_FinanceType\": 0,\n                                 \"Biz_FinanceRate\": \"100.00\",\n                                 \"Property\": [],   //商品属性（shu_pricesimp:属性价格，shu_value:商品属性值）\n                                 \"platForm_Income_Reward\": \"100\",\n                                 \"area_Proxy_Reward\": \"0\",\n                                 \"web_prie_shop\": 38,\n                                 \"ProductsProfit\": 38\n                             }\n                         ]\n                     },\n                 \"Order_TotalPrice\": \"1352.00\",   //订单总价\n                 \"Order_Type\": \"shop\",   //订单类型\n                 \"order_coin\": 0,  //订单使用积分\n                 \"Order_ShippingID\": \"0\",   //快递单号\n                 \"Order_No\": \"201807314\",   //订单编号\n                 \"shipping_trace\": \"http://m.kuaidi100.com/index_all.html?type=&postid=&callbackurl=http://localhost:6002/index.php?UserID=4&status=0\",  //快递100查询接口\n                 \"Shipping_Express\": \"\",   //快递公司名称\n              }\n         ],\n         \"from\": 1,\n         \"last_page\": 1,  //上一页\n         \"next_page_url\": null,  //下一页\n         \"path\": \"http://localhost:6002/api/center/order_list\",\n         \"per_page\": 15,   //每页数量\n         \"prev_page_url\": null,\n         \"to\": 1,\n         \"total\": 1   //消息总数\n      },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/order_list",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/order_list"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"失败\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "App/Http/Controllers/Api/Center/OrderController.php",
    "groupTitle": "用户中心",
    "name": "GetCenterOrder_list"
  },
  {
    "type": "get",
    "url": "/center/sys_message_list",
    "title": "系统消息列表",
    "group": "用户中心",
    "description": "<p>系统消息列表</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>用户登陆认证token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "UserID",
            "description": "<p>用户ID</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "cur_page",
            "defaultValue": "1",
            "description": "<p>当前第几页</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>状态码（0:失败，1:成功, -1:需要重新登陆）</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "msg",
            "description": "<p>返回状态说明信息</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "data",
            "description": "<p>系统消息数据</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"获取系统消息列表成功\",\n     \"data\": {\n         \"current_page\": 1,  //当前页\n         \"data\": [\n             {\n                 \"Message_ID\": 1,   //消息ID\n                 \"Message_Title\": \"欢迎关注观前一号商城\",  //消息标题\n                 \"Message_Description\": \"欢迎关注观前一号商城\",   //消息内容\n                 \"Message_CreateTime\": \"2018-07-17 14:41:40\",   //消息发布时间\n                 \"User_ID\": 0,   //用户ID,为0时表示此消息是群发消息\n                 \"is_read\": 0   //消息是否已读（0:未读，1:已读）\n              }\n         ],\n         \"from\": 1,\n         \"last_page\": 1,  //上一页\n         \"next_page_url\": null,  //下一页\n         \"path\": \"http://localhost:6002/api/center/sys_message_list\",\n         \"per_page\": 15,   //每页数量\n         \"prev_page_url\": null,\n         \"to\": 1,\n         \"total\": 1   //消息总数\n      },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/sys_message_list",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/sys_message_list"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"失败\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "App/Http/Controllers/Api/Center/MessageController.php",
    "groupTitle": "用户中心",
    "name": "GetCenterSys_message_list"
  },
  {
    "type": "get",
    "url": "/center/sys_message_num",
    "title": "系统消息数量",
    "group": "用户中心",
    "description": "<p>系统消息总数量和未读数量</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>用户登陆认证token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "UserID",
            "description": "<p>用户ID</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>状态码（0:失败，1:成功, -1:需要重新登陆）</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "msg",
            "description": "<p>返回状态说明信息</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "data",
            "description": "<p>系统消息数据</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"获取系统消息数量成功\",\n     \"data\": {\n             \"total\": 17,  //用户的系统消息总数\n             \"read\": 15,  //用户已读消息数\n             \"unread\": 2,  //用户未读消息数\n         },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/sys_message_num",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/sys_message_num"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"失败\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "App/Http/Controllers/Api/Center/MessageController.php",
    "groupTitle": "用户中心",
    "name": "GetCenterSys_message_num"
  },
  {
    "type": "get",
    "url": "/center/sys_message_read",
    "title": "读取系统消息",
    "group": "用户中心",
    "description": "<p>读取系统消息</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>用户登陆认证token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "UserID",
            "description": "<p>用户ID</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "MessageID",
            "description": "<p>消息ID</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>状态码（0:失败，1:成功, -1:需要重新登陆）</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "msg",
            "description": "<p>返回状态说明信息</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "data",
            "description": "<p>系统消息数据</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功读取系统消息\",\n     \"data\": {\n         \"current_page\": 1,  //当前页\n         \"from\": 1,\n         \"last_page\": 1,  //上一页\n         \"next_page_url\": null,  //下一页\n         \"path\": \"http://localhost:6002/api/center/sys_message_list\",\n         \"per_page\": 15,   //每页数量\n         \"prev_page_url\": null,\n         \"to\": 1,\n         \"total\": 1   //消息总数\n      },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/sys_message_read",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/sys_message_read"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"失败\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "App/Http/Controllers/Api/Center/MessageController.php",
    "groupTitle": "用户中心",
    "name": "GetCenterSys_message_read"
  },
  {
    "type": "get",
    "url": "/center/user_info",
    "title": "用户信息",
    "group": "用户中心",
    "description": "<p>获取用户信息</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>用户登陆认证token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "UserID",
            "description": "<p>用户ID</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>状态码（0:失败，1:成功, -1:需要重新登陆）</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "msg",
            "description": "<p>返回状态说明信息</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "data",
            "description": "<p>用户信息数据</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\",\n     \"data\": {\n             \"User_ID\": 17,  //用户ID\n             \"User_No\": 600017,  //用户编号\n             \"User_Mobile\": \"13274507043\",  //用户手机号\n             \"User_NickName\": null,  //用户昵称\n             \"User_Level\": 0,  //用户等级\n             \"User_HeadImg\": \"http://localhost:6001//uploadfiles/9nj50igwex/image/5b87a19025.png\",  //用户头像\n             \"Is_Distribute\": 0,  //是否是分销商（0:普通账户，1:分销账户）\n             \"User_CreateTime\": \"2018-10-15 10:53:54\",  //注册时间\n         },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/user_info",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/user_info"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"失败\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "App/Http/Controllers/Api/Center/UserInfoController.php",
    "groupTitle": "用户中心",
    "name": "GetCenterUser_info"
  },
  {
    "type": "get",
    "url": "/check_sms",
    "title": "验证短信验证码",
    "group": "用户认证",
    "description": "<p>验证短信验证码</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mobile",
            "description": "<p>手机号</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "code",
            "description": "<p>验证码</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>1:成功，0:失败</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "msg",
            "description": "<p>状态说明</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"验证成功\"\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/check_sms",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/check_sms"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"验证失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "App/Http/Controllers/Api/AuthController.php",
    "groupTitle": "用户认证",
    "name": "GetCheck_sms"
  },
  {
    "type": "get",
    "url": "/logout",
    "title": "退出登陆",
    "group": "用户认证",
    "description": "<p>退出登陆</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "UserID",
            "description": "<p>用户ID</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>1:成功，0:失败</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "msg",
            "description": "<p>状态说明</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"退出登陆成功\"\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/logout",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/logout"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"退出登陆失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "App/Http/Controllers/Api/AuthController.php",
    "groupTitle": "用户认证",
    "name": "GetLogout"
  },
  {
    "type": "get",
    "url": "/send_sms",
    "title": "发送验证码",
    "group": "用户认证",
    "description": "<p>发送验证码</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mobile",
            "description": "<p>手机号</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>1:成功，0:失败</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "msg",
            "description": "<p>状态说明</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"发送成功\"\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/send_sms",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/send_sms"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"发送失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "App/Http/Controllers/Api/AuthController.php",
    "groupTitle": "用户认证",
    "name": "GetSend_sms"
  },
  {
    "type": "post",
    "url": "/login",
    "title": "用户登陆",
    "group": "用户认证",
    "description": "<p>用户登陆</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mobile",
            "description": "<p>手机号</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "password",
            "description": "<p>密码（普通登陆时为必填）</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "code",
            "description": "<p>验证码（验证码登陆时为必填）</p>"
          },
          {
            "group": "Parameter",
            "type": "Boolean",
            "optional": false,
            "field": "type",
            "description": "<p>登陆类型（0:验证码登陆，1:普通登陆）</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "OwnerID",
            "description": "<p>推荐人ID</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "history_url",
            "description": "<p>原浏览页url</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>状态码（0:失败，1:成功）</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "msg",
            "description": "<p>返回状态说明信息</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>用户认证TOKEN</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "url",
            "description": "<p>原浏览页路径</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"登陆成功\",\n     \"data\": {\n         \"token\": \"586dfb3fbb3d145e1707b21b6c2dbe35.MTU3MTEyNDc4Mg==.7908ef5dfffc38017a3260941272bcf5\",\n         \"expire_time\": \"2019-10-15 15:33:02\"\n     },\n     \"url\": \"\",\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/login",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/login"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"登陆失败\",\n     \"url\": \"\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "App/Http/Controllers/Api/AuthController.php",
    "groupTitle": "用户认证",
    "name": "PostLogin"
  },
  {
    "type": "post",
    "url": "/register",
    "title": "新用户注册",
    "group": "用户认证",
    "description": "<p>新用户注册</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mobile",
            "description": "<p>手机号</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "code",
            "description": "<p>验证码</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>密码</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password_confirmation",
            "description": "<p>确认密码</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "OwnerID",
            "description": "<p>推荐人ID</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>1:成功，0:失败</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "msg",
            "description": "<p>状态说明</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"注册成功\",\n     \"data\": {\n         \"token\": \"586dfb3fbb3d145e1707b21b6c2dbe35.MTU3MTEyNDc4Mg==.7908ef5dfffc38017a3260941272bcf5\",\n         \"expire_time\": \"2019-10-15 15:33:02\"\n     },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/register",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/register"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"注册失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "App/Http/Controllers/Api/AuthController.php",
    "groupTitle": "用户认证",
    "name": "PostRegister"
  }
] });
