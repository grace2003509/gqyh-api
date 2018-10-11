<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use App\Models\Role;
use illuminate\support\facades\route;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        if( Auth::user()->hasRole('administrator')){
            return $next($request);
        }

        //获得当前路由
        $per=Route::currentRouteName();
        //获取当前用户角色
        $roles = Auth::user()->roles->toArray();

        foreach($roles as $role){
            $perms[] = Role::find($role['id'])->perms;
        }
            $p = [];
        //取出用户的所有权限路由
        foreach($perms as $perm){
            foreach($perm as $list){
                $p[$list->name] = $list->name;
            }
        }
        $str = implode($p,',');
        $p = explode(',',$str);

        if(!in_array($per,$p)){
            if($request->ajax()){
                return response();
            }
            return redirect()->route('admin.home')->withErrors('您没有权限访问!!!');
        }

        return $next($request);
    }
}
