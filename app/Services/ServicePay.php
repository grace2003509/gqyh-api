<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/28
 * Time: 14:06
 */

namespace App\Services;

use App\Models\UsersConfig;
use App\Models\UsersPayConfig;
use Yansongda\Pay\Pay;

class ServicePay
{
    public function wx_config($url)
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


    public function ali_config($notify_url, $return_url)
    {
        $upc_obj = new UsersPayConfig();

        $rsPay = $upc_obj->find(USERSID);

        if($rsPay["Payment_AlipayEnabled"]==0 || empty($rsPay["Payment_AlipayPartner"]) || empty($rsPay["Payment_AlipayKey"]) || empty($rsPay["Payment_AlipayAccount"])){
            $data = ['status' => 0, 'msg' => '商家“支付宝”支付方式未启用或信息不全，暂不能支付！'];
            return $data;
        }

        $config = [
            'alipay' => [
                'app_id' => $rsPay["Payment_AlipayPartner"],
                'notify_url' => $notify_url,//异步通知 url
                'return_url' => $return_url,//同步通知 url
                'ali_public_key' => 'jcd91vx39h182qrzjl6v5u0ei8fyhgfh',
                'private_key' => 'MIICXQIBAAKBgQDVV9W6k1MD+oS/31k1x6MrXUVxED4MCBwEjrDgRBB+Wx3WS7cE24aE9qfBJCQ5e8AVQxqBDB+9P4qg9pxapAXf3M3zQpjJDorze6iworYhZOlChSi96O0mTpka1oPSRdf3Cd2QZIHUwd8JE6cqcBDq5OUxajFrtM6GZqHM1tKCMQIDAQABAoGAflknlfi8WIXcarn+3m/ePcdeYeiJppyC41wSeq80yXBzCkALIxBT6Zkenq8l2PlmN/Fm1/hzL6RbGJsU3EV5yBbrJkAZhHnpP1PvkJ/b9inQqOEL24ZHPugwFDKW270JM28geWJIRzxXOg+bBYvdElyhRiSgYB4fWo0NTTVQ6KUCQQDsWk/NVVMyxJG8fOmIMj0b0VQlyD/ExWa5xuDsv3A6y59SFdApyzByKkgx3IGEaE6SWVXjT/keszoIa41ZFkPLAkEA5xPeDzj4KS8o9NJFIBdg8I24PJzR8havGjM6IFwOYjYOujb29lnX1vYsf77fTZaCriMSm8QSaSdUwU6eFUBqcwJBALeUjleW5sCQHgKho2K+YuiwdwPBvoZALcIuz5YUPD/u2RkRCFbuE/sZDGpuM8t8mUrjSOr+uyk1XOYdY/TGbnECQFIYAdUgpTFkesV3im1bQOpVPvXxtLiwDGdfebATZFzJ3bOUYWqmAoNxE+ASfJzA8w5QkYTbRLss6PSALLNaHjcCQQCnD8W16KF6p9qzreY3ODZkumL7Q3rts5+Nd1mBncX/uqrgK5JWhqZJpvh687wttyX5aaZvnhjW4igZifaP1TiU',
            ],
        ];

        return $config;
    }


    /**
     * 微信充值余额，相关处理
     * $type 支付类型（1:订单，2:积分，3:余额）
     */
    public function wx_pay($amount, $ItemID, $type, $pay_subject)
    {
        switch ($type){
            case 1:
                $notify_url = $_SERVER['HTTP_HOST'] . "/api/cart/order_wx_notify/{$ItemID}";
                break;
            case 2:
                $notify_url = $_SERVER['HTTP_HOST'] . "/api/center/integral_wx_notify/{$ItemID}";
                break;
            case 3:
                $notify_url = $_SERVER['HTTP_HOST'] . "/api/center/money_wx_notify/{$ItemID}";
                break;
            default:
                $data = ['status' => 0, 'msg' => '缺少参数'];
                return $data;
        }

        $config = $this->wx_config($notify_url);
        if(isset($config['status']) && $config['status'] == 0){
            return $config;
        }

        $config_biz = [
            'out_trade_no' => time() . strval($ItemID),
            'total_fee' => strval(floatval($amount) * 100), // **单位：分**
            'body' => $pay_subject,
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
//            'openid' => $openid,
        ];

        $pay = new Pay($config);
        return $pay->driver('wechat')->gateway('wap')->pay($config_biz);

    }


    /**
     * 支付宝充值积分，相关处理
     */
    public function ali_pay($amount, $ItemID, $type, $pay_subject)
    {
        switch ($type){
            case 1:
                $notify_url = $_SERVER['HTTP_HOST'] . "/api/cart/order_ali_notify/{$ItemID}";
                $return_url = $_SERVER['HTTP_HOST'] . "/api/cart/order_ali_return/{$ItemID}";
                break;
            case 2:
                $notify_url = $_SERVER['HTTP_HOST'] . "/api/center/integral_ali_notify/{$ItemID}";
                $return_url = $_SERVER['HTTP_HOST'] . "/api/center/integral_ali_return/{$ItemID}";
                break;
            case 3:
                $notify_url = $_SERVER['HTTP_HOST'] . "/api/center/money_ali_notify/{$ItemID}";
                $return_url = $_SERVER['HTTP_HOST'] . "/api/center/money_ali_return/{$ItemID}";
                break;
            default:
                $data = ['status' => 0, 'msg' => '缺少参数'];
                return $data;
        }
        $config = $this->ali_config($notify_url, $return_url);
        if(isset($config['status']) && $config['status'] == 0){
            return $config;
        }

        $config_biz = [
            'out_trade_no' => time().$ItemID,                 // 订单号
            'total_amount' => strval(floatval($amount)),                 // 订单金额，单位：元
            'subject' => $pay_subject,   // 订单商品标题
        ];

        $pay = new Pay($config);
        return $pay->driver('alipay')->gateway('wap')->pay($config_biz);
    }

}