<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReplyServices;

class ReplyController extends Controller
{
    private $replyServices;

    public function __construct(ReplyServices $replyServices)
    {
        $this->replyServices = $replyServices;
    }
    /**
     * @api {post} /v1/reply/list 评论列表
     * @apiVersion 1.0.0
     * @apiName 评论列表
     * @apiGroup PostReply
     *
     * @apiPermission 允许不登录
     *
     * @apiParam {Numeric} post_id 广播ID
     *
     * @apiSuccess {Numeric} id 评论ID
     * @apiSuccess {Numeric} reply_type 评论类型：广播的评论reply_type=10/对reply_type=10的评论,reply_type=20/对reply_type=20的评论,reply_type=30
     * @apiSuccess {Numeric} reply_count 本条评论的回复数目
     * @apiSuccess {Numeric} user_id 评论用户ID
     * @apiSuccess {String} user_nickname 评论用户昵称
     * @apiSuccess {Numeric} parent_user_id 回复某某用户ID
     * @apiSuccess {String} parent_user_name 回复某某用户昵称(reply_type=30时展示，回复某某)
     * @apiSuccess {String} content 评论内容
     * @apiSuccess {DateTime} created_at 评论时间
     * @apiSuccess {Numeric} praise_count 本条评论点赞数目
     * @apiSuccess {Array} sub_reply_list 本条评论下的回复列表，列表格式和评论列表一致
     * @apiSuccess {Array} sub_reply_pagination 本条评论下的回复列表分页数据，默认展示五条，当sub_reply_pagination.total_count>5的时候，需要额外请求展示更多的评论回复
     * @apiSuccessExample Success-Response
     * {
    "code": 0,
    "msg": "success",
    "info": {
        "list": [
            {
                "id": 1,
                "reply_type": 10,
                "parent_id": 0,
                "parent_user_id": 0,
                "reply_count": 1,
                "praise_count": 0,
                "post_id": 10006,
                "user_id": 113,
                "content": "说得好",
                "created_at": "2022-02-01T14:28:28.000000Z",
                "updated_at": "2022-02-02T15:59:02.000000Z",
                "is_del": 0,
                "deleted_at": null,
                "first_reply_id": 0,
                "user_nickname": "立秋",
                "sub_reply_list": [
                    {
                        "id": 4,
                        "reply_type": 20,
                        "parent_id": 3,
                        "parent_user_id": 115,
                        "reply_count": 0,
                        "praise_count": 0,
                        "post_id": 10006,
                        "user_id": 114,
                        "content": "并不赞同",
                        "created_at": "2022-02-02T15:59:02.000000Z",
                        "updated_at": "2022-02-02T15:59:02.000000Z",
                        "is_del": 0,
                        "deleted_at": null,
                        "first_reply_id": 1,
                        "user_nickname": "处暑",
                        "parent_user_name": "白露"
                    },
                    {
                        "id": 3,
                        "reply_type": 20,
                        "parent_id": 1,
                        "parent_user_id": 113,
                        "reply_count": 0,
                        "praise_count": 0,
                        "post_id": 10006,
                        "user_id": 115,
                        "content": "怎么讲",
                        "created_at": "2022-02-02T15:56:39.000000Z",
                        "updated_at": "2022-02-02T15:57:39.000000Z",
                        "is_del": 0,
                        "deleted_at": null,
                        "first_reply_id": 1,
                        "user_nickname": "白露",
                        "parent_user_name": "立秋"
                    }
                ],
                "sub_reply_pagination": {
                    "page": 1,
                    "perpage": 5,
                    "total_page": 1,
                    "total_count": 2
                }
            }
        ],
        "pagination": {
            "page": 1,
            "perpage": 50,
            "total_page": 1,
            "total_count": 1
        }
    }
}
     */
    public function list(Request $request)
    {
        $this->validate($request, [
            'post_id' => 'required',
        ], [
            'post_id.*' => '广播ID必传'
        ]);
        $params = $request->all();
        $res = $this->replyServices->getList($params);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/reply/create 添加广播的评论
     * @apiVersion 1.0.0
     * @apiName 添加评论
     * @apiGroup PostReply
     *
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} post_id 广播ID
     * @apiParam {String} content 评论内容
     * 
     * @apiParamExample Request-Example 评论广播
     * {
     *      "post_id": 1001,
     *      "content": "说得很好"
     * }
     *
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'post_id' => 'required',
            'content' => 'required'
        ], [
            'post_id.*' => '广播ID必传',
            'content.*' => '评论内容必传'
        ]);
        $params = $request->only(['post_id', 'content']);

        $operationInfo = $this->getOperationInfo($request);
        return $this->replyServices->create($params, $operationInfo);
    }

    /**
     * @api {post} /v1/reply/create_sub 回复广播下面的评论
     * @apiVersion 1.0.0
     * @apiName 回复广播下面的评论
     * @apiGroup PostReply
     *
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} post_id 广播ID
     * @apiParam {String} content 评论内容
     * @apiParam {String} reply_id 回复哪条评论就传哪条评论的ID
     * 
     * @apiParamExample Request-Example 回复广播评论
     * {
     *      "post_id": 1001,
     *      "content": "说得很好",
     *      "reply_id": 1001
     * }
     */
    public function createSub(Request $request)
    {
        $this->validate($request, [
            'post_id' => 'required',
            'content' => 'required',
            'reply_id' => 'required',
        ], [
            'post_id.*' => '广播ID必传',
            'content.*' => '评论内容必传',
            'reply_id.*' => '评论ID必传'
        ]);
        $params = $request->only(['post_id', 'content', 'reply_id']);

        $operationInfo = $this->getOperationInfo($request);
        return $this->replyServices->createSub($params, $operationInfo);
    }

    /**
     * @api {post} /v1/reply/delete 删除评论
     * @apiVersion 1.0.0
     * @apiName 删除评论
     * @apiGroup PostReply
     *
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} post_id 广播ID
     * @apiParam {Numeric} reply_id 评论ID
     *
     */
    public function delete(Request $request)
    {
        $this->validate($request, [
            'post_id' => 'required',
            'reply_id' => 'required',
        ], [
            'post_id.*' => '广播ID必传',
            'reply_id.*' => '评论ID必传'
        ]);
        $params = $request->only(['post_id', 'reply_id']);

        $operationInfo = $this->getOperationInfo($request);
        return $this->replyServices->delete($params, $operationInfo);
    }
}
