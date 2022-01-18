<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function noAuthList()
    {
        $url = [
            // 模糊搜索用户
            '/v1/user/suggest_user',
            // 模糊搜索广场
            '/v1/square/suggest',
            // 广场详情
            '/v1/square/detail',
            // 模糊搜索广播
            // 广播列表
            '/v1/post/list',
            
        ];
    }
}
