<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\NoStackException;

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
        $path = $request->getPathInfo();
        //不需要做任何校验的接口
        $whiteList = [
            '/v1/admin/user/login',
            '/v1/admin/user/signup',
            '/v1/admin/user/logout'
        ];

        if (in_array($path, $whiteList)) {
            return $next($request);
        }

        // header中必须有token
        $requestToken = $request->header('token');
        if (empty($requestToken)) {
            throw new NoStackException('登录失效，请重新登录', -2);
        }

        // token必须在redis中
        $dbToken = Redis::get($requestToken);
        if (empty($dbToken)) {
            throw new NoStackException('登录失效，请重新登录', -2);
        }

        // redis中有对应用户信息
        $userInfo = json_decode($dbToken, true);
        if (empty($userInfo)) {
            throw new NoStackException('登录失效，请重新登录', -2);
        }

        $request->merge(
            [
                'operator_id' => $userInfo['user_id'] ?? 0,
                'operator_type' => 20,
                'operator_ip' => $request->getClientIp()
            ]
        );

        return $next($request);
    }
}
