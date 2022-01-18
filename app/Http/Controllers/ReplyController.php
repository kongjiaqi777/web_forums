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
                        "parent_id": 1001,
                        "parent_user_id": 1001,
                        "reply_count": 5,
                        "praise_count": 0,
                        "post_id": 1001,
                        "user_id": 1005,
                        "content": "评论1-第一楼",
                        "created_at": "2022-01-16T18:15:34.000000Z",
                        "is_del": 0,
                        "deleted_at": null,
                        "updated_at": null,
                        "sub_reply_list": [
                            {
                                "id": 5,
                                "reply_type": 20,
                                "parent_id": 1,
                                "parent_user_id": 1005,
                                "reply_count": 0,
                                "praise_count": 0,
                                "post_id": 1001,
                                "user_id": 1002,
                                "content": "第一楼，真的吗",
                                "created_at": "2022-01-16T18:17:45.000000Z",
                                "is_del": 0,
                                "deleted_at": null,
                                "updated_at": null
                            },
                            {
                                "id": 6,
                                "reply_type": 20,
                                "parent_id": 1,
                                "parent_user_id": 1002,
                                "reply_count": 0,
                                "praise_count": 0,
                                "post_id": 1001,
                                "user_id": 1006,
                                "content": "第一楼，是的",
                                "created_at": "2022-01-16T18:18:36.000000Z",
                                "is_del": 0,
                                "deleted_at": null,
                                "updated_at": null
                            },
                            {
                                "id": 7,
                                "reply_type": 20,
                                "parent_id": 1,
                                "parent_user_id": 1005,
                                "reply_count": 0,
                                "praise_count": 0,
                                "post_id": 1001,
                                "user_id": 1007,
                                "content": "第一楼，我觉得不好",
                                "created_at": "2022-01-16T18:19:46.000000Z",
                                "is_del": 0,
                                "deleted_at": null,
                                "updated_at": null
                            },
                            {
                                "id": 11,
                                "reply_type": 20,
                                "parent_id": 1,
                                "parent_user_id": 1007,
                                "reply_count": 0,
                                "praise_count": 0,
                                "post_id": 1001,
                                "user_id": 1010,
                                "content": "666",
                                "created_at": "2022-01-16T18:20:27.000000Z",
                                "is_del": 0,
                                "deleted_at": null,
                                "updated_at": null
                            },
                            {
                                "id": 12,
                                "reply_type": 20,
                                "parent_id": 1,
                                "parent_user_id": 1010,
                                "reply_count": 0,
                                "praise_count": 0,
                                "post_id": 1001,
                                "user_id": 1011,
                                "content": "哦",
                                "created_at": "2022-01-16T18:20:27.000000Z",
                                "is_del": 0,
                                "deleted_at": null,
                                "updated_at": null
                            }
                        ],
                        "sub_reply_pagination": {
                            "page": 1,
                            "perpage": 5,
                            "total_page": 2,
                            "total_count": 7
                        }
                    },
                    {
                        "id": 2,
                        "reply_type": 10,
                        "parent_id": 1001,
                        "parent_user_id": 1001,
                        "reply_count": 3,
                        "praise_count": 0,
                        "post_id": 1001,
                        "user_id": 1002,
                        "content": "评论2-第二楼",
                        "created_at": "2022-01-16T18:16:00.000000Z",
                        "is_del": 0,
                        "deleted_at": null,
                        "updated_at": null,
                        "sub_reply_list": [
                            {
                                "id": 8,
                                "reply_type": 20,
                                "parent_id": 2,
                                "parent_user_id": 1002,
                                "reply_count": 0,
                                "praise_count": 0,
                                "post_id": 1001,
                                "user_id": 1008,
                                "content": "第一楼，我觉得蛮好的",
                                "created_at": "2022-01-16T18:17:45.000000Z",
                                "is_del": 0,
                                "deleted_at": null,
                                "updated_at": null
                            },
                            {
                                "id": 9,
                                "reply_type": 20,
                                "parent_id": 2,
                                "parent_user_id": 1008,
                                "reply_count": 0,
                                "praise_count": 0,
                                "post_id": 1001,
                                "user_id": 1009,
                                "content": "666",
                                "created_at": "2022-01-16T18:20:27.000000Z",
                                "is_del": 0,
                                "deleted_at": null,
                                "updated_at": null
                            },
                            {
                                "id": 10,
                                "reply_type": 20,
                                "parent_id": 2,
                                "parent_user_id": 1009,
                                "reply_count": 0,
                                "praise_count": 0,
                                "post_id": 1001,
                                "user_id": 1010,
                                "content": "666",
                                "created_at": "2022-01-16T18:20:27.000000Z",
                                "is_del": 0,
                                "deleted_at": null,
                                "updated_at": null
                            }
                        ],
                        "sub_reply_pagination": {
                            "page": 1,
                            "perpage": 5,
                            "total_page": 1,
                            "total_count": 3
                        }
                    },
                    {
                        "id": 3,
                        "reply_type": 10,
                        "parent_id": 1001,
                        "parent_user_id": 1001,
                        "reply_count": 1,
                        "praise_count": 0,
                        "post_id": 1001,
                        "user_id": 1003,
                        "content": "评论3-第三楼",
                        "created_at": "2022-01-16T18:16:25.000000Z",
                        "is_del": 0,
                        "deleted_at": null,
                        "updated_at": null,
                        "sub_reply_list": [
                            {
                                "id": 13,
                                "reply_type": 20,
                                "parent_id": 3,
                                "parent_user_id": 1003,
                                "reply_count": 0,
                                "praise_count": 0,
                                "post_id": 1001,
                                "user_id": 1015,
                                "content": "真棒",
                                "created_at": "2022-01-16T18:20:27.000000Z",
                                "is_del": 0,
                                "deleted_at": null,
                                "updated_at": null
                            }
                        ],
                        "sub_reply_pagination": {
                            "page": 1,
                            "perpage": 5,
                            "total_page": 1,
                            "total_count": 1
                        }
                    },
                    {
                        "id": 4,
                        "reply_type": 10,
                        "parent_id": 1001,
                        "parent_user_id": 1001,
                        "reply_count": 1,
                        "praise_count": 0,
                        "post_id": 1001,
                        "user_id": 1004,
                        "content": "评论4-第四楼",
                        "created_at": "2022-01-16T18:17:04.000000Z",
                        "is_del": 0,
                        "deleted_at": null,
                        "updated_at": null,
                        "sub_reply_list": [
                            {
                                "id": 14,
                                "reply_type": 20,
                                "parent_id": 4,
                                "parent_user_id": 1004,
                                "reply_count": 0,
                                "praise_count": 0,
                                "post_id": 1001,
                                "user_id": 1016,
                                "content": "是吗？",
                                "created_at": "2022-01-16T18:20:27.000000Z",
                                "is_del": 0,
                                "deleted_at": null,
                                "updated_at": null
                            }
                        ],
                        "sub_reply_pagination": {
                            "page": 1,
                            "perpage": 5,
                            "total_page": 1,
                            "total_count": 1
                        }
                    }
                ],
                "pagination": {
                    "page": 1,
                    "perpage": 10,
                    "total_page": 1,
                    "total_count": 4
                }
            }
        }
     */
    public function list(Request $request)
    {
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
        
    }
}
