<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MessageServices;

class MessageController extends Controller
{
    private $messageServices;

    public function __construct(MessageServices $messageServices)
    {
        $this->messageServices = $messageServices;
    }

    /**
     * @api {get} /v1/message/list 我的消息列表
     * @apiVersion 1.0.0
     * @apiName 我的消息列表
     * @apiGroup User
     * @apiPermission 必须登录
     *
     * @apiSuccess {Numeric} id 消息ID
     * @apiSuccess {Numeric} msg_type 消息类型
     * @apiSuccess {String}  msg_body 消息内容
     * @apiSuccess {String} msg_title 消息标题
     * @apiSuccess {String} url 跳转链接
     * @apiSuccess {Boolean} is_read 是否已读：0否/1是
     * @apiSuccess {DateTime} created_at 收信时间
     *
     * @apiSuccessExample Success-Response
     * {
            "code": 0,
            "msg": "success",
            "info": {
                "list": [
                    {
                        "id": 2,
                        "template_id": 69,
                        "user_id": 118,
                        "msg_type": 10,
                        "msg_body": "您的《留学广场》广场创建申请已通过",
                        "msg_title": "广场创建",
                        "url": null,
                        "is_read": 0,
                        "created_at": "2022-02-09T11:14:44.000000Z",
                        "updated_at": "2022-02-09T11:14:44.000000Z",
                        "is_del": 0,
                        "deleted_at": null
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
    public function myMessageList(Request $request)
    {
        $params = $request->only(['page', 'perpage']);
        $operationInfo = $this->getOperationInfo($request);
        $params ['user_id'] = $operationInfo['operator_id'] ?? 0;
        $res = $this->messageServices->myMessageList($params);
        return $this->buildSucceed($res);
    }

    /**
     * @api {get} /v1/message/detail 我的消息详情
     * @apiVersion 1.0.0
     * @apiName 我的消息详情
     * @apiGroup User
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} message_id 消息ID
     *
     * @apiSuccess {Numeric} id 消息ID
     * @apiSuccess {Numeric} msg_type 消息类型
     * @apiSuccess {String}  msg_body 消息内容
     * @apiSuccess {String} msg_title 消息标题
     * @apiSuccess {String} url 跳转链接
     * @apiSuccess {Boolean} is_read 是否已读：0否/1是
     * @apiSuccess {DateTime} created_at 收信时间
     *
     * @apiSuccessExample Success-Response
     * {
    "code": 0,
    "msg": "success",
    "info": {
        "id": 2,
        "template_id": 69,
        "user_id": 118,
        "msg_type": 10,
        "msg_body": "您的《留学广场》广场创建申请已通过",
        "msg_title": "广场创建",
        "url": null,
        "is_read": 0,
        "created_at": "2022-02-09T11:14:44.000000Z",
        "updated_at": "2022-02-09T11:14:44.000000Z",
        "is_del": 0,
        "deleted_at": null
    }
}
     */ 
    public function detail(Request $request)
    {
        $this->validate($request,[
            'message_id' => 'required'
        ], [
            'message_id.*' => '消息ID必传'
        ]);
        $params = $request->only(['message_id']);
        $res = $this->messageServices->detail($params);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/message/read 读消息
     * @apiVersion 1.0.0
     * @apiName 读消息
     * @apiGroup User
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} message_id 消息ID
     */
    public function read(Request $request)
    {
        $this->validate($request,[
            'message_id' => 'required'
        ], [
            'message_id.*' => '消息ID必传'
        ]);
        $params = $request->only(['message_id']);
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->messageServices->read($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/message/delete 删除消息
     * @apiVersion 1.0.0
     * @apiName 删除消息
     * @apiGroup User
     * @apiPermission 必须登录
     */
    public function delete(Request $request)
    {
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->messageServices->delete($operationInfo);
        return $this->buildSucceed($res);
    }
}
