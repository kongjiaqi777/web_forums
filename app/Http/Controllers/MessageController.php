<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessageController extends Controller
{
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
     */ 
    public function myMessageList(Request $request)
    {

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
     */ 

    /**
     * @api {post} /v1/message/read 读消息
     * @apiVersion 1.0.0
     * @apiName 读消息
     * @apiGroup User
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} message_id 消息ID
     */
    
}
