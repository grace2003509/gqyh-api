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
            $expire_time = base64_decode($token_arr[1]);
            $user_info = $m_obj->where('remark_token', $token_arr[0])->first();

            if (!$user_info || $user_info['remark_token'] == '') {
                return response(['status' => -1, 'msg' => '请重新登陆']);
            }

            if (time() > $expire_time) {
                return response(['status' => -1, 'msg' => '请重新登陆']);
            }
        }

        return $next($request);
    }
}
