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
    "filename": "app/Http/Controllers/Api/TestController.php",
    "groupTitle": "test"
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
    "filename": "app/Http/Controllers/Api/Center/UserInfoController.php",
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
    "filename": "app/Http/Controllers/Api/Center/UserInfoController.php",
    "groupTitle": "用户中心",
    "name": "GetCenterSys_message_num"
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
    "filename": "app/Http/Controllers/Api/Center/UserInfoController.php",
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
  }
] });
