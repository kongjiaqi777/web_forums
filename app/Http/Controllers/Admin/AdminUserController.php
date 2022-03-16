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

    /**
     * @api {get} /v1/admin/user/suggest 根据信息模糊搜索用户
     * @apiVersion 1.0.0
     * @apiName 管理端用户模糊搜索
     * @apiGroup AdminUser
     *
     * @apiParam {string} nickname 用户昵称或标签
     *
     * @apiParamExample Request-Example
     * {
     *      "nickname": "霜"
     * }
     *
     * @apiSuccess {Numeric} id 用户ID
     * @apiSuccess {String} email 邮件信息
     * @apiSuccess {String} nickname 昵称
     * @apiSuccess {String} avatar 头像
     * @apiSuccess {String} label 个人简介
     * 
     *
     * @apiSuccessExample Success-Response
     *  {
            "code": 0,
            "msg": "success",
            "info": {
                "list": [
                    {
                        "id": 118,
                        "nickname": "霜降",
                        "label": "test",
                        "avatar": null,
                        "email": "test18@123.com"
                    }
                ],
                "pagination": {
                    "page": 1,
                    "perpage": 20,
                    "total_page": 1,
                    "total_count": 1
                }
            }
        }
     */
    public function suggest(Request $request)
    {
        $this->validate($request, [
            'nickname' => 'required',
        ], [
            'nickname.required' => '需要输入昵称或标签'
        ]);

        $params = $request->only(['nickname']);

        $res = $this->userServices->suggest($params);
        return $this->buildSucceed($res);
    }


}
