<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SquareServices;

class SquareController extends Controller
{
    private $squareServices;

    public function __construct(SquareServices $squareServices)
    {
        $this->squareServices = $squareServices;
    }

    /**
     * @api {GET} /v1/square/suggest 根据名称模糊搜索广场列表
     * @apiVersion 1.0.0
     * @apiName 广场列表
     * @apiGroup Square
     * @apiPermission 允许不登录[用户未登录is_follow全部为0]
     *
     * @apiParam {Numeric} [page=1]     页码
     * @apiParam {Numeric} [perpage=20] 每页条数
     * @apiParam {String}  name         广场名称或标签
     * 
     * @apiParamExample {curl} Request Example
     * curl 'http://forums.test/v1/square/suggest'
     * @apiSuccess {Numeric} id          广场ID
     * @apiSuccess {String}  name        广场名称
     * @apiSuccess {String}  avatar      广场头像
     * @apiSuccess {String}  label       广场标签
     * @apiSuccess {Numeric} is_follow   当前登录用户是否关注[0未关注/1已关注，用户未登录统一为0]
     * @apiSuccess {DateTime} created_at 创建时间
     * @apiSuccessExample Success-Response
     *
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": [
     *          {
     *              "id": 1000,
     *              "name": "留学广场",
     *              "avatar": "广场头像"
     *          }
     *      ]
     * }
     *
     */
    public function suggest(Request $request)
    {

    }

      /**
     * @api {GET} /v1/square/detail 广场详情
     * @apiVersion 1.0.0
     * @apiName 广场详情
     * @apiGroup Square
     * @apiPermission 允许不登录[用户未登录is_follow全部为0]
     * @apiParam {Numeric} square_id    广场ID
     * 
     * @apiParamExample {curl} Request Example
     * curl 'http://forums.test/v1/square/detail'
     * @apiSuccess {Numeric} id          广场ID
     * @apiSuccess {Numeric} creater_id  创建人ID
     * @apiSuccess {String}  name        广场名称
     * @apiSuccess {String}  avatar      广场头像
     * @apiSuccess {String}  label       广场简介
     * @apiSuccess {Numeric} follow_count 成员人数
     * @apiSuccess {DateTime} created_at 创建时间
     * @apiSuccess {Numeric} is_follow   当前登录用户是否关注[0未关注/1已关注，用户未登录统一为0]
     * @apiSuccess {string} verify_status 审核状态:申请创建100/审核通过200/审核驳回300/申请更换广场主400/申请解除500
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
        $this->validate($request, [
            'square_id' => 'required|numeric|min:1'
        ], [
            'square_id.required' => '广场ID必传',
            'square_id.numeric' => '广场ID格式不正确'
        ]);
        $params = $request->only(['square_id']);

        $squareId = $params['square_id'] ?? 0;
        $userId = $_REQUEST['user_id'] ?? 0;

        $res = $this->squareServices->getDetail($squareId, $userId);
        return $this->buildSucceed($res);
    }

    /**
     * @api {GET} /v1/square/my_follow_list 我关注的广场列表
     * @apiVersion 1.0.0
     * @apiName 我关注的广场列表
     * @apiGroup Square
     * @apiPermission 需要登录
     *
     * @apiParam {Numeric} [page=1]     页码
     * @apiParam {Numeric} [perpage=20] 每页条数
     * 
     * @apiParamExample {curl} Request Example
     * curl 'http://forums.test/v1/square/my_follow_list'
     * @apiSuccess {Numeric} id          广场ID
     * @apiSuccess {String}  name        广场名称
     * @apiSuccess {String}  avatar      广场头像
     * @apiSuccessExample Success-Response
     *
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": [
     *          {
     *              "id": 1000,
     *              "name": "留学广场",
     *              "avatar": "广场头像"
     *          }
     *      ]
     * }
     *
     */
    public function myFollowList(Request $request)
    {
    }

  
    /**
     * @api {post} /v1/square/create 创建广场
     * @apiVersion 1.0.0
     * @apiName 创建广场
     * @apiGroup Square
     * @apiPermission 需要登录
     *
     * @apiParam {String}  name        广场名称
     * @apiParam {String}  avatar      广场头像
     * @apiParam {String}  label       广场标签
     *
     * @apiParamExample Request-Example
     * {
     *      "name": "留学广场",
     *      "avatar": "头像链接",
     *      "label": "这是广场的标签"
     * }
     *
     * @apiSuccess {numeric} square_id code=0即成功，失败code=-1
     *
     * @apiSuccessExample Success-Response
     * {
     *      "code": 0,
     *      "msg": "success",
     *      "info": 1003
     *  }
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'avatar' => 'required',
            'label' => 'required',
        ], [

        ]);
        $params = $request->all();
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->squareServices->createSquare($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/square/update 修改广场信息
     * @apiVersion 1.0.0
     * @apiName 修改广场
     * @apiGroup Square
     * @apiPermission 需要登录
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
        $res = $this->squareServices->updateSquare($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/square/set_follow 关注某广场
     * @apiVersion 1.0.0
     * @apiName 关注广场
     * @apiGroup Square
     * @apiPermission 需要登录
     *
     * @apiParam {Numeric} square_id    广场ID
     * @apiParamExample Request-Example
     * {
     *      "square_id": 1001
     * }
     *
     * @apiSuccess info code=0即成功，失败code=-1
     * @apiSuccessExample Success-Response
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": 2
     *  }
     */
    public function setFollow(Request $request)
    {
        $params = $request->all();
        // $operationInfo = $this->getOperationInfo($request);
        $squareId = $params['square_id'] ?? 0;
        $userId = $params['user_id'] ?? 0;

        $res = $this->squareServices->setFollow($squareId, $userId);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/square/cancel_follow 取消关注广场
     * @apiVersion 1.0.0
     * @apiName 取消关注广场
     * @apiGroup Square
     * @apiPermission 需要登录
     * 
     * @apiParam {Numeric} square_id    广场ID
     * @apiParamExample Request-Example
     * {
     *      "square_id": 1001
     * }
     *
     * @apiSuccessExample Success-Response
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": "取消关注成功"
     *  }
     */
    public function cancelFollow(Request $request)
    {
        $params = $request->all();
        $squareId = $params['square_id'] ?? 0;
        $userId = $params['user_id'] ?? 0;

        $res = $this->squareServices->cancelFollow($squareId, $userId);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/square/apply_relieve 广场主申请卸任
     * @apiVersion 1.0.0
     * @apiName 广场主申请卸任
     * @apiGroup Square
     * @apiPermission 需要登录
     *
     * @apiParam {Numeric} square_id    广场ID
     * @apiParamExample Request-Example
     * {
     *      "square_id": 1001
     * }
     *
     * @apiSuccessExample Success-Response
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": {
     *          "square_id": 1001,
     *          "verify_status": 400
     *      }
     *  }
     */
    public function applyRelieve(Request $request)
    {
        
    }
}
