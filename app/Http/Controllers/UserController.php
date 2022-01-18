<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
      /**
     * @api {get} /v1/user/suggest_user 根据信息模糊搜索用户
     * @apiVersion 1.0.0
     * @apiName SuggestUser
     * @apiGroup User
     * @apiPermission 允许不登录[用户未登录is_follow全部为0]
     *
     * @apiParam {string} nickname 用户昵称或标签
     *
     * @apiParamExample Request-Example
     * {
     *      "nickname": "啊"
     * }
     *
     * @apiSuccess {Numeric} id 用户ID
     * @apiSuccess {String} email 邮件信息
     * @apiSuccess {String} nickname 昵称
     * @apiSuccess {String} avatar 头像
     * @apiSuccess {String} label 个人简介
     * @apiSuccess {Numeric} is_follow 当前登录用户是否关注[0未关注/1已关注，用户未登录统一为0]
     *
     * @apiSuccessExample Success-Response
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": [{
     *          "id": 1001,
     *          "email": "hello@qq.com",
     *          "nickname": "啊你好",
     *          "avatar": "",
     *          "label": "这里是个人简介"
     *      }, {
     *          "id": 1002,
     *          "email": "hello@qq.com",
     *          "nickname": "你好",
     *          "avatar": "",
     *          "label": "啊这里是个人简介"
     *      }]
     *  }
     */
    public function suggestUser(Request $request)
    {
        
    }

    /**
     * @api {post} /v1/user/info 获取用户信息
     * @apiVersion 1.0.0
     * @apiName UserInfo
     * @apiGroup User
     * @apiPermission 需要登录
     *
     * @apiParam {string} token token信息
     *
     * @apiSuccess {Numeric} id 用户ID
     * @apiSuccess {String} email 邮件信息
     * @apiSuccess {String} nickname 昵称
     * @apiSuccess {String} avatar 头像
     * @apiSuccess {String} label 个人简介
     *
     * @apiSuccessExample Success-Response
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": {
     *          "id": 1001,
     *          "email": "hello@qq.com",
     *          "nickname": "这里是用户昵称",
     *          "avatar": "图片URL",
     *          "label": "这里是个人简介/标签"
     *      }
     *  }
     */
    public function getUserInfoById(Request $request)
    {

    }

    /**
     * @api {post} /v1/user/update_label 修改自己的标签
     * @apiVersion 1.0.0
     * @apiName UpdateLabel
     * @apiGroup User
     * @apiPermission 需要登录
     *
     * @apiParam {String} label 标签
     *
     * @apiParamExample Request-Example
     * {
     *      "label": "new标签"
     * }
     *
     * @apiSuccess {Numeric} id 用户ID
     * @apiSuccess {String} email 邮件信息
     * @apiSuccess {String} nickname 昵称
     * @apiSuccess {String} avatar 头像
     * @apiSuccess {String} label 个人简介
     *
     * @apiSuccessExample Success-Response
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": {
     *          "id": 1001,
     *          "email": "hello@qq.com",
     *          "nickname": "这里是用户昵称",
     *          "avatar": "",
     *          "label": "这里是个人简介"
     *      }
     *  }
     */
    public function updateLabel(Request $request)
    {

    }

    /**
     * @api {get} /v1/user/follow_list 我关注的用户列表
     * @apiVersion 1.0.0
     * @apiName 我关注的用户列表
     * @apiGroup User
     * @apiPermission 需要登录
     *
     * @apiSuccess {Numeric} id 用户ID
     * @apiSuccess {String} email 邮件信息
     * @apiSuccess {String} nickname 昵称
     * @apiSuccess {String} avatar 头像
     * @apiSuccess {String} label 个人简介
     *
     * @apiSuccessExample Success-Response
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": [{
     *          "id": 1001,
     *          "email": "hello@qq.com",
     *          "nickname": "这里是用户昵称",
     *          "avatar": "图片URL",
     *          "label": "这里是个人简介/标签"
     *      }, {
     *          "id": 1002,
     *          "email": "hello@qq.com",
     *          "nickname": "你好",
     *          "avatar": "",
     *          "label": "啊这里是个人简介"
     *      }]
     *  }
     */
    public function myFollowUserList(Request $request)
    {

    }

    /**
     * @api {post} /v1/user/set_follow 关注某用户
     * @apiVersion 1.0.0
     * @apiName 关注某用户
     * @apiGroup User
     * @apiPermission 需要登录
     * 
     * @apiParam {numeric} follow_user_id 需要关注的用户ID
     *
     * @apiParamExample Request-Example
     * {
     *      "follow_user_id": 1001
     * }
     *
     * @apiSuccessExample Success-Response
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": 1
     *  }
     */
    public function setFollowUser(Request $request)
    {

    }

    /**
     * @api {post} /v1/user/cancel_follow 取关某用户
     * @apiVersion 1.0.0
     * @apiName 取关某用户
     * @apiGroup User
     * @apiPermission 需要登录
     *
     * @apiParam {numeric} follow_user_id 取消关注的用户ID
     *
     * @apiParamExample Request-Example
     * {
     *      "follow_user_id": 1001
     * }
     *
     * @apiSuccessExample Success-Response
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": 1
     *  }
     */
    public function cancelFollowUser(Request $request)
    {

    }
}
