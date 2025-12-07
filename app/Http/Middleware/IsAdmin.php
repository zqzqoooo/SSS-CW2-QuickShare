<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    // 逻辑：
    // 1. auth()->check(): 确保用户已登录
    // 2. auth()->user()->is_admin: 确保用户是管理员 (我们在数据库中加的字段)
    if (!auth()->check() || !auth()->user()->is_admin) {
        // 如果不是管理员，返回 403 禁止访问，或者重定向回首页
        abort(403, 'Unauthorized action.');
    }

    return $next($request);
    }
}
