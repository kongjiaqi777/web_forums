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
        $defaultUserId = rand(100, 124);

        //支持ajax跨域请求
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,token');
        header('Access-Control-Allow-Methods:GET, POST, PATCH, PUT, OPTIONS');
        
        $request->merge(
            [
                'operator_id' => $defaultUserId,
                'operator_type' => 10,
                'operator_ip' => $request->getClientIp()
            ]
        );
        $path = $request->getPathInfo();
        //不需要做任何校验的接口
        $whiteList = $this->noAuthList();
        if (in_array($path, $whiteList)) {
            return $next($request);
        }

        // $requestToken = $request->header('token');
        // $dbToken = Redis::get($requestToken);
        // if (empty($dbToken)) {
        //     throw new NoStackException('登录失效，请重新登录', -2);
        //     // throw new NoStackException('token无效');
        // } else {
        //     return $next($request);
        // }

        return $next($request);
    }

    // 白名单方法
    public function noAuthList()
    {
        return [
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

    // 请求外部网站验证token
    public function verifyToken()
    {

    }

    // 根据token获取用户信息
    public function getUserInfoByToken()
    {

    }
}
