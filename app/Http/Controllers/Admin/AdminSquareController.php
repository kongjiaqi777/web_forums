<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSquareController extends Controller
{
    /**
     * @api {get} /v1/admin/square/list 管理端广场列表
     * @apiVersion 1.0.0
     * @apiName 管理端广场列表
     * @apiGroup AdminSquare
     * 
     * @apiParam {Numeric} [page=1]     页码
     * @apiParam {Numeric} [perpage=20] 每页条数
     * @apiParam {Numeric} square_id    广场ID
     * @apiParam {Numeric} creater_id   创建人ID
     * @apiParam {Numeric} verify_status 状态
     * @apiParam {DateTime} created_start 创建开始时间
     * @apiParam {DateTime} created_end 创建结束时间
     *
     * @apiParamExample {curl} Request Example
     * curl 'http://forums.test/v1/admin/square/list'
     * @apiSuccess {Numeric} id          广场ID
     * @apiSuccess {Numeric} creater_id  创建人ID
     * @apiSuccess {String} creater_email 创建人账户
     * @apiSuccess {String} creater_nickname 创建人昵称
     * @apiSuccess {String} name        广场名称
     * @apiSuccess {String} avatar      广场头像
     * @apiSuccess {String} label       广场简介
     * @apiSuccess {Numeric} follow_count 关注人数
     * @apiSuccess {DateTime} created_at 创建时间
     * @apiSuccess {Numeric} verify_status 审核状态:申请创建100/审核通过200/审核驳回300/申请更换广场主400/申请解除500
     * @apiSuccessExample Success-Response
     *
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": [
     *          {
     *              "id": 1000,
     *              "name": "留学广场",
     *              "avatar": "广场头像",
     *              "creater_id": 1011,
     *              "creater_email": "hello@qq.com",
     *              "creater_nickname": "张三",
     *              "label":"留学广场标签",
     *              "follow_count": 100,
     *              "created_at": "2021-12-02 13:00:00",
     *              "verify_status": 100
     *          }
     *      ]
     * }
     *
     */
    public function list(Request $request)
    {
        
    }

    /**
     * @api {get} /v1/admin/square/suggest 广场名称suggest
     * @apiVersion 1.0.0
     * @apiName 广场名称suggest
     * @apiGroup AdminSquare
     * 
     * @apiParam {Numeric} [page=1]     页码
     * @apiParam {Numeric} [perpage=20] 每页条数
     * @apiParam {String} name          广场名称模糊搜索
     *
     * @apiParamExample {curl} Request Example
     * curl 'http://forums.test/v1/admin/square/list'
     * @apiSuccess {Numeric} id          广场ID
     * @apiSuccess {String} name        广场名称
     * @apiSuccessExample Success-Response
     *
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": [
     *          {
     *              "id": 1000,
     *              "name": "留学广场",
     *          }
     *      ]
     * }
     */
    public function suggestName(Request $request)
    {

    }

    /**
     * @api {get} /v1/admin/square/detail 广场详情
     * @apiVersion 1.0.0
     * @apiName 广场详情
     * @apiGroup AdminSquare
     *
     * @apiParam {Numeric} square_id    广场ID
     *
     * @apiParamExample {curl} Request Example
     * curl 'http://forums.test/v1/admin/square/detail'
     * @apiSuccess {Numeric} id          广场ID
     * @apiSuccess {Numeric} creater_id  创建人ID
     * @apiSuccess {String}  name        广场名称
     * @apiSuccess {String}  avatar      广场头像
     * @apiSuccess {String}  label       广场简介
     * @apiSuccess {Numeric} follow_count 关注人数
     * @apiSuccess {DateTime} created_at 创建时间
     * @apiSuccess {Numeric} is_follow   当前登录用户是否关注[0未关注/1已关注，用户未登录统一为0]
     * @apiSuccess {string} verify_status 审核状态:审核状态:申请创建100/审核通过200/审核驳回300/申请更换广场主400/申请解除500
     * @apiSuccess {string} verify_reason 审核不通过的原因
     * @apiSuccessExample Success-Response
     *
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info":
     *          {
     *              "id": 1000,
     *              "creater_id": 1001,
     *              "name": "留学广场",
     *              "avatar": "广场头像",
     *              "label": "广场标签",
     *              "follow_count": 100,
     *              "created_at": "2021-10-02 13:00:00",
     *              "is_follow": 0,
     *              "verify_status": "300",
     *              "verify_reason": "请给出广场详细简介"
     *          }
     * }
     */
    public function detail(Request $request)
    {

    }

    /**
     * @api {post} /v1/admin/square/update 编辑广场
     * @apiVersion 1.0.0
     * @apiName 编辑广场
     * @apiGroup AdminSquare
     */
    public function update(Request $request)
    {

    }

    /**
     * @api {post} /v1/admin/square/delete 解散广场
     * @apiVersion 1.0.0
     * @apiName 解散广场
     * @apiGroup AdminSquare
     *
     * @apiParam {Numeric} square_id    广场ID
     */
    public function delete(Request $request)
    {

    }

    /**
     * @api {post} /v1/admin/square/approve 广场审核通过
     * @apiVersion 1.0.0
     * @apiName 广场审核通过
     * @apiGroup AdminSquare
     *
     * @apiParam {Numeric} square_id    广场ID
     */
    public function approve(Request $request)
    {

    }

    /**
     * @api {post} /v1/admin/square/reject 广场审核驳回
     * @apiVersion 1.0.0
     * @apiName 广场审核驳回
     * @apiGroup AdminSquare
     *
     * @apiParam {Numeric} square_id    广场ID
     * @apiParam {String} verify_reason 审核驳回原因
     */
    public function reject(Request $request)
    {

    }

    /**
     * @api {post} /v1/admin/square/switch 更换广场主
     * @apiVersion 1.0.0
     * @apiName 广场审核驳回
     * @apiGroup AdminSquare
     *
     * @apiParam {Numeric} square_id    广场ID
     * @apiParam {Numeric} creater_id   新的用户ID
     */
    public function switch(Request $request)
    {

    }
}
