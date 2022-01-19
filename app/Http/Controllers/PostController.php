<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PostServices;

class PostController extends Controller
{

    private $postServices;

    public function __construct(PostServices $postServices)
    {
        $this->postServices = $postServices;
    }

    /**
     * @api {get} /v1/post/list 广播列表
     * @apiVersion 1.0.0
     * @apiName 广播列表
     * @apiGroup Post
     * @apiPermission 允许不登录
     *
     * @apiParam {Numeric} [page=1] 页码，默认值1
     * @apiParam {Numeric} [perpage=20] 每页条数
     * @apiParam {Numeric} [square_id]  广场ID，筛选广场下的广播必传这个字段
     * @apiParam {Numeric} [post_type] 广播类型:广场广播10/个人广播20
     *
     * @apiParamExample 首页广场广播列表
     * {
     *      "page": 1,
     *      "perpage": 20,
     *      "post_type": 10
     * }
     *
     * @apiParamExample 首页个人广播列表
     * {
     *      "page": 1,
     *      "perpage": 20,
     *      "post_type": 20
     * }
     *
     * @apiParamExample 某广场下的全部广播
     * {
     *      "page": 1,
     *      "perpage": 20,
     *      "square_id": 1001,
     *      "post_type": 10
     * }
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
    public function list(Request $request)
    {
        $params = $request->all();

        $params ['page'] ?? $params ['page'] = 1;
        $params ['perpage'] ?? $params ['perpage'] = 20;

        $res = $this->postServices->getList($params);
        return $this->buildSucceed($res);
    }

    /**
     * @api {get} /v1/post/my_list 我的广播列表
     * @apiVersion 1.0.0
     * @apiName 我的广播列表
     * @apiGroup Post
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} [page=1] 页码，默认值1
     * @apiParam {Numeric} [perpage=20] 每页条数
     * @apiParam {Numeric} [post_type] 广播类型:广场广播10/个人广播20
     *
     * @apiParamExample 我的广场广播列表
     * {
     *      "page": 1,
     *      "perpage": 20,
     *      "post_type": 10
     * }
     *
     * @apiParamExample 我的个人广播列表
     * {
     *      "page": 1,
     *      "perpage": 20,
     *      "post_type": 20
     * }
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
    public function getMyPostList(Request $request)
    {

    }

    /**
     * @api {get} /v1/post/suggest 广播标题/内容模糊搜索
     * @apiVersion 1.0.0
     * @apiName 广播标题/内容模糊搜索
     * @apiGroup Post
     * @apiPermission 允许不登录
     *
     * @apiParam {Numeric} [page=1] 页码，默认值1
     * @apiParam {Numeric} [perpage=20] 每页条数
     * @apiParam {String} name 广播标题或内容
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

    }

    /**
     * @api {post} /v1/post/create 发广播
     * @apiVersion 1.0.0
     * @apiName 发广播
     * @apiGroup Post
     * @apiPermission 必须登录
     *
     * @apiName {Numeric} square_id  所属广场ID
     * @apiName {String} title       标题
     * @apiName {String} content     内容
     * @apiName {String} photo       图片
     *
     * @apiParamExample Request-Example
     * {
     *      "square_id": 1001,
     *      "title": "第一个广播",
     *      "content": "广播内容",
     *      "photo":"photoUrl"
     * }
     *
     * @apiSuccess {numeric} post_id code=0即成功，失败code=-1
     *
     * @apiSuccessExample Success-Response
     * {
     *      "code": 0,
     *      "msg": "success",
     *      "info": 1003
     *  }
     *
     * @apiErrorExample Error-Response
     * {
     *      "code": -1,
     *      "msg": "广场信息有误，无法创建"
     *  }
     */
    public function create(Request $request)
    {
        $params = $request->all();
        $params['creater_id'] ?? $params['creater_id'] = 1001;
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->postServices->createPost($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/post/update 更改广播
     * @apiVersion 1.0.0
     * @apiName 更改广播
     * @apiGroup Post
     * @apiPermission 必须登录
     *
     * @apiName {Numeric} post_id    广播ID
     * @apiName {String} title       标题
     * @apiName {String} content     内容
     * @apiName {String} photo       图片
     *
     * @apiParamExample Request-Example
     * {
     *      "square_id": post_id,
     *      "title": "第一个广播",
     *      "content": "广播内容",
     *      "photo":"photoUrl"
     * }
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
     * @apiSuccess {Numeric} my_praise      我是否点赞：0未点赞/1已点赞
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
    public function update(Request $request)
    {

    }

    /**
     * @api {get} /v1/post/top_list 广场主置顶管理
     * @apiVersion 1.0.0
     * @apiName 广场主置顶管理
     * @apiGroup Post
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} square_id 广场ID
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
    public function getTopList(Request $request)
    {

    }

    /**
     * @api {post} /v1/post/set_top 广场主置顶广播
     * @apiVersion 1.0.0
     * @apiName 广场主置顶广播
     * @apiGroup Post
     * @apiPermission 必须登录
     * 
     * @apiParam {Numeric} post_id 广播ID
     * @apiParam {Numeric} post_id 被置顶的广播ID
     * @apiSuccessExample Success-Response
     * {
     *      "code": 0,
     *      "msg": "success",
     *      "info": 1003
     *  }
     */
    public function setTop(Request $request)
    {

    }

    /**
     * @api {post} /v1/post/delete 删除广播
     * @apiVersion 1.0.0
     * @apiName 删除广播
     * @apiGroup Post
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
    public function delete(Request $request)
    {
    }

    /**
     * @api {get} /v1/post/detail 广播详情
     * @apiVersion 1.0.0
     * @apiName 广播详情
     * @apiGroup Post
     * @apiPermission 允许不登录[不登录my_praise默认是0]
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
     * @apiSuccess {Numeric} my_praise      我是否点赞：0未点赞/1已点赞
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
        $params = $request->all();
        $res = $this->postServices->detailPost($params);
        return $this->buildSucceed($res);
    }

    /**
     * @api {get} /v1/post/browse_list 我的历史浏览记录
     * @apiVersion 1.0.0
     * @apiName 我的历史浏览记录
     * @apiGroup Post
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} [page=1] 页码，默认值1
     * @apiParam {Numeric} [perpage=20] 每页条数
     *
     * @apiParamExample {curl} Request Example
     * curl 'http://forums.test/v1/post/browse_list'
     *
     * @apiSuccess {Numeric} id         广播ID
     * @apiSuccess {String} suqare_name 所属广场名称
     * @apiSuccess {String} title       标题
     * @apiSuccess {String} content     内容
     * @apiSuccess {String} photo       图片
     * @apiSuccess {Numeric} reply_count    回复数目
     * @apiSuccess {Numeric} praise_count   点赞数目
     * @apiSuccess {Numeric} is_praise      我是否点赞：0未点赞/1已点赞
     * @apiSuccess {DateTime} browsed_at 最近浏览时间
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
    public function browseList(Request $request)
    {
    }

}
