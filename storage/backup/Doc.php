<?php
    /**
     * @api {GET} /v1/square/list 广场列表
     * @apiVersion 1.0.0
     * @apiName 广场列表
     * @apiGroup Square
     *
     * @apiParam {Numeric} [page=1]     页码
     * @apiParam {Numeric} [perpage=20] 每页条数
     * @apiParam {Numeric} square_id    广场ID
     * 
     * @apiParamExample {curl} Request Example
     * curl 'http://forums.test/v1/square/list'
     * @apiSuccess {Numeric} id          广场ID
     * @apiSuccess {String}  name        广场名称
     * @apiSuccess {String}  avatar      广场头像
     * @apiSuccess {String}  label       广场简介
     * @apiSuccess {DateTime} created_at 创建时间
     * @apiSuccess {Numeric} is_follow   当前登录用户是否关注[0未关注/1已关注，用户未登录统一为0]
     * @apiSuccessExample Success-Response
     *
     *  {
     *      "code": 0,
     *      "msg": "success",
     *      "info": {
     *           "list": [
     *              {
     *                  "id": 1000,
     *                  "name": "测试广场",
     *                  "creater_id": 1001,
     *                  "avatar": null,
     *                  "profile": "",
     *                  "verify_status": 100,
     *                  "verify_reason": "通过审核",
     *                  "follow_count": 0,
     *                  "created_at": "2022-01-09T15:04:39.000000Z",
     *                  "updated_at": "2022-01-09T15:04:43.000000Z",
     *                  "deleted_at": null,
     *                  "is_del": 0
     *              }
     *          ],
     *          "pagination": {
     *              "page": 1,
     *              "perpage": 50,
     *              "total_page": 1,
     *              "total_count": 1
     *          }
     *      }
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