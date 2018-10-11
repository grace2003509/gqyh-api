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

//管理后台管理员认证
Route::group(['prefix' => 'admin', 'namespace' => 'Admin\Auth'], function ($route){
    //登入登出
    $route->get('/login', 'LoginController@showLoginForm')->name('login');
    $route->post('/login', 'LoginController@login')->name('admin.login');
    $route->get('/logout', 'LoginController@logout')->name('admin.user.logout');

    //忘记密码重置
    $route->get('/password/email', 'ForgotPasswordController@showLinkRequestForm')->name('password.email');
    $route->post('/password/email', 'ForgotPasswordController@sendResetLinkEmail');
    $route->get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    $route->post('/password/reset', 'ResetPasswordController@reset')->name('password.reset_save');
});

Route::group(['prefix' => 'admin/system', 'middleware' => ['auth', 'role:administrator']], function ($route) {
    //用户管理
    $route->resource('/user', 'Admin\System\UserController');
    //角色管理
    $route->resource('/role', 'Admin\System\RoleController');
    //角色分配
    $route->resource('/assignrole', 'Admin\System\AssignroleController');
    //权限分配
    $route->resource('/assignpermission', 'Admin\System\AssignpermissionController');
});

Route::group(['prefix' => 'admin', 'middleware' => 'auth', 'namespace' => 'Admin'], function () {

    Route::get('/home', 'HomeController@index')->name('admin.home');

    //个人资料修改
    Route::resource('/system/profile', 'System\ProfileController');

    //基础设置
    Route::group(['prefix' => 'base', 'namespace' => 'Base'], function ($route) {
        //系统设置
        $route->get('/sys_index', 'SysConfigController@index')->name('admin.base.sys_index');
        $route->post('/sys_edit', 'SysConfigController@edit')->name('admin.base.sys_edit');
        //客服设置
        $route->get('/kf_index', 'KfConfigController@index')->name('admin.base.kf_index');
        $route->post('/kf_edit', 'KfConfigController@edit')->name('admin.base.kf_edit');
        //支付设置
        $route->get('/pay_index', 'PayConfigController@index')->name('admin.base.pay_index');
        $route->post('/pay_edit', 'PayConfigController@edit')->name('admin.base.pay_edit');
        $route->get('/wechat_set', 'PayConfigController@wechat_set')->name('admin.base.wechat_set');
        $route->post('/wechat_edit', 'PayConfigController@wechat_edit')->name('admin.base.wechat_edit');
        //快递公司管理
        $route->get('/shipping', 'ShippingController@index')->name('admin.base.shipping');
        $route->post('/shipping_store', 'ShippingController@store')->name('admin.base.shipping_store');
        $route->post('/shipping_update/{id}', 'ShippingController@update')->name('admin.base.shipping_update');
        $route->get('/shipping_del/{id}', 'ShippingController@destroy')->name('admin.base.shipping_del');
        $route->post('/shipping_recovered', 'ShippingController@destroy')->name('admin.base.shipping_recovered');
        //自定义URL
        $route->get('/diy_url', 'DiyUrlController@index')->name('admin.base.diy_url');
        $route->post('/diy_url_store', 'DiyUrlController@store')->name('admin.base.diy_url_store');
        $route->post('/diy_url_update/{id}', 'DiyUrlController@update')->name('admin.base.diy_url_update');
        $route->get('/diy_url_del/{id}', 'DiyUrlController@del')->name('admin.base.diy_url_del');
        //系统url查询
        $route->get('/sys_url', 'SysUrlController@index')->name('admin.base.sys_url');

    });

    //商城管理
    Route::group(['prefix' => 'shop', 'namespace' => 'Shop'], function ($route) {
        //商城基本设置
        $route->get('/base_index', 'BaseConfigController@index')->name('admin.shop.base_index');
        $route->post('/base_update', 'BaseConfigController@update')->name('admin.shop.base_update');
        //积分设置
        $route->get('/integrate_index','IntegrateConfigController@index')->name('admin.shop.integrate_index');
        $route->post('/integrate_update','IntegrateConfigController@update')->name('admin.shop.integrate_update');
        //开关设置
        $route->get('/on_off_index', 'OnOffConfigController@index')->name('admin.shop.on_off_index');
        $route->get('/on_off_edit/{id}', 'OnOffConfigController@edit_status')->name('admin.shop.on_off_edit');
        $route->post('/on_off_store', 'OnOffConfigController@store')->name('admin.shop.on_off_store');
        $route->post('/on_off_update/{id}', 'OnOffConfigController@update')->name('admin.shop.on_off_update');
        $route->get('/on_off_del/{id}', 'OnOffConfigController@del')->name('admin.shop.on_off_del');
        //首页设置
        $route->get('/home_index', 'HomeConfigController@index')->name('admin.shop.home_index');
        $route->post('/home_update', 'HomeConfigController@update')->name('admin.shop.home_update');
        //底部菜单设置
        $route->get('/foot_menu_index', 'FootMenuController@index')->name('admin.shop.foot_menu_index');
        $route->post('/foot_menu_update', 'FootMenuController@update')->name('admin.shop.foot_menu_update');
        $route->get('/foot_menu_del', 'FootMenuController@del')->name('admin.shop.foot_menu_del');
    });

    //会员管理
    Route::group(['prefix' => 'member', 'namespace' => 'Member'], function ($route) {
        //会员列表
        $route->get('/user_list', 'UserController@index')->name('admin.member.user_list');
        $route->get('/user_output', 'UserController@output')->name('admin.member.user_output');
        $route->get('/user_del/{id}', 'UserController@del')->name('admin.member.user_del');
        $route->get('/all_user_del', 'UserController@all_del')->name('admin.member.all_user_del');
        $route->post('/user_update', 'UserController@update')->name('admin.member.user_update');
        $route->get('/user_capital/{id}', 'UserController@show')->name('admin.member.user_capital');
        //手动下单选择商品显示价格
        $route->get('/product_change/{id}', 'UserController@product_change')->name('admin.member.product_change');
        $route->post('/do_order', 'UserController@do_order')->name('admin.member.do_order');
        //消息管理
        $route->get('/message_index', 'MessageController@index')->name('admin.member.message_index');
        $route->get('/message_create', 'MessageController@create')->name('admin.member.message_create');
        $route->post('/message_store', 'MessageController@store')->name('admin.member.message_store');
        $route->get('/message_edit/{id}', 'MessageController@edit')->name('admin.member.message_edit');
        $route->post('/message_update/{id}', 'MessageController@update')->name('admin.member.message_update');
        $route->get('/message_del/{id}', 'MessageController@del')->name('admin.member.message_del');
    });

    //产品管理
    Route::group(['prefix' => 'product', 'namespace' => 'Product'], function ($route) {
        $route->get('/product_index', 'ProductController@index')->name('admin.product.product_index');
        //审核
        $route->get('/product_audit/{id}', 'ProductController@audit')->name('admin.product.product_audit');
        $route->post('/product_update/{id}', 'ProductController@update')->name('admin.product.product_update');
        //批量设置
        $route->post('/product_active', 'ProductController@active')->name('admin.product.product_active');
        //产品分销佣金设置详情
        $route->get('/product_commission', 'ProductController@commission')->name('admin.product.product_commission');
        //产品类别
        $route->get('/category_index', 'ProductCategoryController@index')->name('admin.product.category_index');
        $route->get('/category_create', 'ProductCategoryController@create')->name('admin.product.category_create');
        $route->post('/category_store', 'ProductCategoryController@store')->name('admin.product.category_store');
        $route->get('/category_edit/{id}', 'ProductCategoryController@edit')->name('admin.product.category_edit');
        $route->post('/category_update/{id}', 'ProductCategoryController@update')->name('admin.product.category_update');
        $route->get('/category_del/{id}', 'ProductCategoryController@del')->name('admin.product.category_del');
        //产品评论
        $route->get('/commit_index', 'ProductCommitController@index')->name('admin.product.commit_index');
        $route->get('/commit_del/{id}', 'ProductCommitController@del')->name('admin.product.commit_del');
        $route->get('/commit_audit/{id}', 'ProductCommitController@audit')->name('admin.product.commit_audit');
        //产品订单
        $route->get('/order_index', 'ProductOrderController@index')->name('admin.product.order_index');
        $route->get('/order_show/{id}', 'ProductOrderController@show')->name('admin.product.order_show');
        $route->post('/order_update/{id}', 'ProductOrderController@update')->name('admin.product.order_update');
        $route->get('/order_print/{ids}', 'ProductOrderController@order_print')->name('admin.product.order_print');
        //退款单
        $route->get('/back_index', 'ProductOrderBackController@index')->name('admin.product.back_index');
        $route->get('/back_show/{id}', 'ProductOrderBackController@show')->name('admin.product.back_show');
        $route->get('/back_update/{id}', 'ProductOrderBackController@update')->name('admin.product.back_update');
    });

    //分销管理
    Route::group(['prefix' => 'distribute', 'namespace' => 'Distribute'], function ($route) {
        //基础设置
        $route->get('/base_config_index', 'BaseConfigController@index')->name('admin.distribute.base_config_index');
        $route->get('/dis_level', 'BaseConfigController@get_dis_level')->name('admin.distribute.dis_level');
        $route->post('/base_config_update', 'BaseConfigController@update')->name('admin.distribute.base_config_update');
        //分销级别设置
        $route->get('/level', 'BaseConfigController@level')->name('admin.distribute.level');
        $route->get('/level_add', 'BaseConfigController@level_add')->name('admin.distribute.level_add');
        $route->post('/level_store', 'BaseConfigController@level_store')->name('admin.distribute.level_store');
        $route->get('/level_edit/{id}', 'BaseConfigController@level_edit')->name('admin.distribute.level_edit');
        $route->post('/level_update/{id}', 'BaseConfigController@level_update')->name('admin.distribute.level_update');
        $route->get('/level_del/{id}', 'BaseConfigController@level_del')->name('admin.distribute.level_del');
        //搜索获取商品信息
        $route->get('/get_product', 'BaseConfigController@get_product')->name('admin.distribute.get_product');
        //首页设置
        $route->get('/home_config_index', 'HomeConfigController@index')->name('admin.distribute.home_config_index');
        $route->post('/home_config_update', 'HomeConfigController@update')->name('admin.distribute.home_config_update');
        //提现设置
        $route->get('/withdraw_config_index', 'WithdrawConfigController@index')->name('admin.distribute.withdraw_config_index');
        $route->post('/withdraw_config_update', 'WithdrawConfigController@update')->name('admin.distribute.withdraw_config_update');
        //爵位设置
        $route->get('/protitle_config_index', 'ProtitleConfigController@index')->name('admin.distribute.protitle_config_index');
        $route->post('/protitle_config_update', 'ProtitleConfigController@update')->name('admin.distribute.protitle_config_update');
        //区域代理设置
        $route->get('/agent_config_index', 'AgentConfigController@index')->name('admin.distribute.agent_config_index');
        $route->post('/agent_config_update', 'AgentConfigController@update')->name('admin.distribute.agent_config_update');
        //其他设置
        $route->get('/other_config_index', 'OtherConfigController@index')->name('admin.distribute.other_config_index');
        $route->post('/other_config_update', 'OtherConfigController@update')->name('admin.distribute.other_config_update');
        //分销账号管理
        $route->get('/account_index', 'DisAccountController@index')->name('admin.distribute.account_index');
        $route->get('/account_update/{id}', 'DisAccountController@update')->name('admin.distribute.account_update');
        //下属
        $route->get('/account_posterity/{id}', 'DisAccountController@posterity')->name('admin.distribute.account_posterity');
        //获取保存区域代理信息
        $route->get('/get_dis_agent_area', 'DisAccountController@get_dis_agent_area')->name('admin.distribute.get_dis_agent_area');
        $route->post('/save_dis_agent_area', 'DisAccountController@save_dis_agent_area')->name('admin.distribute.save_dis_agent_area');
        //提现方法管理
        $route->get('/withdraw_method_index', 'WithdrawMethodController@index')->name('admin.distribute.withdraw_method_index');
        $route->get('/withdraw_method_create', 'WithdrawMethodController@create')->name('admin.distribute.withdraw_method_create');
        $route->post('/withdraw_method_store', 'WithdrawMethodController@store')->name('admin.distribute.withdraw_method_store');
        $route->get('/withdraw_method_edit/{id}', 'WithdrawMethodController@edit')->name('admin.distribute.withdraw_method_edit');
        $route->post('/withdraw_method_update/{id}', 'WithdrawMethodController@update')->name('admin.distribute.withdraw_method_update');
        $route->get('/withdraw_method_del/{id}', 'WithdrawMethodController@del')->name('admin.distribute.withdraw_method_del');
        //区域代理管理
        $route->get('/agent_index', 'AgentController@index')->name('admin.distribute.agent_index');
        $route->get('/agent_apply', 'AgentController@agent_apply')->name('admin.distribute.agent_apply');
        $route->get('/agent_apply_view/{id}', 'AgentController@agent_apply_view')->name('admin.distribute.agent_apply_view');
        $route->post('/agent_apply_audit/{id}', 'AgentController@agent_apply_audit')->name('admin.distribute.agent_apply_audit');
        //提现记录
        $route->get('/withdraw_index', 'WithdrawController@index')->name('admin.distribute.withdraw_index');
        $route->get('/withdraw_update/{id}', 'WithdrawController@update')->name('admin.distribute.withdraw_update');
        $route->get('/withdraw_output', 'WithdrawController@output')->name('admin.distribute.withdraw_output');
        //分销记录
        $route->get('/account_record', 'DisRecordController@account_record')->name('admin.distribute.account_record');//分销佣金记录
        $route->get('/point_record', 'DisRecordController@point_record')->name('admin.distribute.point_record');//重消奖记录
        $route->get('/protitle_record', 'DisRecordController@protitle_record')->name('admin.distribute.protitle_record');//团队奖记录
        $route->get('/agent_record', 'DisRecordController@agent_record')->name('admin.distribute.agent_record');//区域代理奖记录

    });

    //商家管理
    Route::group(['prefix' => 'business', 'namespace' => 'Business'], function ($route) {

        //商家设置
        $route->get('/home_describe', 'BizConfigController@home_describe')->name('admin.business.home_describe');
        $route->get('/enter_describe', 'BizConfigController@enter_describe')->name('admin.business.enter_describe');
        $route->get('/register_describe', 'BizConfigController@register_describe')->name('admin.business.register_describe');
        $route->get('/fee_describe', 'BizConfigController@fee_describe')->name('admin.business.fee_describe');
        $route->post('/describe_update', 'BizConfigController@describe_update')->name('admin.business.describe_update');
        //商家分组
        $route->get('/group_index', 'BizGroupController@index')->name('admin.business.group_index');
        $route->get('/group_create', 'BizGroupController@create')->name('admin.business.group_create');
        $route->post('/group_store', 'BizGroupController@store')->name('admin.business.group_store');
        $route->get('/group_edit/{id}', 'BizGroupController@edit')->name('admin.business.group_edit');
        $route->post('/group_update/{id}', 'BizGroupController@update')->name('admin.business.group_update');
        $route->get('/group_del/{id}', 'BizGroupController@del')->name('admin.business.group_del');
        //商家分类
        $route->get('/biz_category_index', 'BizCategoryController@index')->name('admin.business.biz_category_index');
        $route->get('/biz_category_create', 'BizCategoryController@create')->name('admin.business.biz_category_create');
        $route->post('/biz_category_store', 'BizCategoryController@store')->name('admin.business.biz_category_store');
        $route->get('/biz_category_edit/{id}', 'BizCategoryController@edit')->name('admin.business.biz_category_edit');
        $route->post('/biz_category_update/{id}', 'BizCategoryController@update')->name('admin.business.biz_category_update');
        $route->get('/biz_category_del/{id}', 'BizCategoryController@del')->name('admin.business.biz_category_del');
        //联盟商家列表
        $route->get('/biz_union_index', 'BizUnionController@index')->name('admin.business.biz_union_index');
        $route->get('/biz_union_create', 'BizUnionController@create')->name('admin.business.biz_union_create');
        $route->post('/biz_union_store', 'BizUnionController@store')->name('admin.business.biz_union_store');
        $route->get('/biz_union_edit/{id}', 'BizUnionController@edit')->name('admin.business.biz_union_edit');
        $route->post('/biz_union_update/{id}', 'BizUnionController@update')->name('admin.business.biz_union_update');
        $route->get('/biz_union_del/{id}', 'BizUnionController@del')->name('admin.business.biz_union_del');
        $route->get('/get_region', 'BizUnionController@get_region')->name('admin.business.get_region');
        //普通商家列表
        $route->get('/biz_index', 'BizController@index')->name('admin.business.biz_index');
        $route->get('/biz_create', 'BizController@create')->name('admin.business.biz_create');
        $route->post('/biz_store', 'BizController@store')->name('admin.business.biz_store');
        $route->get('/biz_edit/{id}', 'BizController@edit')->name('admin.business.biz_edit');
        $route->post('/biz_update/{id}', 'BizController@update')->name('admin.business.biz_update');
        $route->get('/biz_del/{id}', 'BizController@del')->name('admin.business.biz_del');
        //入驻资质审核
        $route->get('/biz_apply_index', 'BizApplyController@index')->name('admin.business.biz_apply_index');
        $route->get('/biz_apply_show/{id}', 'BizApplyController@show')->name('admin.business.biz_apply_show');
        $route->get('/biz_apply_del/{id}', 'BizApplyController@del')->name('admin.business.biz_apply_del');
        //支付记录
        $route->get('/enter_pay', 'BizPayController@enter_pay')->name('admin.business.enter_pay');
        $route->get('/charge_pay', 'BizPayController@charge_pay')->name('admin.business.charge_pay');
        $route->get('/bail_back', 'BizPayController@bail_back')->name('admin.business.bail_back');
        $route->get('/bail_show/{id}', 'BizPayController@bail_show')->name('admin.business.bail_show');
    });

    //活动管理
    Route::group(['prefix' => 'active', 'namespace' => 'Active'], function ($route) {
        $route->get('/index', 'ActiveController@index')->name('admin.active.index');
        $route->get('/create', 'ActiveController@create')->name('admin.active.create');
        $route->post('/store', 'ActiveController@store')->name('admin.active.store');
        $route->get('/edit/{id}', 'ActiveController@edit')->name('admin.active.edit');
        $route->post('/update/{id}', 'ActiveController@update')->name('admin.active.update');
        $route->get('/del/{id}', 'ActiveController@del')->name('admin.active.del');
        //商家活动列表
        $route->get('/biz_active/{id}', 'ActiveController@biz_actives')->name('admin.active.biz_active');
    });


    //我的微信
    Route::group(['perfix' => 'wechat', 'namespace' => 'Wechat'], function ($route) {
        //微信接口配置
        $route->get('/api_index', 'ApiConfigController@index')->name('admin.wechat.api_index');
        $route->post('/api_edit', 'ApiConfigController@edit')->name('admin.wechat.api_edit');
        //首次关注设置
        $route->get('/reply_index', 'ReplyConfigController@index')->name('admin.wechat.reply_index');
        $route->post('/reply_edit', 'ReplyConfigController@edit')->name('admin.wechat.reply_edit');
        //自定义菜单设置
        $route->get('/menu_index', 'DiyMenuConfigController@index')->name('admin.wechat.menu_index');
        $route->get('/menu_edit/{id}', 'DiyMenuConfigController@edit')->name('admin.wechat.menu_edit');
        $route->post('/menu_update/{id}', 'DiyMenuConfigController@update')->name('admin.wechat.menu_update');
        $route->get('/menu_add', 'DiyMenuConfigController@add')->name('admin.wechat.menu_add');
        $route->post('/menu_store', 'DiyMenuConfigController@store')->name('admin.wechat.menu_store');
        $route->get('/menu_del/{id}', 'DiyMenuConfigController@del')->name('admin.wechat.menu_del');
        $route->get('/menu_push', 'DiyMenuConfigController@push')->name('admin.wechat.menu_push');
        $route->get('/menu_cancel', 'DiyMenuConfigController@cancel')->name('admin.wechat.menu_cancel');
        //关键词设置
        $route->get('/keyword_index', 'KeyWordController@index')->name('admin.wechat.keyword_index');
        $route->get('/keyword_edit/{id}', 'KeyWordController@edit')->name('admin.wechat.keyword_edit');
        $route->post('/keyword_update/{id}', 'KeyWordController@update')->name('admin.wechat.keyword_update');
        $route->get('/keyword_add', 'KeyWordController@add')->name('admin.wechat.keyword_add');
        $route->post('/keyword_store', 'KeyWordController@store')->name('admin.wechat.keyword_store');
        $route->get('/keyword_del/{id}', 'KeyWordController@del')->name('admin.wechat.keyword_del');
        //图文消息管理
        $route->get('/material_index', 'MaterialController@index')->name('admin.wechat.material_index');
        $route->get('/material_edit/{id}', 'MaterialController@edit')->name('admin.wechat.material_edit');
        $route->post('/material_update/{id}', 'MaterialController@update')->name('admin.wechat.material_update');
        $route->get('/material_add', 'MaterialController@add')->name('admin.wechat.material_add');
        $route->get('/material_madd', 'MaterialController@madd')->name('admin.wechat.material_madd');
        $route->post('/material_store', 'MaterialController@store')->name('admin.wechat.material_store');
        $route->get('/material_del/{id}', 'MaterialController@del')->name('admin.wechat.material_del');
    });

    //财务统计
    Route::group(['prefix' => 'statistics', 'namespace' => 'Statistics'], function ($route) {
        //销售记录
        $route->get('/sale_record', 'SaleRecordController@index')->name('admin.statistics.sale_record');
        //自动结算配置
        $route->get('/balance_index', 'BalanceConfigController@index')->name('admin.statistics.balance_index');
        $route->post('/balance_update', 'BalanceConfigController@update')->name('admin.statistics.balance_update');
        //生成报告
        $route->get('/report_index', 'CreateReportController@index')->name('admin.statistics.report_index');
        $route->get('/report_download', 'CreateReportController@download')->name('admin.statistics.report_download');
        //付款单
        $route->get('/bill_index', 'PaymentBillController@index')->name('admin.statistics.bill_index');
        $route->get('/bill_create', 'PaymentBillController@create')->name('admin.statistics.bill_create');
        $route->get('/bill_show/{id}', 'PaymentBillController@show')->name('admin.statistics.bill_show');
        $route->post('/bill_store', 'PaymentBillController@store')->name('admin.statistics.bill_store');
        $route->get('/bill_del/{id}', 'PaymentBillController@del')->name('admin.statistics.bill_del');
        $route->get('/bill_okey/{id}', 'PaymentBillController@okey')->name('admin.statistics.bill_okey');

    });

    //微官网
    Route::group(['prefix' => 'web', 'namespace' => 'Web'], function ($route) {
        //风格设置
        $route->get('/skin_config', 'SkinConfigController@index')->name('admin.web.skin_config');
        //首页设置
        $route->get('/home_config', 'HomeConfigController@index')->name('admin.web.home_config');
        $route->post('/home_config_update', 'HomeConfigController@update')->name('admin.web.home_config_update');
        //栏目管理
        $route->get('/column_index', 'ColumnController@index')->name('admin.web.column_index');
        $route->get('/column_create', 'ColumnController@create')->name('admin.web.column_create');
        $route->post('/column_store', 'ColumnController@store')->name('admin.web.column_store');
        $route->get('/column_edit/{id}', 'ColumnController@edit')->name('admin.web.column_edit');
        $route->post('/column_update/{id}', 'ColumnController@update')->name('admin.web.column_update');
        $route->get('/column_del/{id}', 'ColumnController@del')->name('admin.web.column_del');
        //内容管理
        $route->get('/article_index', 'ArticleController@index')->name('admin.web.article_index');
        $route->get('/article_create', 'ArticleController@create')->name('admin.web.article_create');
        $route->post('/article_store', 'ArticleController@store')->name('admin.web.article_store');
        $route->get('/article_edit/{id}', 'ArticleController@edit')->name('admin.web.article_edit');
        $route->post('/article_update/{id}', 'ArticleController@update')->name('admin.web.article_update');
        $route->get('/article_del/{id}', 'ArticleController@del')->name('admin.web.article_del');
        //一键导航
        $route->get('/lbs_index', 'LbsController@lbs_index')->name('admin.web.lbs_index');
        $route->post('/lbs_save', 'LbsController@lbs_save')->name('admin.web.lbs_save');
    });

    //上传文件
    Route::post('/upload_json', 'UploadController@upload_json')->name('admin.upload_json');
    Route::get('/file_manager_json', 'UploadController@file_manager_json')->name('admin.file_manager_json');

});


