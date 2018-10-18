<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//接口路径
Route::group(['prefix' => 'api', 'namespace' => 'Api'], function ($route){
    //测试
    $route->get('/test/{id}', 'TestController@test');

    //登陆
    $route->post('/login', 'AuthController@login');
    $route->post('/register', 'AuthController@register');
    $route->get('/logout', 'AuthController@logout');
    $route->get('/send_sms', 'AuthController@sendSMS');//发送短信验证码
    $route->get('/check_sms', 'AuthController@checkSMS');//验证短信验证码

    //个人中心
    $route->group(['prefix' => 'center', 'middleware' => ['check_auth']], function ($api) {
        //用户信息
        $api->get('/user_info', 'UserInfoController@user_info');
        $api->post('/upload_headimg', 'UserInfoController@upload_headimg');
        //系统消息
        $api->get('/sys_message_num', 'MessageController@sys_message_num');
        $api->get('/sys_message_list', 'MessageController@sys_message_list');
        $api->get('/sys_message_read', 'MessageController@sys_message_read');
        //订单列表、详情
        $api->get('/order_list', 'OrderController@index');
        $api->get('/order_detail', 'OrderController@show');
        $api->get('/order_cancel', 'OrderController@cancel');
        $api->post('/order_commit', 'OrderController@commit');
        $api->get('/order_receive', 'OrderController@receive');
        //积分
        $api->get('/integral_info', 'IntegralController@integral_info');
        $api->get('/integral_record', 'IntegralController@integral_record');
        $api->post('/do_sign', 'IntegralController@do_sign');
        $api->post('/integral_largess', 'IntegralController@integral_largess');
        //余额
    });

});


