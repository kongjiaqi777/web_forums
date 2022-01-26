<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PraiseServices;

class PraiseController extends Controller
{
    private $praiseServices;

    public function __construct(PraiseServices $praiseServices)
    {
        $this->praiseServices = $praiseServices;
    }

    /**
     * @api {post} /v1/praise/create_post 点赞广播
     * @apiVersion 1.0.0
     * @apiName 点赞广播
     * @apiGroup PostPraise
     *
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} post_id 广播ID
     */
    public function createPost(Request $request)
    {
        $params = $request->all();
        $operationInfo = $this->getOperationInfo($request);
        return $this->praiseServices->createPost($params, $operationInfo);
    }

    /**
     * @api {post} /v1/praise/cancel_post 取消点赞广播
     * @apiVersion 1.0.0
     * @apiName 取消点赞广播
     * @apiGroup PostPraise
     *
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} post_id 广播ID
     */
    public function cancelPost(Request $request)
    {
        $params = $request->all();
        $operationInfo = $this->getOperationInfo($request);
        return $this->praiseServices->cancelPost($params, $operationInfo);
    }

    /**
     * @api {post} /v1/praise/create_reply 点赞评论
     * @apiVersion 1.0.0
     * @apiName 点赞评论
     * @apiGroup PostPraise
     *
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} reply_id 评论ID
     */
    public function createReply(Request $request)
    {
        $params = $request->all();
        $operationInfo = $this->getOperationInfo($request);
        return $this->praiseServices->createReply($params, $operationInfo);
    }

    /**
     * @api {post} /v1/praise/cancel_reply 取消点赞评论
     * @apiVersion 1.0.0
     * @apiName 取消点赞评论
     * @apiGroup PostPraise
     *
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} reply_id 评论ID
     */
    public function cancelReply(Request $request)
    {
        $params = $request->all();
        $operationInfo = $this->getOperationInfo($request);
        return $this->praiseServices->cancelReply($params, $operationInfo);
    }
}
