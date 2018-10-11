<?php
/**
 * Created by PhpStorm.
 * Admin: apple
 * Date: 2016/10/7
 * Time: 23:36
 */

namespace App\Services;

use Carbon\Carbon;

class ServiceSMS
{
    private $account = 'dxwblackboy1987';
    private $pwd = '8226A146350519B335244B8F0228';

    private function convert($str, $from = 'utf-8', $to = 'gb2312') {
        if(!$str) return '';
        $from = strtolower($from);
        $to = strtolower($to);
        if($from == $to) return $str;
        $from = str_replace('gbk', 'gb2312', $from);
        $to = str_replace('gbk', 'gb2312', $to);
        $from = str_replace('utf8', 'utf-8', $from);
        $to = str_replace('utf8', 'utf-8', $to);
        if($from == $to) return $str;
        $tmp = array();
        if(function_exists('iconv')) {
            if(is_array($str)) {
                foreach($str as $key => $val) {
                    $tmp[$key] = iconv($from, $to."//IGNORE", $val);
                }
                return $tmp;
            } else {
                return iconv($from, $to."//IGNORE", $str);
            }
        } else if(function_exists('mb_convert_encoding')) {
            if(is_array($str)) {
                foreach($str as $key => $val) {
                    $tmp[$key] = mb_convert_encoding($val, $to, $from);
                }
                return $tmp;
            } else {
                return mb_convert_encoding($str, $to, $from);
            }
        } else {
            return $this->dconvert($str, $to, $from);
        }
    }

    private function dconvert($str, $from = 'utf-8', $to = 'gb2312') {
        $from = str_replace('utf-8', 'utf8', $from);
        $to = str_replace('utf-8', 'utf8', $to);
        $tmp = file($_SERVER["DOCUMENT_ROOT"].'/static/gb-unicode.table');
        if(!$tmp) return $str;
        $table = array();
        while(list($key, $value) = each($tmp)) {
            if($from == 'utf8') {
                $table[hexdec(substr($value, 7, 6))]=substr($value, 0, 6);
            } else {
                $table[hexdec(substr($value, 0, 6))] = substr($value, 7 , 6);
            }
        }
        unset($tmp);
        $dstr = '';
        if($from == 'utf8') {
            $len = strlen($str);
            $i = 0;
            while($i < $len) {
                $c = ord(substr( $str, $i++, 1 ));
                switch($c >> 4) {
                    case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7:
                    $dstr .= substr( $str, $i-1, 1);
                    break;
                    case 12: case 13:
                    $char2 = ord( substr( $str, $i++, 1));
                    $char3 = $table[(($c & 0x1F) << 6) | ($char2 & 0x3F)];
                    $dstr .= $this->dhex2bin(dechex(  $char3 + 0x8080));
                    break;
                    case 14:
                        $char2 = ord( substr( $str, $i++, 1 ) );
                        $char3 = ord( substr( $str, $i++, 1 ) );
                        $char4 = $table[(($c & 0x0F) << 12) | (($char2 & 0x3F) << 6) | (($char3 & 0x3F) << 0)];
                        $dstr .= $this->dhex2bin(dechex($char4 + 0x8080));
                        break;
                }
            }
        } else {
            while($str) {
                if(ord(substr($str, 0, 1)) > 127) {
                    $utf8 = $this->dch2utf8(hexdec($table[hexdec(bin2hex(substr($str,0,2)))-0x8080]));
                    $dutf8 = strlen($utf8);
                    for($i = 0; $i < $dutf8; $i += 3) {
                        $dstr .= chr(substr($utf8, $i,3));
                    }
                    $str = substr($str, 2, strlen($str));
                } else {
                    $dstr .= substr($str, 0, 1);
                    $str = substr($str, 1, strlen($str));
                }
            }
        }
        unset($table);
        return $dstr;
    }

    private function dhex2bin($hexdata) {
        $bindata = '';
        $dhexdata = strlen($hexdata);
        for($i = 0; $i < $dhexdata; $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }
        return $bindata;
    }

    private function dch2utf8($c) {
        $str = '';
        if ($c < 0x80) {
            $str .= $c;
        } else if ($c < 0x800) {
            $str .= (0xC0 | $c>>6);
            $str .= (0x80 | $c & 0x3F);
        } else if ($c < 0x10000) {
            $str .= (0xE0 | $c>>12);
            $str .= (0x80 | $c>>6 & 0x3F);
            $str .= (0x80 | $c & 0x3F);
        } else if ($c < 0x200000) {
            $str .= (0xF0 | $c>>18);
            $str .= (0x80 | $c>>12 & 0x3F);
            $str .= (0x80 | $c>>6 & 0x3F);
            $str .= (0x80 | $c & 0x3F);
        }
        return $str;
    }

    private function strip_sms($message) {
        $message = strip_tags($message);
        $message = preg_replace("/&([a-z]{1,});/", '', $message);
        $message = str_replace(' ', '', $message);
        return $message;
    }

    private function word_count($string) {
        $string = $this->convert($string, 'utf-8', 'gbk');
        $length = strlen($string);
        $count = 0;
        for($i = 0; $i < $length; $i++) {
            $t = ord($string[$i]);
            if($t > 127) $i++;
            $count++;
        }
        return $count;
    }

    private function send_sms_curl($url){
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    public function send_sms($mobile, $message) {
        global $DB1;
        $flag = 1;

        if($flag==1){
            $mes = $message;
            $message = $this->strip_sms($message);
            $word = $this->word_count($message);
            $message = $this->convert($message, 'UTF-8', 'UTF-8');
            $recode = 0;
            $smsapi = "web.duanxinwang.cc/asmx/smsservice.aspx";
            $user = $this->account;
            $pass = $this->pwd;
            $sendurl = "http://".$smsapi."?name={$user}&pwd={$pass}&content=".urlencode($message)."&mobile={$mobile}&stime=&sign=观前一号&type=pt&extno=";
            $res = $this->send_sms_curl($sendurl);
            $res = explode(',', $res);
            $statusStr = array(
                "0" => "短信发送成功",
                "1" => "含有敏感词汇",
                "2" => "余额不足",
                "3" => "没有号码",
                "4" => "包含sql语句",
                "10" => "账号不存在",
                "11" => "账号注销",
                "12" => "账号停用",
                "13" => "IP鉴权失败",
                "14" => "格式错误",
                "-1" => "系统异常"
            );

            if($res[0]==0) $recode=1;
            $code = $statusStr[$res[0]];
            $Data = array(
                "mobile"=>$mobile,
                "message"=>$mes,
                "word"=>$word,
                "sendtime"=>time(),
                "code"=>$code,
                "usersid"=>USERSID
            );
            $DB1->Add("sms",$Data);

            return $recode;
        }else{
            return false;
        }
    }


//获取剩余短信条数
    function get_remain_sms() {

        $user = $this->account;
        $pass = $this->pwd;
        $sendurl = 'http://web.duanxinwang.cc/asmx/smsservice.aspx?name='.$user.'&pwd='.$pass.'&type=balance';
        $res = $this->send_sms_curl($sendurl);
        $res_con = explode("\n", $res);		//接口输出的为两行数据，用换行符分割
        if($res_con[0] != 0) {
            return 0;
        } else {
            $res = explode(',', $res_con[0]);
            return $res[1];
        }
    }


}