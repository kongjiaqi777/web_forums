<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Redis;
use App\Services\UserServices;

class UserController extends Controller
{
    private $userServices;

    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }

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
     *      "nickname": "霜"
     * }
     *
     * @apiSuccess {Numeric} id 用户ID
     * @apiSuccess {String} email 邮件信息
     * @apiSuccess {String} nickname 昵称
     * @apiSuccess {String} avatar 头像
     * @apiSuccess {String} label 个人简介
     * @apiSuccess {Boolean} is_follow 当前登录用户是否关注[0未关注/1已关注，用户未登录统一为0]
     * @apiSuccess {Boolean} is_mutual 当前登录用户是否相互关注[0否/1是，用户未登录统一为0]
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
                "email": "test18@123.com",
                "is_mutual": 0,
                "is_follow": 0
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
    public function suggestUser(Request $request)
    {
        $params = $request->all();
        $res = $this->userServices->suggestUser($params);
        return $this->buildSucceed($res);
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
     * @apiSuccess {Numeric} unread_message_count 未读消息数目
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
    public function getUserInfoByToken(Request $request)
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
        $this->validate($request, [
            'id' => 'required',
            'label' => 'required'
        ], [
            'id.*' => '用户ID必传',
            'label.*' => '标签必传'
        ]);
        $params = $request->only(['id', 'label']);
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->userServices->update($params, $operationInfo);
        return $this->buildSucceed($res);
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
     * @apiSuccess {Boolean} is_mutual 是否互相关注:1是/0否
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
                "email": "test18@123.com",
                "is_mutual": 0
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
    public function myFollowUserList(Request $request)
    {
        $params = $request->all();
        $res = $this->userServices->myFollowUserList($params);
        return $this->buildSucceed($res);
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

    /**
     * @api {get} /v1/user/fans_list 关注我的用户列表
     * @apiVersion 1.0.0
     * @apiName 关注我的用户列表
     * @apiGroup User
     * @apiPermission 需要登录
     *
     * @apiSuccess {Numeric} id 用户ID
     * @apiSuccess {String} email 邮件信息
     * @apiSuccess {String} nickname 昵称
     * @apiSuccess {String} avatar 头像
     * @apiSuccess {String} label 个人简介
     * @apiSuccess {Boolean} is_mutual 是否互相关注:1是/0否
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
                "email": "test18@123.com",
                "is_mutual": 0
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
    public function myFansUserList()
    {

    }

    /**
     * @api {get} /v1/user/get_by_id 根据用户ID获取详情
     * @apiVersion 1.0.0
     * @apiName GetUserInfoById
     * @apiGroup User
     * @apiPermission 允许不登录[用户未登录is_follow全部为0]
     *
     * @apiParam {Numeric} id 用户ID
     *
     * @apiSuccess {Numeric} id 用户ID
     * @apiSuccess {String} email 邮件信息
     * @apiSuccess {String} nickname 昵称
     * @apiSuccess {String} avatar 头像
     * @apiSuccess {String} label 个人简介
     * @apiSuccess {Boolean} is_follow 当前登录用户是否关注[0未关注/1已关注，用户未登录统一为0]
     * @apiSuccess {Boolean} is_mutual 当前登录用户是否相互关注[0否/1是，用户未登录统一为0]
     * 
     *
     * @apiSuccessExample Success-Response
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": {
     *          "id": 1001,
     *          "email": "hello@qq.com",
     *          "nickname": "啊你好",
     *          "avatar": "",
     *          "label": "这里是个人简介"
     *      }
     *  }
     */
    public function getUserInfoById(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ], [
            'id.*' => '用户ID必传'
        ]);
        $params = $request->only('id');
        $userId = $params ['id'] ?? 0;

        $res = $this->userServices->getById($userId);
        return $this->buildSucceed($res);
    }
}
