<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    /**
     * Handle an incoming request.
     * (处理到来的请求。)
     * * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is logged in AND is banned (检查用户是否已登录 且 被封禁)
        if (Auth::check() && Auth::user()->is_banned) {
            
            // Force logout (强制退出登录)
            Auth::logout();

            // Invalidate the Session and regenerate the token (让 Session 失效并重新生成 token)
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect back to the login page with an error message (重定向回登录页并携带错误消息)
            return redirect()->route('login')->withErrors([

                'email' => 'Your account has been banned by the administrator and you are unable to access the system.',
            ]);
        }

        // If not banned, continue to the next request handler (如果没有被封禁，继续处理下一个请求)
        return $next($request);
    }
}