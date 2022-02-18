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
     * @apiParamExample Request Example
     * {
            "created_start": "2022-01-20",
            "created_end":"2022-01-21"
        }
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
     * {
        "code": 0,
        "msg": "success",
        "info": {
            "list": [
                {
                    "id": 1005,
                    "name": "直播广场",
                    "creater_id": 118,
                    "avatar": "头像链接",
                    "label": "label",
                    "verify_status": 20,
                    "verify_reason": null,
                    "follow_count": 1,
                    "created_at": "2022-01-20T08:05:27.000000Z",
                    "updated_at": "2022-01-20T13:57:37.000000Z",
                    "deleted_at": null,
                    "is_del": 0,
                    "creater_email": "test18@123.com",
                    "creater_nickname": "霜降",
                    "verify_status_display": "已通过"
                },
                {
                    "id": 1004,
                    "name": "股票广场",
                    "creater_id": 118,
                    "avatar": "hhhhh",
                    "label": "这是一个分享交流股票心得的广场。想一夜暴富吗？",
                    "verify_status": 20,
                    "verify_reason": null,
                    "follow_count": 5,
                    "created_at": "2022-01-20T07:59:23.000000Z",
                    "updated_at": "2022-01-26T14:58:25.000000Z",
                    "deleted_at": null,
                    "is_del": 0,
                    "creater_email": "test18@123.com",
                    "creater_nickname": "霜降",
                    "verify_status_display": "已通过"
                },
                {
                    "id": 1003,
                    "name": "职场讨论",
                    "creater_id": 118,
                    "avatar": "头像链接",
                    "label": "讨论职场奇葩事",
                    "verify_status": 20,
                    "verify_reason": null,
                    "follow_count": 3,
                    "created_at": "2022-01-20T07:53:04.000000Z",
                    "updated_at": "2022-01-20T13:57:43.000000Z",
                    "deleted_at": null,
                    "is_del": 0,
                    "creater_email": "test18@123.com",
                    "creater_nickname": "霜降",
                    "verify_status_display": "已通过"
                },
                {
                    "id": 1002,
                    "name": "外汇广场",
                    "creater_id": 118,
                    "avatar": "头像链接",
                    "label": "这是外汇的广场",
                    "verify_status": 20,
                    "verify_reason": null,
                    "follow_count": 1,
                    "created_at": "2022-01-20T07:51:24.000000Z",
                    "updated_at": "2022-01-20T13:57:31.000000Z",
                    "deleted_at": null,
                    "is_del": 0,
                    "creater_email": "test18@123.com",
                    "creater_nickname": "霜降",
                    "verify_status_display": "已通过"
                },
                {
                    "id": 1001,
                    "name": "留学广场",
                    "creater_id": 118,
                    "avatar": "头像链接",
                    "label": "这是留学的广场",
                    "verify_status": 20,
                    "verify_reason": null,
                    "follow_count": 1,
                    "created_at": "2022-01-20T07:04:24.000000Z",
                    "updated_at": "2022-01-20T13:57:28.000000Z",
                    "deleted_at": null,
                    "is_del": 0,
                    "creater_email": "test18@123.com",
                    "creater_nickname": "霜降",
                    "verify_status_display": "已通过"
                }
            ],
            "pagination": {
                "page": 1,
                "perpage": 20,
                "total_page": 1,
                "total_count": 5
            }
        }
    }
     *
     */
    public function list(Request $request)
    {
        $params = $request->all();
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
     * @apiParamExample Request Example
     * {
            "name": "车"
        }
     * @apiSuccess {Numeric} id          广场ID
     * @apiSuccess {String} name        广场名称
     * @apiSuccessExample Success-Response
     * {
            "code": 0,
            "msg": "success",
            "info": {
                "list": [
                    {
                        "id": 1007,
                        "name": "车车车",
                        "label": "label",
                        "avatar": "车车车",
                        "verify_status": 20,
                        "follow_count": 0,
                        "is_del": 0,
                        "is_follow": 0
                    },
                    {
                        "id": 1006,
                        "name": "车车车",
                        "label": "label",
                        "avatar": "车车车",
                        "verify_status": 50,
                        "follow_count": 0,
                        "is_del": 1,
                        "is_follow": 0
                    }
                ],
                "pagination": {
                    "page": 1,
                    "perpage": 20,
                    "total_page": 1,
                    "total_count": 2
                }
            }
        }
     */
    public function suggest(Request $request)
    {
        $params = $request->all();
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
     * @apiParamExample Request Example
     * {
            "square_id": 1006
        }
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
     * {
            "code": 0,
            "msg": "success",
            "info": {
                "id": 1006,
                "name": "车车车",
                "creater_id": 111,
                "avatar": "车车车",
                "label": "label",
                "verify_status": 50,
                "verify_reason": "名称不能重复",
                "follow_count": 0,
                "created_at": "2022-01-22T12:52:56.000000Z",
                "updated_at": "2022-01-22T14:12:14.000000Z",
                "deleted_at": "2022-01-22 22:12:14",
                "is_del": 1,
                "verify_status_display": "已解散"
            }
        }
     */
    public function detail(Request $request)
    {
        $this->validate($request, [
            'square_id' => 'required|numeric|min:1'
        ], [
            'square_id.*' => '广场ID参数不合法'
        ]);
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
     * @apiParamExample Request Example
        * {
            "square_id": 1007,
            "name": "闲人说车",
            "label": "车辆爱好者，互相交流"
        }
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
     * {
            "code": 0,
            "msg": "success",
            "info": {
                "id": 1007,
                "name": "闲人说车",
                "creater_id": 111,
                "avatar": "车车车",
                "label": "车辆爱好者，互相交流",
                "verify_status": 20,
                "verify_reason": null,
                "follow_count": 0,
                "created_at": "2022-01-22T12:58:04.000000Z",
                "updated_at": "2022-01-27T02:30:27.000000Z",
                "deleted_at": null,
                "is_del": 0
            }
        }
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'square_id' => 'required|numeric|min:1'
        ], [
            'square_id.*' => '广场ID参数不合法'
        ]);
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
        $this->validate($request, [
            'square_id' => 'required|numeric|min:1'
        ], [
            'square_id.*' => '广场ID参数不合法'
        ]);
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
     * @apiName 管理端更换广场主
     * @apiGroup AdminSquare
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} square_id    广场ID
     * @apiParam {Numeric} creater_id   新的用户ID
     */
    public function switch(Request $request)
    {
        $this->validate($request, [
            'square_id' => 'required|numeric|min:1',
            'creater_id' => 'required|numeric|min:1',
        ], [
            'square_id.*' => '广场ID参数不合法',
            'creater_id.*' => '请选择新的广场主',
        ]);
        $params = $request->only([
            'square_id',
            'creater_id'
        ]);
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->squareServices->doSwitch($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/admin/square/reject_switch 管理端更换广场主申请驳回
     * @apiVersion 1.0.0
     * @apiName 管理端更换广场主申请驳回
     * @apiGroup AdminSquare
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} square_id    广场ID
     */
    public function rejectSwitch(Request $request)
    {
        $this->validate($request, [
            'square_id' => 'required|numeric|min:1',
        ], [
            'square_id.*' => '广场ID参数不合法',
        ]);
        $params = $request->only([
            'square_id'
        ]);
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->squareServices->rejectSwitch($params, $operationInfo);
        return $this->buildSucceed($res);
    }
}
