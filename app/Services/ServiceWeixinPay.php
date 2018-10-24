<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/28
 * Time: 14:06
 */

namespace App\Services;

use App\Models\Biz;
use App\Models\Dis_Account;
use App\Models\Dis_Account_Record;
use App\Models\Dis_Agent_Area;
use App\Models\Dis_Agent_Record;
use App\Models\Dis_Config;
use App\Models\Dis_Level;
use App\Models\Dis_Point_Record;
use App\Models\Dis_Record;
use App\Models\Member;
use App\Models\User_Message;
use App\Models\UserIntegralRecord;
use App\Models\UserOrder;
use App\Models\Setting;
use App\Models\ShopConfig;
use App\Models\ShopProduct;
use App\Models\UsersConfig;
use App\Models\UsersPayConfig;
use Illuminate\Support\Facades\DB;

class ServiceWeixinPay
{
    public function config($url)
    {
        $upc_obj = new UsersPayConfig();
        $u_obj = new UsersConfig();

        $rsPay = $upc_obj->find(USERSID);
        $rsUsers = $u_obj->select('Users_WechatAppId', 'Users_WechatAppSecret')->find(USERSID);

        if ($rsPay["PaymentWxpayEnabled"] == 0 || empty($rsPay["PaymentWxpayPartnerId"]) || empty($rsPay["PaymentWxpayPartnerKey"]) || empty($rsUsers["Users_WechatAppId"]) || empty($rsUsers["Users_WechatAppSecret"])) {
            $data = ['status' => 0, 'msg' => '商家“微支付”支付方式未启用或信息不全，暂不能支付！'];
            return $data;
        }

        $config = [
            'wechat' => [
                'app_id' => trim($rsUsers["Users_WechatAppId"]),
                'mch_id' => trim($rsPay["PaymentWxpayPartnerId"]),
                'notify_url' => $url,
                'key' => trim($rsPay["PaymentWxpayPartnerKey"]),
                'cert_client' => $rsPay['PaymentWxpayCert'],
                'cert_key' => $rsPay['PaymentWxpayKey'],
            ],
        ];

        return $config;
    }

}