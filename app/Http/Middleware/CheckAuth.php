<?php
/**
 * 验证前端用户是否登陆
 */

namespace App\Http\Middleware;

use App\Models\Member;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class CheckAuth
{
    protected $user;

    public function __construct(Guard $user)
    {
        $this->user = $user;
    }

    public function handle($request, Closure $next)
    {
        $m_obj = new Member();

        if (!$request->header('access-key')) {

            return response(['status' => -1, 'msg' => '请重新登陆']);

        } else {
            $token_arr = explode('.', $request->header('access-key'));
            $token_arr[0] = isset($token_arr[0]) ? $token_arr[0] : '';
            $expire_time = isset($token_arr[1]) ? base64_decode($token_arr[1]) : 0;
            $token_arr[2] = isset($token_arr[2]) ? $token_arr[2] : '';

            if (time() > $expire_time || md5(USERSID) != $token_arr[2]) {
                return response(['status' => -1, 'msg' => '请重新登陆']);
            }

            if(!$request->UserID){
                return response(['status' => 0, 'msg' => '缺少必要的参数UserID']);
            }

            $user_info = $m_obj->select('User_ID', 'remark_token')
                ->where('User_ID', intval($request->UserID))
                ->first();
            if (!$user_info) {
                return response(['status' => 0, 'msg' => '此用户不存在']);
            }

            if ($user_info['remark_token'] == '' || $user_info['remark_token'] != $token_arr[0]) {
                return response(['status' => -1, 'msg' => '请重新登陆']);
            }
        }

        return $next($request);
    }
}
