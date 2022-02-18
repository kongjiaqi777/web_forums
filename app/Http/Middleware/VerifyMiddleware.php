<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\NoStackException;
use App\Libs\CurlLib;
use App\Models\User\UserModel;

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
        // $defaultUserId = rand(100, 124);

        //支持ajax跨域请求
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,token');
        header('Access-Control-Allow-Methods:GET, POST, PATCH, PUT, OPTIONS');
        
        
        $path = $request->getPathInfo();
        //不需要做任何校验的接口
        $whiteList = $this->noAuthList();
        if (in_array($path, $whiteList)) {
            return $next($request);
        }

        $requestToken = $request->header('token');

        if (empty($requestToken)) {
            throw new NoStackException('登录失效，请重新登录', -2);
        }

        $dbToken = Redis::get($requestToken);
        if (empty($dbToken)) {
            $userSourceInfo = $this->verifyToken($requestToken);

            $userModel = new UserModel();
            $userInfo = $userModel->setUserBySourceInfo($userSourceInfo);
            $token = $userSourceInfo['token'] ?? '';
            $this->setRedis($token, $userInfo);

            $userStatus = $userInfo['status'] ?? 0;
            $forbiddenList = $this->forbiddenList();
            if ($userStatus == config('display.user_status.forbidden.code') && in_array($path, $forbiddenList)) {
                throw New NoStackException('禁言中,不允许此操作');
            }
        } else {
            $userInfo = json_decode($dbToken, true);
            if (empty($userInfo)) {
                throw new NoStackException('登录失效，请重新登录', -2);
            }
        }

        $request->merge(
            [
                'operator_id' => $userInfo['id'] ?? 0,
                'operator_type' => 10,
                'operator_ip' => $request->getClientIp()
            ]
        );

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
            '/v1/post/suggest',
            // 广播列表
            '/v1/post/list',
            '/v1/user/logout',
        ];
    }

    // 请求外部网站验证token
    public function verifyToken($token)
    {
        $validateRes = CurlLib::curl_post(
            getenv('SOURCE_USERINFO_API'),
            [],
            ['Authorization: Bearer ' . $token]
        );

        $returnCode = $validateRes['code'] ?? 0;
        if ($returnCode == 200) {
            return $validateRes['data'] ?? [];
        } else {
            throw New NoStackException('源网站查询用户信息失败');
        }
    }

    public function setRedis($token, $userInfo)
    {
        $value = json_encode($userInfo);
        Redis::set($token, $value);
        Redis::expire($token, intval(env('SESSION_EXPIRE', 86400)));
    }

    // 禁言
    public function forbiddenList()
    {
        return [
            '/v1/post/create',
            '/v1/post/update',
            '/v1/reply/create',
            '/v1/reply/create_sub',
        ];
    }
}
