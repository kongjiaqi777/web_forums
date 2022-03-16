<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\PostServices;

class AdminPostController extends Controller
{
    private $postServices;

    public function __construct(PostServices $postServices)
    {
        $this->postServices = $postServices;
    }

    /**
     * @api {get} /v1/admin/post/list 管理端广播列表
     * @apiVersion 1.0.0
     * @apiName 管理端广播列表
     * @apiGroup AdminPost
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} [page=1] 页码，默认值1
     * @apiParam {Numeric} [perpage=20] 每页条数
     * @apiParam {Numeric} [square_id]  广场ID
     * @apiParam {Numeric} [post_id] 广播ID
     * @apiParam {Numeric} [post_type] 广播类型:广场广播10/个人广播20
     * @apiParam {DateTime} [created_start] 创建开始时间
     * @apiParam {DateTime} [created_end] 创建结束时间
     * @apiParam {Numeric} [is_del] 是否删除
     * @apiParam {Numeric} [top_rule_select] 置顶规则
     *
     * @apiSuccess {Numeric} id         广播ID
     * @apiSuccess {Numeric} square_id  所属广场ID
     * @apiSuccess {String} suqare_name 所属广场名称
     * @apiSuccess {Numeric} post_type 广播类型
     * @apiSuccess {String} title       标题
     * @apiSuccess {String} content     内容
     * @apiSuccess {String} photo       图片
     * @apiSuccess {Numeric} creater_id 创建人ID
     * @apiSuccess {String} creater_email 创建人账号
     * @apiSuccess {Numeric} top_rule       置顶规则
     * @apiSuccess {Datetime} created_at 创建时间
     * @apiSuccess {Numeric} is_del 是否删除
     * @apiSuccessExample Success-Response
     * {
     *      "code": 0,
     *      "msg": "success",
     *      "info": [
     *           "list": [
     *              {
     *                  "id": 1000,
     *                  "square_id": 1001,
     *                  "title": "hello",
     *                  "content": "hello",
     *                  "photo": null,
     *                  "creater_id": 1001,
     *                  "top_rule": 1,
     *                  "reply_count": 0,
     *                  "praise_count": 0,
     *                  "created_at": "2022-01-15T22:42:33.000000Z",
     *                  "updated_at": null,
     *                  "deleted_at": null,
     *                  "is_del": 0
     *              }
     *          ],
     *          "pagination": {
     *              "page": 1,
     *              "perpage": 20,
     *              "total_page": 1,
     *              "total_count": 1
     *          }
     *      ]
     * }
     */
    public function list(Request $request)
    {
        $this->validate($request, [
            'square_id' => 'numeric',
            'post_id' => 'numeric',
            'post_type' => 'numeric',
            'created_start' => 'date_format:Y-m-d H:i:s',
            'created_end' => 'date_format:Y-m-d H:i:s',
            'is_del'  => 'numeric|in:0,1',
            'top_rule_select' => 'numeric',
        ]);

        $params = $request->only([
            'page',
            'perpage',
            'square_id',
            'post_id',
            'post_type',
            'created_start',
            'created_end',
            'is_del',
            'top_rule_select',
        ]);
        $res = $this->postServices->getList($params);
        return $this->buildSucceed($res);
    }

    /**
     * @api {get} /v1/admin/post/detail 管理端广播详情
     * @apiVersion 1.0.0
     * @apiName 管理端广播详情
     * @apiGroup AdminPost
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} post_id    广播ID
     *
     * @apiSuccess {Numeric} id         广播ID
     * @apiSuccess {Numeric} square_id  所属广场ID
     * @apiSuccess {String} suqare_name 所属广场名称
     * @apiSuccess {String} title       标题
     * @apiSuccess {String} content     内容
     * @apiSuccess {String} photo       图片
     * @apiSuccess {Numeric} creater_id 创建人ID
     * @apiSuccess {Numeric} creater_name 创建人名称
     * @apiSuccess {Numeric} top_rule       置顶规则
     * @apiSuccess {Numeric} reply_count    回复数目
     * @apiSuccess {Numeric} praise_count   点赞数目
     * @apiSuccessExample Success-Response
     * {
     *      "code": 0,
     *      "msg": "success",
     *      "info": 
     *              {
     *                  "id": 1000,
     *                  "square_id": 1001,
     *                  "title": "hello",
     *                  "content": "hello",
     *                  "photo": null,
     *                  "creater_id": 1001,
     *                  "top_rule": 1,
     *                  "reply_count": 0,
     *                  "praise_count": 0,
     *                  "created_at": "2022-01-15T22:42:33.000000Z",
     *                  "updated_at": null,
     *                  "deleted_at": null,
     *                  "is_del": 0
     *              }
     * }
     */
    public function detail(Request $request)
    {
        $this->validate($request, [
            'post_id' => 'required'
        ], [
            'post_id.*' => '需要广播ID'
        ]);
        $params = $request->all();
        $res = $this->postServices->detail($params);
        return $this->buildSucceed($res);
    }

     /**
     * @api {post} /v1/admin/post/set_top 管理端置顶广播
     * @apiVersion 1.0.0
     * @apiName 广场主置顶广播
     * @apiGroup AdminPost
     * @apiPermission 必须登录
     * 
     * @apiParam {Numeric} post_id 被置顶的广播ID
     * @apiParam {Boolean} [homepage_top=1] 首页置顶必传
     * @apiSuccessExample Success-Response
     * {
     *      "code": 0,
     *      "msg": "success",
     *      "info": 1003
     *  }
     */
    public function setTop(Request $request)
    {
        $this->validate($request, [
            'post_id' => 'required'
        ], [
            'post_id.*' => '需要广播ID'
        ]);
        $params = $request->only(['post_id', 'homepage_top']);
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->postServices->setTop($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/admin/post/delete_post 管理端删除广播
     * @apiVersion 1.0.0
     * @apiName 管理端删除广播
     * @apiGroup AdminPost
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} post_id 广播ID
     *
     * @apiParam {Numeric} post_id 被删除的广播ID
     * @apiSuccessExample Success-Response
     * {
     *      "code": 0,
     *      "msg": "success",
     *      "info": 1003
     *  }
     */
    public function deletePost(Request $request)
    {
        $this->validate($request, [
            'post_id' => 'required'
        ], [
            'post_id.*' => '需要广播ID'
        ]);
        $params = $request->all();
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->postServices->delete($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/admin/post/delete_reply 管理端删除评论
     * @apiVersion 1.0.0
     * @apiName 管理端删除评论
     * @apiGroup AdminPost
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} reply_id 回复ID
     *
     * @apiParam {Numeric} reply_id 被删除的回复ID
     * @apiSuccessExample Success-Response
     * {
     *      "code": 0,
     *      "msg": "success",
     *      "info": 1003
     *  }
     */
    public function deleteReply(Request $request)
    {
        $this->validate($request, [
            'reply_id' => 'required'
        ], [
            'reply_id.*' => '需要回复ID'
        ]);
        $params = $request->all();
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->postServices->deleteReply($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {get} /v1/admin/post/suggest 管理端广播标题模糊搜索
     * @apiVersion 1.0.0
     * @apiName 管理端广播标题模糊搜索
     * @apiGroup AdminPost
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} [page=1] 页码，默认值1
     * @apiParam {Numeric} [perpage=20] 每页条数
     * @apiParam {String} name 广播标题
     *
     * @apiSuccess {Numeric} id         广播ID
     * @apiSuccess {Numeric} square_id  所属广场ID
     * @apiSuccess {String} suqare_name 所属广场名称
     * @apiSuccess {Numeric} post_type 广播类型
     * @apiSuccess {String} title       标题
     * @apiSuccess {String} content     内容
     * @apiSuccess {String} photo       图片
     * @apiSuccess {Numeric} creater_id 创建人ID
     * @apiSuccess {Numeric} creater_name 创建人名称
     * @apiSuccess {Numeric} top_rule       置顶规则
     * @apiSuccess {Numeric} reply_count    回复数目
     * @apiSuccess {Numeric} praise_count   点赞数目
     * @apiSuccess {Numeric} is_praise      我是否点赞：0未点赞/1已点赞
     * @apiSuccessExample Success-Response
     * {
     *      "code": 0,
     *      "msg": "success",
     *      "info": [
     *           "list": [
     *              {
     *                  "id": 1000,
     *                  "square_id": 1001,
     *                  "title": "hello",
     *                  "content": "hello",
     *                  "photo": null,
     *                  "creater_id": 1001,
     *                  "top_rule": 1,
     *                  "reply_count": 0,
     *                  "praise_count": 0,
     *                  "created_at": "2022-01-15T22:42:33.000000Z",
     *                  "updated_at": null,
     *                  "deleted_at": null,
     *                  "is_del": 0
     *              }
     *          ],
     *          "pagination": {
     *              "page": 1,
     *              "perpage": 20,
     *              "total_page": 1,
     *              "total_count": 1
     *          }
     *      ]
     * }
     */
    public function suggest(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:38|string',
        ], [
            'name.required' => '请输入查询关键字',
            'name.max' => '最多支持输入38个字符',
            'name.string' => '仅支持字符串查询'
        ]);

        $params = $request->only([
            'page',
            'perpage',
            'name'
        ]);

        $res = $this->postServices->suggest($params);
        return $this->buildSucceed($res);
    }
}
