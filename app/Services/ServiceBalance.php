<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/28
 * Time: 14:06
 */

namespace App\Services;


use App\Models\Biz;
use App\Models\Dis_Account_Record;
use App\Models\Dis_Record;
use App\Models\ShopSalesPayment;
use App\Models\ShopSalesRecord;
use App\Models\UserOrder;

class ServiceBalance
{
    //创建付款单
    public function create_payment($bizid, $start_time, $end_time, $status)
    { // 付款单生成详情
        $lists = $this->get_sales_record($bizid, $start_time, $end_time, $status);
        $alltotal = $cash = $web_total = $logistic = 0;
        if(empty($lists)) return false;

        foreach ($lists as $id => $item) {
            $alltotal += $item["Order_Amount"];
            $cash += isset($item["Order_Diff"]) ? $item["Order_Diff"] : 0;
            $logistic += $item["Order_Shipping"];
            $web_total += $item["Web_Price"];
        }

        $data = array(
            "products_num" => count($lists),
            "alltotal" => $alltotal,
            "logistic" => $logistic,
            "cash" => $cash,
            "web" => $web_total,
            "supplytotal" => $alltotal + $logistic - $cash - $web_total
        );
        return $data;
    }



    public function get_sales_record($bizid, $start_time, $end_time, $status)
    {
        $ssr_obj = new ShopSalesRecord();
        if($bizid > 0) $ssr_obj = $ssr_obj->where('Biz_ID', $bizid);
        if($start_time > 0) $ssr_obj = $ssr_obj->where('Record_CreateTime', '>=', $start_time);
        if($end_time > 0) $ssr_obj = $ssr_obj->where('Record_CreateTime', '<=', $end_time);
        $ssr_obj = $ssr_obj->where('Record_Status', $status);
        $lists = $ssr_obj->get();
        foreach ($lists as $r) {
            $lists[$r["Record_ID"]] = $r;
        }
        return $lists;
    }



    public function rmb_format($money = 0, $is_round = true, $int_unit = '元')
    {
        $chs = array(
            0,
            '壹',
            '贰',
            '叁',
            '肆',
            '伍',
            '陆',
            '柒',
            '捌',
            '玖'
        );
        $uni = array(
            '',
            '拾',
            '佰',
            '仟'
        );
        $dec_uni = array(
            '角',
            '分'
        );
        $exp = array(
            '',
            '万',
            '亿'
        );
        $res = '';
        // 以 元为单位分割
        $parts = explode('.', $money, 2);
        $int = isset($parts[0]) ? strval($parts[0]) : 0;
        $dec = isset($parts[1]) ? strval($parts[1]) : '';
        // 处理小数点
        $dec_len = strlen($dec);
        if (isset($parts[1]) && $dec_len > 2) {
            $dec = $is_round ? substr(strrchr(strval(round(floatval("0." . $dec), 2)), '.'), 1) : substr($parts[1], 0, 2);
        }
        // number= 0.00时，直接返回 0
        if (empty($int) && empty($dec)) {
            return '零';
        }

        // 整数部分 从右向左
        for ($i = strlen($int) - 1, $t = 0; $i >= 0; $t ++) {
            $str = '';
            // 每4字为一段进行转化
            for ($j = 0; $j < 4 && $i >= 0; $j ++, $i --) {
                $u = $int{$i} > 0 ? $uni[$j] : '';
                $str = $chs[$int{$i}] . $u . $str;
            }
            $str = rtrim($str, '0');
            $str = preg_replace("/0+/", "零", $str);
            $u2 = $str != '' ? $exp[$t] : '';
            $res = $str . $u2 . $res;
        }
        $dec = rtrim($dec, '0');
        // 小数部分 从左向右
        if (! empty($dec)) {
            $res .= $int_unit;
            $cnt = strlen($dec);
            for ($i = 0; $i < $cnt; $i ++) {
                $u = $dec{$i} > 0 ? $dec_uni[$i] : ''; // 非0的数字后面添加单位
                $res .= $chs[$dec{$i}] . $u;
            }
            if ($cnt == 1)
                $res .= '整';
            $res = rtrim($res, '0'); // 去掉末尾的0
            $res = preg_replace("/0+/", "零", $res); // 替换多个连续的0
        } else {
            $res .= $int_unit . '整';
        }
        return $res;
    }



    public function add_sales($orderid)
    {
        $uo_obj = new UserOrder();
        $dr_obj = new Dis_Record();
        $dar_obj = new Dis_Account_Record();
        $b_obj = new Biz();
        $ssr_obj = new ShopSalesRecord();

        $item = $uo_obj->where('Order_ID', $orderid)
            ->where('Order_Status', 4)
            ->first();
        $r = $dr_obj->select('Record_ID')->where('Order_ID', $orderid)->get();
        $bonus = $dar_obj->whereIn('Ds_Record_ID', $r['Record_ID'])->sum('Record_Money');
        $rsBizff = $b_obj->select('Finance_Type', 'Finance_Rate')->find($item['Biz_ID']);

        if ($item) {
            $CartList = json_decode($item["Order_CartList"], true);
            $curpro_cash = 0;
            $Shipping = json_decode(htmlspecialchars_decode($item["Order_Shipping"]), true);
            $backnumarr = json_decode($item['back_qty_str'],true);
            $backqtyarr = json_decode($item['cash_str'],true);
            if (!empty($backnumarr) && !empty($backqtyarr)) {
                foreach ($backnumarr as $Product_ID=>$Product_List) {
                    foreach ($Product_List as $k => $v) {
                        if (isset($backqtyarr['cash_str'][$Product_ID][$k]) && $v) {
                            $curpro_cash += $backqtyarr['cash_str'][$Product_ID][$k]['cash_str'] / $backqtyarr['cash_str'][$Product_ID][$k]['Qty'] * $v;
                        }
                    }
                }
            }

            if (!empty($item['back_qty_str'])) {
                if (!empty($curpro_cash)) {
                    $item['Coupon_Cash'] = $item['Coupon_Cash'] - $curpro_cash;
                }
                $web_pjs = $this->repeat_list($CartList, $item['Integral_Money'], $item['curagio_money'], $item['Coupon_Cash'], 0, $backnumarr);
                $web_pjs = $web_pjs['web_priejs'];
            } else {
                $web_pjs = $item['Web_Pricejs'];
            }

            $Data = array(
                "Users_ID" => USERSID,
                "Order_ID" => $orderid,
                "Order_Json" => $item["Order_CartList"],
                "Biz_ID" => $item["Biz_ID"],
                "Bonus" => $bonus,
                "Order_Amount" => $item["Order_TotalAmount"] - $item["Back_Amount_Source"] - (empty($Shipping["Price"]) ? 0 : $Shipping["Price"]),
                "Order_Diff" => $item["Coupon_Cash"],
                "Order_Shipping" => empty($Shipping["Price"]) ? 0 : $Shipping["Price"],
                "Order_TotalPrice" => $item["Order_TotalPrice"] - $item["Back_Amount"],
                "Record_CreateTime" => time(),
                "Finance_Type" => $rsBizff['Finance_Type'],
                "Finance_Rate" => $rsBizff['Finance_Rate'],
                "Web_Price" => $web_pjs,
            );
            return $ssr_obj->create($Data);
        }
        return false;
    }



    private function repeat_list($CartList, $diyong_money, $curagio_money, $cash, $catloop, $backnumarr = [])
    {
        $_SESSION['yformat'] = '';
        $muilti = 0;
        if (count($CartList) > 1) {
            $muilti = 1;
        }
        if (empty($muilti)) {
            foreach($CartList as $key=>$products){
                if (count($products) > 1) {
                    $muilti = 1;
                    break;
                }
            }
        }

        $web_prie = 0;
        $web_priejs = 0;
        if (empty($muilti)) {
            foreach($CartList as $key=>$products){
                foreach($products as $k=>$v){
                    if (count($v["Property"]) > 0) {
                        $produ_price = isset($v["Property"]["shu_pricesimp"]) ? $v["Property"]["shu_pricesimp"] : 0;
                        $produ_priceg = isset($v["Property"]["shu_priceg"]) ? $v["Property"]["shu_priceg"] : 0;
                    }else{
                        $produ_price = isset($v["ProductsPriceX"]) ? $v["ProductsPriceX"] : 0;
                        $produ_priceg = isset($v["Products_PriceS"]) ? $v["Products_PriceS"] : 0;
                    }
                    $procuragio = $produ_price * ($v["user_curagio"] * 0.01) * $v["Qty"];
                    $produ_web = $produ_price - $produ_priceg;
                    if (empty($v['Biz_FinanceType'])) {
                        $web_prie += ($produ_price * $v["Qty"] - $diyong_money - $procuragio - $cash) * $v['Biz_FinanceRate'] * 0.01;
                        $_SESSION['yformat'][$key][$k] = '(商品价格'.$produ_price.'*商品数量'.$v["Qty"].'-积发抵用'.$diyong_money.'-会员折扣'.$procuragio.'-优惠券'.$cash.')*交易额比例'.$v['Biz_FinanceRate'].'%';
                        $web_priejs += ($produ_price * $v["Qty"]) * $v['Biz_FinanceRate'] * 0.01;
                    } else {
                        if (empty($v['Products_FinanceType'])) {
                            $web_prie += ($produ_price * $v["Qty"] - $diyong_money - $procuragio - $cash) * $v['Products_FinanceRate'] * 0.01;
                            $_SESSION['yformat'][$key][$k] = '(商品价格'.$produ_price.'*商品数量'.$v["Qty"].'-积发抵用'.$diyong_money.'-会员折扣'.$procuragio.'-优惠券'.$cash.')*交易额比例'.$v['Products_FinanceRate'].'%';
                            $web_priejs += ($produ_price * $v["Qty"]) * $v['Products_FinanceRate'] * 0.01;
                        } else {
                            $diyongagiocash = $diyong_money + $cash + $procuragio;
                            $web_prie += $produ_web * $v["Qty"] - $diyongagiocash;
                            $_SESSION['yformat'][$key][$k] = '(商品价格'.$produ_price.'-供货价'.$produ_priceg.')*商品数量'.$v["Qty"].'-(积发抵用'.$diyong_money.'+优惠券'.$cash.')+会员折扣'.$procuragio;
                            $web_priejs += $produ_web * $v["Qty"];
                        }
                    }
                }
            }
            $CartListupdate = $CartList;
        } else {
            $shop_num = 0;
            foreach($CartList as $key=>$products){
                foreach($products as $k=>$v){
                    $shop_num += $v["Qty"];
                }
            }
            $CartListnew = $CartList;
            foreach($CartList as $key=>$products){
                foreach($products as $k=>$v){
                    if (count($v["Property"]) > 0) {
                        $produ_price = isset($v["Property"]["shu_pricesimp"]) ? $v["Property"]["shu_pricesimp"] : 0;
                        $produ_priceg = isset($v["Property"]["shu_priceg"]) ? $v["Property"]["shu_priceg"] : 0;
                    }else{
                        $produ_price = isset($v["ProductsPriceX"]) ? $v["ProductsPriceX"] : 0;
                        $produ_priceg = isset($v["Products_PriceS"]) ? $v["Products_PriceS"] : 0;
                    }
                    $procuragio = $produ_price * ($v["user_curagio"] * 0.01) * $v["Qty"];
                    $produ_web = $produ_price - $produ_priceg;
                    if (empty($v['Biz_FinanceType'])) {
                        $web_prie += ($produ_price * $v["Qty"] - $diyong_money / $shop_num * $v["Qty"] - $procuragio - $cash / $shop_num * $v["Qty"]) * $v['Biz_FinanceRate'] * 0.01;
                        $_SESSION['yformat'][$key][$k] = '(商品价格'.$produ_price.'*商品数量'.$v["Qty"].'-积发抵用'.$diyong_money.'/商品总数量'.$shop_num.'*商品数量'.$v["Qty"].'-会员折扣'.$procuragio.'-优惠券'.$cash.'/商品总数量'.$shop_num.'*商品数量'.$v["Qty"].')*交易额比例'.$v['Biz_FinanceRate'].'%';
                        $web_priejs += ($produ_price * $v["Qty"]) * $v['Biz_FinanceRate'] * 0.01;
                        $web_prie_shop = ($produ_price * $v["Qty"] - $diyong_money / $shop_num * $v["Qty"] - $procuragio - $cash / $shop_num * $v["Qty"]) * $v['Biz_FinanceRate'] * 0.01;
                    } else {
                        if (empty($v['Products_FinanceType'])) {
                            $web_prie += ($produ_price * $v["Qty"] - $diyong_money / $shop_num * $v["Qty"] - $procuragio - $cash / $shop_num * $v["Qty"]) * $v['Products_FinanceRate'] * 0.01;
                            $_SESSION['yformat'][$key][$k] = '(商品价格'.$produ_price.'*商品数量'.$v["Qty"].'-积发抵用'.$diyong_money.'/商品总数量'.$shop_num.'*商品数量'.$v["Qty"].'-会员折扣'.$procuragio.'-优惠券'.$cash.'/商品总数量'.$shop_num.'*商品数量'.$v["Qty"].')*交易额比例'.$v['Products_FinanceRate'].'%';
                            $web_priejs += ($produ_price * $v["Qty"]) * $v['Products_FinanceRate'] * 0.01;
                            $web_prie_shop = ($produ_price * $v["Qty"] - $diyong_money / $shop_num * $v["Qty"] - $procuragio - $cash / $shop_num * $v["Qty"]) * $v['Products_FinanceRate'] * 0.01;
                        } else {
                            $diyongagiocash = ($diyong_money + $cash) / $shop_num * $v["Qty"] + $procuragio;
                            $web_prie += $produ_web * $v["Qty"] - $diyongagiocash;
                            $_SESSION['yformat'][$key][$k] = '(商品价格'.$produ_price.'-供货价'.$produ_priceg.')*商品数量'.$v["Qty"].'-((积发抵用'.$diyong_money.'+优惠券'.$cash.')/商品总数量'.$shop_num.'+会员折扣'.$procuragio.')'.'*商品数量'.$v["Qty"];
                            $web_priejs += $produ_web * $v["Qty"];
                            $web_prie_shop = $produ_web * $v["Qty"] - $diyongagiocash;
                        }
                    }
                    $CartListnew[$key][$k]['web_prie_shop'] = $web_prie_shop;
                }
            }
            $CartListupdate = $CartListnew;
        }
        $webarr = array();
        $webarr['web_priejs'] = $web_priejs;
        $webarr['web_prie'] = $web_prie;
        $webarr['CartListupdate'] = $CartListupdate;
        $webarr['muilti'] = $muilti;
        return $webarr;
    }
}