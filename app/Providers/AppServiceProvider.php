<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //不得大于
        Validator::extend('nothan', function ($attribute, $value, $parameters, $validator) {
            //需验证的值$value
            $other = Input::get($parameters[0]);//获取需对比的参数值
            if($value > $other){
                return false;
            }else{
                return true;
            }
        });

        //验证手机号
        Validator::extend('mobile', function ($attribute, $value, $parameters, $validator) {
            //需验证的值$value
            $is = preg_match('/^1[3|4|5|7|8]\d{9}$/', $value);
            if(!$is){
                return false;
            }else{
                return true;
            }
        });

        //验证电话号
        Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            //需验证的值$value
            $is1 = preg_match('/^1[34578][0-9]{9}$/', $value);
            $is2 = preg_match('/^[0-9]{3,4}\-[0-9]{7,8}$/', $value);
            if(!$is1 && !$is2){
                return false;
            }else{
                return true;
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
