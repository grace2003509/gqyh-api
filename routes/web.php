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
    $route->group(['prefix' => 'center', 'namespace' => 'Center',  'middleware' => ['check_auth']], function ($api) {
        //用户信息
        $api->get('/user_info', 'UserInfoController@user_info');
    });

});


