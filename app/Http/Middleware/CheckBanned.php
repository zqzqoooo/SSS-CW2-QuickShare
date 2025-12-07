<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        // 检查：用户已登录 并且 用户的 is_banned 字段为 1
        if (Auth::check() && Auth::user()->is_banned) {
            
            // 强制退出登录
            Auth::logout();

            // 让 Session 失效
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 重定向回登录页，并附带错误信息
            return redirect()->route('login')->withErrors([
                'email' => '您的账号已被管理员封禁，无法访问系统。',
            ]);
        }

        return $next($request);
    }
}