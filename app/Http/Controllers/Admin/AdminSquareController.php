<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\SquareServices;

class AdminSquareController extends Controller
{
    private $squareServices;

    public function __construct(SquareServices $squareServices)
    {
        $this->squareServices = $squareServices;
    }

    /**
     * @api {get} /v1/admin/square/list 管理端广场列表
     * @apiVersion 1.0.0
     * @apiName 管理端广场列表
     * @apiGroup AdminSquare
     * @apiPermission 必须登录
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
     * @apiSuccess {String} verify_status_display 审核状态展示
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
     *              "verify_status": 100,
     *              "verify_status_display":"申请创建"
     *          }
     *      ]
     * }
     *
     */
    public function list(Request $request)
    {
        $params = $request->all();

        $params ['page'] ?? $params ['page'] = 1;
        $params ['perpage'] ?? $params ['perpage'] = 20;

        $res = $this->squareServices->getList($params);
        return $this->buildSucceed($res);
    }

    /**
     * @api {get} /v1/admin/square/suggest 管理端广场名称suggest
     * @apiVersion 1.0.0
     * @apiName 管理端广场名称suggest
     * @apiGroup AdminSquare
     * @apiPermission 必须登录
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
    public function suggest(Request $request)
    {
        $params = $request->all();

        $params ['page'] ?? $params ['page'] = 1;
        $params ['perpage'] ?? $params ['perpage'] = 20;

        $res = $this->squareServices->suggest($params);
        return $this->buildSucceed($res);
    }

    /**
     * @api {get} /v1/admin/square/detail 管理端广场详情
     * @apiVersion 1.0.0
     * @apiName 管理端广场详情
     * @apiGroup AdminSquare
     * @apiPermission 必须登录
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
        $params = $request->all();
        $res = $this->squareServices->detail($params);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/admin/square/update 管理端编辑广场
     * @apiVersion 1.0.0
     * @apiName 管理端编辑广场
     * @apiGroup AdminSquare
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} square_id    广场ID
     * @apiParam {String}  name        广场名称
     * @apiParam {String}  avatar      广场头像
     * @apiParam {String}  label       广场简介
     * 
     * @apiParamExample {curl} Request Example
     * curl 'http://forums.test/v1/square/detail'
     * @apiSuccess {Numeric} id          广场ID
     * @apiSuccess {Numeric} creater_id  创建人ID
     * @apiSuccess {String}  name        广场名称
     * @apiSuccess {String}  avatar      广场头像
     * @apiSuccess {String}  label       广场简介
     * @apiSuccess {Numeric} follow_count 关注人数
     * @apiSuccess {DateTime} created_at 创建时间
     * @apiSuccess {Numeric} is_follow   当前登录用户是否关注[0未关注/1已关注，用户未登录统一为0]
     * @apiSuccess {string} verify_status 审核状态:待审核100/审核通过200/审核驳回300
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
    public function update(Request $request)
    {
        $params = $request->all();
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->squareServices->update($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/admin/square/delete 管理端解散广场
     * @apiVersion 1.0.0
     * @apiName 管理端解散广场
     * @apiGroup AdminSquare
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} square_id    广场ID
     */
    public function delete(Request $request)
    {
        $params = $request->all();
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->squareServices->delete($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/admin/square/approve 管理端广场审核通过
     * @apiVersion 1.0.0
     * @apiName 管理端广场审核通过
     * @apiGroup AdminSquare
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} square_id    广场ID
     */
    public function approve(Request $request)
    {
        $this->validate($request, [
            'square_id' => 'required|numeric|min:1'
        ], [
            'square_id.*' => '广场ID参数不合法'
        ]);
        $params = $request->only(['square_id']);
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->squareServices->doApprove($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/admin/square/reject 管理端广场审核驳回
     * @apiVersion 1.0.0
     * @apiName 管理端广场审核驳回
     * @apiGroup AdminSquare
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} square_id    广场ID
     * @apiParam {String} verify_reason 审核驳回原因
     */
    public function reject(Request $request)
    {
        $this->validate($request, [
            'square_id' => 'required|numeric|min:1'
        ], [
            'square_id.*' => '广场ID参数不合法'
        ]);
        $params = $request->only([
            'square_id',
            'verify_reason',
        ]);
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->squareServices->doReject($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/admin/square/switch 管理端更换广场主
     * @apiVersion 1.0.0
     * @apiName 管理端广场审核驳回
     * @apiGroup AdminSquare
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} square_id    广场ID
     * @apiParam {Numeric} creater_id   新的用户ID
     */
    public function switch(Request $request)
    {
        $this->validate($request, [
            'square_id' => 'required|numeric|min:1'
        ], [
            'square_id.*' => '广场ID参数不合法'
        ]);
        $params = $request->only([
            'square_id',
            'creater_id'
        ]);
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->squareServices->doSwitch($params, $operationInfo);
        return $this->buildSucceed($res);
    }
}
