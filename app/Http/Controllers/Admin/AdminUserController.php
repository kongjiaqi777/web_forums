<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
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

    /**
     * @api {post} /v1/admin/user/logout 管理端登出
     * @apiVersion 1.0.0
     * @apiName 管理端登出
     * @apiGroup AdminUser
     * 
     * @apiParam {String} token
     */

}
