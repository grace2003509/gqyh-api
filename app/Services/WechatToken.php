<?php
/**
 * Created by PhpStorm.
 * Admin: admin
 * Date: 2018/8/21
 * Time: 13:44
 */

namespace App\Services;


use App\Models\UsersAccessToken;
use App\Models\UsersConfig;
use App\Models\WeixinLog;

class WechatToken
{
    var $usersid;
    var $access_token;
    var $curl_timeout;

    function __construct()
    {
        $this->curl_timeout = 30;
        $this->access_token = '';
        $this->usersid = USERSID;
    }

    private function curl_get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);
        curl_close($ch);
        $encoding = mb_detect_encoding($res, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
        $res = mb_convert_encoding($res, 'utf-8', $encoding);
        $data = json_decode($res, true);
        return $data;
    }

    public function get_access_token()
    {
        $token = 0;
        $item_obj = new UsersAccessToken();
        $item = $item_obj->find(1);
        if ($item) {
            $diff = intval($item["expires_in"]) - 300;
            if ($item["access_token"] && $diff >= time()) {
                $weixin_ip = $this->curl_get('https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=' . $item["access_token"]);
                if (!empty($weixin_ip["errcode"])) {
                    if ($weixin_ip["errcode"] == '40001' && strpos($weixin_ip["errmsg"], "access_token is invalid or not latest") > -1) {
                        $token = 0;
                    }

                    $log_obj = new WeixinLog();
                    $log_obj->create(["message" => $weixin_ip["errmsg"], 'log_CreateTime' => time()]);
                } else {
                    $this->access_token = $item["access_token"];
                    $token = 1;
                }
            }
        }
        if ($token == 0) {
            $user_obj = new UsersConfig();
            $users = $user_obj->find(USERSID);
            if ($users["Users_WechatAppSecret"] && $users["Users_WechatAppId"] && in_array($users["Users_WechatType"], array('1', '2', '3'))) {
                $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $users["Users_WechatAppId"] . '&secret=' . $users["Users_WechatAppSecret"];
                $data = $this->curl_get($url);
                if (empty($data["errcode"])) {
                    $Data = array(
                        'access_token' => $data["access_token"],
                        'expires_in' => time() + intval($data["expires_in"])
                    );
                    if (!$item) {
                        $Data["usersid"] = $this->usersid;
                        $item_obj->create($Data);
                    } else {
                        $item->update($Data);
                    }
                    $message = $this->usersid . "access_token" . date("Y-m-d H:i:s", time());
                    $log_obj = new WeixinLog();
                    $log_obj->create(["message" => $message, 'log_CreateTime' => time()]);
                    $this->access_token = $data["access_token"];
                }
            }
        }

        return $this->access_token;
    }

    public function GetUserInfo($openid)
    {
        $this->get_access_token();
        $data = array();
        if ($this->access_token) {
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $this->access_token . "&openid=" . $openid . "&lang=zh_CN";
            $Data = $this->curl_get($url);
            if (empty($Data["errcode"])) {
                $data = $Data;
            }
        }
        return $data;
    }
}