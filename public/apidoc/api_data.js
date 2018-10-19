define({ "api": [
  {
    "type": "get",
    "url": "/center/coupon_list",
    "title": "优惠券列表",
    "group": "优惠券",
    "description": "<p>获取优惠券列表</p>",
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
            "description": "<p>当前页数</p>"
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\",\n     \"data\": {\n         \"current_page\": 1,  //当前页\n         \"data\": [\n             {\n                 \"Coupon_ID\": 1,   //优惠券ID\n                 \"Coupon_Keywords\": null,\n                 \"Coupon_Title\": null,\n                 \"Coupon_UserLevel\": 0,\n                 \"Coupon_UsedTimes\": 0,   //优惠券使用次数（-1:不限次数）\n                 \"Coupon_CreateTime\": \"1533035858\",\n                 \"Coupon_UseArea\": 0,  //优惠券使用范围\n                 \"Coupon_UseType\": 0,   //优惠券类型(0:折扣，1:现金)\n                 \"Coupon_Condition\": 0,   //一次购满多少可用\n                 \"Coupon_Discount\": \"0.00\",  //优惠券折扣\n                 \"Coupon_Cash\": 0,  //优惠券可抵现金\n                 \"Coupon_StartTime\": \"2018-07-31 19:16:44\",  //开始时间\n                 \"Coupon_EndTime\": \"2018-08-07 19:16:44\",   //结束时间\n                 \"Biz_ID\": 2,   //店铺ID\n                 \"Coupon_Subject\": \"奶粉类专用\",  //标题\n                 \"Coupon_PhotoPath\": \"\",  //优惠券图片\n                 \"Coupon_Description\": \"\"   //信息描述\n              }\n          ],\n         \"from\": 1,\n         \"last_page\": 1,  //上一页\n         \"next_page_url\": null,  //下一页\n         \"path\": \"http://localhost:6002/api/center/coupon_list\",\n         \"per_page\": 15,   //每页数量\n         \"prev_page_url\": null,\n         \"to\": 1,\n         \"total\": 1   //消息总数\n         },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/coupon_list",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/coupon_list"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/CouponController.php",
    "groupTitle": "优惠券",
    "name": "GetCenterCoupon_list"
  },
  {
    "type": "get",
    "url": "/center/lose_coupon",
    "title": "失效、过期优惠券",
    "group": "优惠券",
    "description": "<p>获取用户失效、过期优惠券列表</p>",
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
            "description": "<p>当前页数</p>"
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\",\n     \"data\": {\n         \"current_page\": 1,  //当前页\n         \"data\": [\n             {\n                 \"User_ID\": 1, //用户ID\n                 \"Record_ID\": 3,  //记录ID\n                 \"Coupon_ID\": 1,   //优惠券ID\n                 \"Coupon_UsedTimes\": 0,   //优惠券使用次数（-1:不限次数）\n                 \"Record_CreateTime\": \"2018-07-31 19:17:38\",\n                 \"Coupon_UseArea\": 0,  //优惠券使用范围\n                 \"Coupon_UseType\": 0,   //优惠券类型(0:折扣，1:现金)\n                 \"Coupon_Condition\": 0,   //一次购满多少可用\n                 \"Coupon_Discount\": \"0.00\",  //优惠券折扣\n                 \"Coupon_Cash\": 0,  //优惠券可抵现金\n                 \"Coupon_StartTime\": \"2018-07-31 19:16:44\",  //开始时间\n                 \"Coupon_EndTime\": \"2018-08-07 19:16:44\",   //结束时间\n                 \"Biz_ID\": 2,   //店铺ID\n                 \"Coupon_Subject\": \"奶粉类专用\",  //标题\n                 \"Coupon_PhotoPath\": \"\",  //优惠券图片\n                 \"Coupon_Description\": \"\"   //信息描述\n              }\n          ],\n         \"from\": 1,\n         \"last_page\": 1,  //上一页\n         \"next_page_url\": null,  //下一页\n         \"path\": \"http://localhost:6002/api/center/lose_coupon\",\n         \"per_page\": 15,   //每页数量\n         \"prev_page_url\": null,\n         \"to\": 1,\n         \"total\": 1   //消息总数\n         },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/lose_coupon",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/lose_coupon"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/CouponController.php",
    "groupTitle": "优惠券",
    "name": "GetCenterLose_coupon"
  },
  {
    "type": "get",
    "url": "/center/my_coupon",
    "title": "我的优惠券",
    "group": "优惠券",
    "description": "<p>获取用户优惠券列表</p>",
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
            "description": "<p>当前页数</p>"
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\",\n     \"data\": {\n         \"current_page\": 1,  //当前页\n         \"data\": [\n             {\n                 \"User_ID\": 1, //用户ID\n                 \"Record_ID\": 3,  //记录ID\n                 \"Coupon_ID\": 1,   //优惠券ID\n                 \"Coupon_UsedTimes\": 0,   //优惠券使用次数（-1:不限次数）\n                 \"Record_CreateTime\": \"2018-07-31 19:17:38\",\n                 \"Coupon_UseArea\": 0,  //优惠券使用范围\n                 \"Coupon_UseType\": 0,   //优惠券类型(0:折扣，1:现金)\n                 \"Coupon_Condition\": 0,   //一次购满多少可用\n                 \"Coupon_Discount\": \"0.00\",  //优惠券折扣\n                 \"Coupon_Cash\": 0,  //优惠券可抵现金\n                 \"Coupon_StartTime\": \"2018-07-31 19:16:44\",  //开始时间\n                 \"Coupon_EndTime\": \"2018-08-07 19:16:44\",   //结束时间\n                 \"Biz_ID\": 2,   //店铺ID\n                 \"Coupon_Subject\": \"奶粉类专用\",  //标题\n                 \"Coupon_PhotoPath\": \"\",  //优惠券图片\n                 \"Coupon_Description\": \"\"   //信息描述\n              }\n          ],\n         \"from\": 1,\n         \"last_page\": 1,  //上一页\n         \"next_page_url\": null,  //下一页\n         \"path\": \"http://localhost:6002/api/center/my_coupon\",\n         \"per_page\": 15,   //每页数量\n         \"prev_page_url\": null,\n         \"to\": 1,\n         \"total\": 1   //消息总数\n         },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/my_coupon",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/my_coupon"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/CouponController.php",
    "groupTitle": "优惠券",
    "name": "GetCenterMy_coupon"
  },
  {
    "type": "post",
    "url": "/center/get_coupon",
    "title": "领取优惠券",
    "group": "优惠券",
    "description": "<p>领取优惠券</p>",
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
            "field": "CouponID",
            "description": "<p>优惠券ID</p>"
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\",\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/get_coupon",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/get_coupon"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/CouponController.php",
    "groupTitle": "优惠券",
    "name": "PostCenterGet_coupon"
  },
  {
    "type": "get",
    "url": "/center/charge_record",
    "title": "充值记录",
    "group": "余额",
    "description": "<p>获取用户充值记录</p>",
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
            "description": "<p>当前页数</p>"
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\",\n     \"data\": {\n         \"current_page\": 1,  //当前页\n         \"data\": [\n             {\n                 \"Item_ID\": 23,  //记录ID\n                 \"User_ID\": 1,   //用户ID\n                 \"Amount\": \"100.00\",\n                 \"Total\": \"3600.00\",\n                 \"Operator\": \"余额支付充值积分 +100\",  //记录描述\n                 \"Status\": 2,\n                 \"CreateTime\": \"2018/08/14\"   //日期\n              }\n          ],\n         \"from\": 1,\n         \"last_page\": 1,  //上一页\n         \"next_page_url\": null,  //下一页\n         \"path\": \"http://localhost:6002/api/center/charge_record\",\n         \"per_page\": 15,   //每页数量\n         \"prev_page_url\": null,\n         \"to\": 1,\n         \"total\": 1   //消息总数\n         },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/charge_record",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/charge_record"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/MoneyController.php",
    "groupTitle": "余额",
    "name": "GetCenterCharge_record"
  },
  {
    "type": "get",
    "url": "/center/money_record",
    "title": "资金流水",
    "group": "余额",
    "description": "<p>获取用户资金流水列表</p>",
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
            "description": "<p>当前页数</p>"
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\",\n     \"data\": {\n         \"current_page\": 1,  //当前页\n         \"data\": [\n             {\n                 \"Item_ID\": 23,  //记录ID\n                 \"User_ID\": 1,   //用户ID\n                 \"Amount\": \"-338.00\",\n                 \"Total\": \"3600.00\",\n                 \"Note\": \"商城购买支出 -338.00 (订单号:11)\",  //记录描述\n                 \"Type\": 0,\n                 \"CreateTime\": \"2018/08/14\"   //日期\n              }\n          ],\n         \"from\": 1,\n         \"last_page\": 1,  //上一页\n         \"next_page_url\": null,  //下一页\n         \"path\": \"http://localhost:6002/api/center/money_record\",\n         \"per_page\": 15,   //每页数量\n         \"prev_page_url\": null,\n         \"to\": 1,\n         \"total\": 1   //消息总数\n         },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/money_record",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/money_record"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/MoneyController.php",
    "groupTitle": "余额",
    "name": "GetCenterMoney_record"
  },
  {
    "type": "get",
    "url": "/center/address_list",
    "title": "地址列表",
    "group": "地址管理",
    "description": "<p>获取用户收货地址列表</p>",
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\",\n     \"data\": [\n         {\n             \"User_ID\": \"1\",  //用户ID\n             \"Address_ID\": 1,  //地址ID\n             \"Address_Name\": \"1\",  //收货人名称\n             \"Address_Mobile\": \"15501691825\",   //收货人电话\n             \"Address_Detailed\": \"1\",  //详细地址\n             \"Address_Is_Default\": 1,   //是否是默认地址（1:是，0:否）\n             \"Address_TrueName\": null,   //收货人真实姓名（进口奶粉需要）\n             \"Address_Certificate\": null,  //收货人真实身份证号（进品奶粉需要）\n             \"Province\": \"江苏\",  //省\n             \"City\": \"苏州市\",   //市\n             \"Area\": \"吴中区\"   //区\n         }\n     ]\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/address_list",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/address_list"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/AddressController.php",
    "groupTitle": "地址管理",
    "name": "GetCenterAddress_list"
  },
  {
    "type": "post",
    "url": "/center/address_del",
    "title": "删除地址",
    "group": "地址管理",
    "description": "<p>删除收货地址</p>",
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
            "field": "AddressID",
            "description": "<p>地址ID</p>"
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"操作成功\",\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/address_del",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/address_del"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/AddressController.php",
    "groupTitle": "地址管理",
    "name": "PostCenterAddress_del"
  },
  {
    "type": "post",
    "url": "/center/address_edit",
    "title": "编辑、添加地址",
    "group": "地址管理",
    "description": "<p>编辑、添加收货地址</p>",
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
            "field": "AddressID",
            "description": "<p>地址ID</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "Name",
            "description": "<p>收货人</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "Mobile",
            "description": "<p>收货人电话</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "Province",
            "description": "<p>省</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "City",
            "description": "<p>市</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "Area",
            "description": "<p>区</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "Detailed",
            "description": "<p>详细地址</p>"
          },
          {
            "group": "Parameter",
            "type": "Boolean",
            "optional": false,
            "field": "default",
            "description": "<p>是否是默认地址（1:是，0:否）</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "TrueName",
            "description": "<p>收货人真实姓名</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "Certificate",
            "description": "<p>收货人真实身份证号</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "url",
            "description": "<p>原访问页面路径</p>"
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"操作成功\",\n     \"url\": \"/\",\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/address_edit",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/address_edit"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/AddressController.php",
    "groupTitle": "地址管理",
    "name": "PostCenterAddress_edit"
  },
  {
    "type": "get",
    "url": "/center/sys_message_list",
    "title": "系统消息列表",
    "group": "消息中心",
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
    "filename": "app/Http/Controllers/Api/MessageController.php",
    "groupTitle": "消息中心",
    "name": "GetCenterSys_message_list"
  },
  {
    "type": "get",
    "url": "/center/sys_message_num",
    "title": "系统消息数量",
    "group": "消息中心",
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
    "filename": "app/Http/Controllers/Api/MessageController.php",
    "groupTitle": "消息中心",
    "name": "GetCenterSys_message_num"
  },
  {
    "type": "get",
    "url": "/center/sys_message_read",
    "title": "读取系统消息",
    "group": "消息中心",
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
    "filename": "app/Http/Controllers/Api/MessageController.php",
    "groupTitle": "消息中心",
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\",\n     \"data\": {\n             \"User_ID\": 17,  //用户ID\n             \"User_No\": 600017,  //用户编号\n             \"User_Mobile\": \"13274507043\",  //用户手机号\n             \"User_NickName\": null,  //用户昵称\n             \"User_Integral\": 20,  //用户当前积分\n             \"User_Money\": 1314,  //用户当前余额\n             \"User_Level\": 0,  //用户等级\n             \"User_HeadImg\": \"http://localhost:6001//uploadfiles/9nj50igwex/image/5b87a19025.png\",  //用户头像\n             \"Is_Distribute\": 0,  //是否是分销商（0:普通账户，1:分销账户）\n             \"User_CreateTime\": \"2018-10-15 10:53:54\",  //注册时间\n         },\n}",
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
    "filename": "app/Http/Controllers/Api/UserInfoController.php",
    "groupTitle": "用户中心",
    "name": "GetCenterUser_info"
  },
  {
    "type": "post",
    "url": "/center/upload_headimg",
    "title": "上传用户头像",
    "group": "用户中心",
    "description": "<p>上传用户头像</p>",
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
            "type": "String",
            "optional": false,
            "field": "up_head",
            "description": "<p>上传图片元素名称</p>"
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
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"头像上传成功\",\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/upload_headimg",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/upload_headimg"
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
    "filename": "app/Http/Controllers/Api/UserInfoController.php",
    "groupTitle": "用户中心",
    "name": "PostCenterUpload_headimg"
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
    "filename": "app/Http/Controllers/Api/AuthController.php",
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
    "filename": "app/Http/Controllers/Api/AuthController.php",
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
    "filename": "app/Http/Controllers/Api/AuthController.php",
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
    "filename": "app/Http/Controllers/Api/AuthController.php",
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
    "filename": "app/Http/Controllers/Api/AuthController.php",
    "groupTitle": "用户认证",
    "name": "PostRegister"
  },
  {
    "type": "get",
    "url": "/center/integral_info",
    "title": "积分信息",
    "group": "积分",
    "description": "<p>获取用户积分信息</p>",
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\",\n     \"data\": {\n                 \"is_sign\": 1,   //是否开启签到功能\n                 \"today_sign\": 0,   //今天是否已签到\n                 \"sign_num\": 0,   //签到总次数\n                 \"integral\": 20   //当前积分数\n         },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/integral_info",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/integral_info"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/IntegralController.php",
    "groupTitle": "积分",
    "name": "GetCenterIntegral_info"
  },
  {
    "type": "get",
    "url": "/center/integral_rate",
    "title": "积分充值比例",
    "group": "积分",
    "description": "<p>获取积分充值比例设置信息</p>",
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\",\n     \"data\": 5   //积分充值比例1:5，即1元=5积分\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/integral_rate",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/integral_rate"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/IntegralController.php",
    "groupTitle": "积分",
    "name": "GetCenterIntegral_rate"
  },
  {
    "type": "get",
    "url": "/center/integral_record",
    "title": "积分明细",
    "group": "积分",
    "description": "<p>获取用户积分明细</p>",
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
            "description": "<p>当前页数</p>"
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\",\n     \"data\": {\n         \"current_page\": 1,  //当前页\n         \"data\": [\n             {\n                 \"User_ID\": \"17\",   //用户ID\n                 \"Record_ID\": 47,   //明细ID\n                 \"Record_Integral\": 20,   //积分数\n                 \"Operator_UserName\": \"\",   //后台手动修改积分操作员名称\n                 \"Record_Type\": 7,   //积分类型\n                 \"Record_Description\": \"注册得积分\",\n                 \"Record_CreateTime\": \"2018-10-15 10:53:54\",   //记录时间\n              }\n          ],\n         \"from\": 1,\n         \"last_page\": 1,  //上一页\n         \"next_page_url\": null,  //下一页\n         \"path\": \"http://localhost:6002/api/center/integral_record\",\n         \"per_page\": 15,   //每页数量\n         \"prev_page_url\": null,\n         \"to\": 1,\n         \"total\": 1   //消息总数\n         },\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/integral_record",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/integral_record"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/IntegralController.php",
    "groupTitle": "积分",
    "name": "GetCenterIntegral_record"
  },
  {
    "type": "post",
    "url": "/center/do_sign",
    "title": "签到",
    "group": "积分",
    "description": "<p>用户签到获取积分</p>",
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\"\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/do_sign",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/do_sign"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/IntegralController.php",
    "groupTitle": "积分",
    "name": "PostCenterDo_sign"
  },
  {
    "type": "post",
    "url": "/center/integral_largess",
    "title": "积分转赠",
    "group": "积分",
    "description": "<p>积分转赠</p>",
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
            "type": "String",
            "optional": false,
            "field": "to_mobile",
            "description": "<p>对方手机号</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "Amount",
            "description": "<p>转赠积分数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "pay_password",
            "description": "<p>用户支付密码</p>"
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"成功\"\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/integral_largess",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/integral_largess"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"缺少必要的参数UserID\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/IntegralController.php",
    "groupTitle": "积分",
    "name": "PostCenterIntegral_largess"
  },
  {
    "type": "get",
    "url": "/center/order_detail",
    "title": "订单详情",
    "group": "订单中心",
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
    "filename": "app/Http/Controllers/Api/OrderController.php",
    "groupTitle": "订单中心",
    "name": "GetCenterOrder_detail"
  },
  {
    "type": "get",
    "url": "/center/order_list",
    "title": "订单列表",
    "group": "订单中心",
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
            "optional": true,
            "field": "status",
            "description": "<p>订单列表（0:待确认，1:待付款，2:已付款，3:已发货，4:已完成）</p>"
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"获取订单列表成功\",\n     \"data\": {\n         \"current_page\": 1,  //当前页\n         \"data\": [\n             {\n                 \"User_ID\": \"4\",  //用户ID\n                 \"Order_ID\": 4,  //订单ID\n                 \"Order_Status\": 4, //订单状态（0:待确认，1:待付款，2:已付款，3:已发货，4:已完成）\n                 \"Order_CartList\": {    //订单商品列表\n                          \"11\": [   //商品ID\n                             {\n                                 \"ProductsName\": \"宝贝营养品\",   //商品名称\n                                 \"ImgPath\": \"/uploadfiles/biz/2/image/5b188c7412.jpg\",   //商品图片\n                                 \"ProductsPriceX\": \"38.00\",   //商品现价\n                                 \"ProductsPriceY\": \"188.00\",\n                                 \"Products_PriceS\": \"0.00\",\n                                 \"ProductsPriceA\": 0,\n                                 \"ProductsPriceAmax\": 0,\n                                 \"Products_PayCoin\": \"0\",   //商品所需积分\n                                 \"Products_Integration\": \"0\",\n                                 \"user_curagio\": 0,\n                                 \"Productsattrstrval\": \"\",\n                                 \"Productsattrkeystrval\": [],\n                                 \"ProductsWeight\": \"2.00\",\n                                 \"Products_IsPaysBalance\": \"1\",\n                                 \"Products_Shipping\": null,\n                                 \"Products_Business\": null,\n                                 \"Shipping_Free_Company\": \"0\",\n                                 \"IsShippingFree\": \"0\",\n                                 \"OwnerID\": \"3\",\n                                 \"ProductsIsShipping\": \"0\",\n                                 \"Qty\": 1,   //商品数量\n                                 \"Products_FinanceType\": \"0\",\n                                 \"Products_FinanceRate\": \"100.00\",\n                                 \"Biz_FinanceType\": 0,\n                                 \"Biz_FinanceRate\": \"100.00\",\n                                 \"Property\": [],   //商品属性（shu_pricesimp:属性价格，shu_value:商品属性值）\n                                 \"platForm_Income_Reward\": \"100\",\n                                 \"area_Proxy_Reward\": \"0\",\n                                 \"web_prie_shop\": 38,\n                                 \"ProductsProfit\": 38\n                             }\n                         ]\n                     },\n                 \"Order_TotalPrice\": \"1352.00\",   //订单总价\n                 \"Order_Type\": \"shop\",   //订单类型\n                 \"order_coin\": 0,  //订单使用积分\n                 \"Order_ShippingID\": \"0\",   //快递单号\n                 \"Is_Commit\": 0,  //是否已评论（0:未评论，1:已评论）\n                 \"Order_No\": \"201807314\",   //订单编号\n                 \"shipping_trace\": \"http://m.kuaidi100.com/index_all.html?type=&postid=&callbackurl=http://localhost:6002/index.php?UserID=4&status=0\",  //快递100查询接口\n                 \"Shipping_Express\": \"\",   //快递公司名称\n              }\n         ],\n         \"from\": 1,\n         \"last_page\": 1,  //上一页\n         \"next_page_url\": null,  //下一页\n         \"path\": \"http://localhost:6002/api/center/order_list\",\n         \"per_page\": 15,   //每页数量\n         \"prev_page_url\": null,\n         \"to\": 1,\n         \"total\": 1   //消息总数\n      },\n}",
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
    "filename": "app/Http/Controllers/Api/OrderController.php",
    "groupTitle": "订单中心",
    "name": "GetCenterOrder_list"
  },
  {
    "type": "post",
    "url": "/center/order_cancel",
    "title": "取消订单",
    "group": "订单中心",
    "description": "<p>取消订单</p>",
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"取消订单成功\",\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/order_cancel",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/order_cancel"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"取消订单失败\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/OrderController.php",
    "groupTitle": "订单中心",
    "name": "PostCenterOrder_cancel"
  },
  {
    "type": "post",
    "url": "/center/order_commit",
    "title": "提交评论",
    "group": "订单中心",
    "description": "<p>提交评论</p>",
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
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "Score",
            "description": "<p>卖家打分（1，2，3，4，5）</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "Note",
            "description": "<p>评论内容</p>"
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"提交评论成功\",\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/order_commit",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/order_commit"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"提交评论失败\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/OrderController.php",
    "groupTitle": "订单中心",
    "name": "PostCenterOrder_commit"
  },
  {
    "type": "post",
    "url": "/center/order_receive",
    "title": "确认收货",
    "group": "订单中心",
    "description": "<p>确认收货</p>",
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
          "content": "{\n     \"status\": \"1\",\n     \"msg\": \"确认收货成功\",\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:6002/api/center/order_receive",
        "type": "curl"
      }
    ],
    "sampleRequest": [
      {
        "url": "/api/center/order_receive"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"status\": \"0\",\n     \"msg\": \"确认收货失败\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/OrderController.php",
    "groupTitle": "订单中心",
    "name": "PostCenterOrder_receive"
  }
] });
