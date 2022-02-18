<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\UserServices;

class AdminUserController extends Controller
{

    private $userServices;

    public function __construct(
        UserServices $userServices
    ) {
        $this->userServices = $userServices;
    }

    /**
     * @api {post} /v1/admin/user/login 管理端登录
     * @apiVersion 1.0.0
     * @apiName 管理端登录
     * @apiGroup AdminUser
     * 
     * @apiParam {String} email 邮箱
     * @apiParam {String} password 密码
     *
     * @apiSuccess {String} token
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);
        $params = $request->all();
        return $this->userServices->login($params);
    }

    /**
     * @api {post} /v1/admin/user/logout 管理端登出
     * @apiVersion 1.0.0
     * @apiName 管理端登出
     * @apiGroup AdminUser
     * 
     * @apiParam {String} token
     */
    public function logout(Request $request)
    {
        $token = $request->header('token');
        return $this->userServices->logout($token);
    }

    /**
     * @api {post} /v1/admin/user/signup 管理端注册
     * @apiVersion 1.0.0
     * @apiName 管理端注册
     * @apiGroup AdminUser
     * 
     * @apiParam {String} email 邮箱地址
     * @apiParam {String} password 密码。最长15位
     * @apiParam {String} nickname 昵称
     */
    public function signup(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|unique:App\Models\User\AdminUserModel,email',
            'password' => 'required|max:15|string',
            'nickname' => 'required|string',
        ], [
            'email.*' => '邮箱地址必传且唯一',
            'password.*' => '密码必传',
            'nickname.*' => '昵称必传'
        ]);
        $params = $request->all();
        $res = $this->userServices->signup($params);
        return $this->buildSucceed($res);
    }

}
