<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyAdminMiddleware
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
        $defaultUserId = 1;

        //支持ajax跨域请求
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,token');
        header('Access-Control-Allow-Methods:GET, POST, PATCH, PUT, OPTIONS');
        
        $request->merge(
            [
                'operator_id' => $defaultUserId,
                'operator_type' => 20,
                'operator_ip' => $request->getClientIp()
            ]
        );
        $path = $request->getPathInfo();
        //不需要做任何校验的接口
        $whiteList = [
            '/v1/admin/login'
        ];

        if (in_array($path, $whiteList)) {
            return $next($request);
        }

        return $next($request);
    }
}
