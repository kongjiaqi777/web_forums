<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\ComplaintServices;

class AdminComplaintController extends Controller
{
    private $complaintServices;

    public function __construct(ComplaintServices $complaintServices)
    {
        $this->complaintServices = $complaintServices;
    }

    /**
     * @api {get} /v1/admin/complaint/post_list 管理端广播/评论投诉列表
     * @apiVersion 1.0.0
     * @apiName 管理端广播投诉列表
     * @apiGroup AdminComplaint
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} [user_id] 投诉人账号
     * @apiParam {Numeric} [post_id] 广播ID
     * @apiParam {Numeric} [reply_id] 评论ID
     * @apiParam {Numeric} [complaint_type] 投诉类型:广播投诉10/评论投诉20/广场主投诉30
     * @apiParam {Numeric} [verify_status] 状态
     * @apiParam {DateTime} [created_start] 创建开始时间
     * @apiParam {DateTime} [created_end] 创建结束时间
     *
     * @apiSuccess {Numeric} id 投诉ID
     * @apiSuccess {Numeric} post_id 广播ID
     * @apiSuccess {Numeric} reply_id 评论ID
     * @apiSuccess {Numeric} square_id 广场ID
     * @apiSuccess {Numeric} complaint_user_id 被投诉人
     * @apiSuccess {String}  complaint_user_email 被投诉人账号
     * @apiSuccess {Numeric} user_id 投诉人
     * @apiSuccess {String}  user_email 投诉人账号
     * @apiSuccess {Numeric} complaint_type 投诉类型:广播投诉10/评论投诉20
     * @apiSuccess {String} content 投诉内容
     * @apiSuccess {String} photo 投诉图片URL
     * @apiSuccess {Numeric} verify_status 投诉审核状态
     * @apiSuccess {String} verify_reason 审核理由
     */
    public function getPostComplaintList(Request $request)
    {
        $params = $request->all();
        $res = $this->complaintServices->getPostComplaintList($params);
        return $this->buildSucceed($res);
    }

    /**
     * @api {get} /v1/admin/complaint/user_list 管理端广场主投诉列表
     * @apiVersion 1.0.0
     * @apiName 管理端广场主投诉列表
     * @apiGroup AdminComplaint
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} [user_id] 投诉人账号
     * @apiParam {Numeric} [post_id] 广播ID
     * @apiParam {Numeric} [reply_id] 评论ID
     * @apiParam {Numeric} [verify_status] 状态
     * @apiParam {DateTime} [created_start] 创建开始时间
     * @apiParam {DateTime} [created_end] 创建结束时间
     *
     * @apiSuccess {Numeric} id 投诉ID
     * @apiSuccess {Numeric} post_id 广播ID
     * @apiSuccess {Numeric} reply_id 评论ID
     * @apiSuccess {Numeric} square_id 广场ID
     * @apiSuccess {Numeric} complaint_user_id 被投诉人
     * @apiSuccess {String}  complaint_user_email 被投诉人账号
     * @apiSuccess {Numeric} user_id 投诉人
     * @apiSuccess {String}  user_email 投诉人账号
     * @apiSuccess {Numeric} complaint_type 投诉类型:广播投诉10/评论投诉20/广场主投诉30
     * @apiSuccess {String} content 投诉内容
     * @apiSuccess {String} photo 投诉图片URL
     * @apiSuccess {Numeric} verify_status 投诉审核状态
     * @apiSuccess {String} verify_reason 审核理由
     */
    public function getUserComplaintList(Request $request)
    {
        $params = $request->all();
        $res = $this->complaintServices->getUserComplaintList($params);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/admin/complaint/deal_post 管理端处理广播类型投诉
     * @apiVersion 1.0.0
     * @apiName 管理端处理广播类型投诉
     * @apiGroup AdminComplaint
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} complaint_id 投诉ID
     * @apiParam {Numeric} verify_status 投诉状态 code=complaint_verify_status_op
     * @apiParam {String}  [verify_reason] 投诉处理原因
     */
    public function dealPost(Request $request)
    {
        $verifyStatusOpCodes = data_get(config('display.complaint_verify_status_op'), '*.code');
        $this->validate($request, [
            'complaint_id' => 'required',
            'verify_status' => 'required|numeric|in:'.implode(',', $verifyStatusOpCodes),
        ], [
            'complaint_id.*' => '投诉ID必传',
            'verify_status.required' => '投诉处理类型必传',
            'verify_status.numeric' => '投诉处理类型错误',
            'verify_status.in' => '投诉处理类型取值范围有误'
        ]);
        $params = $request->only([
            'complaint_id',
            'verify_status',
            'verify_reason',
        ]);
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->complaintServices->dealPost($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {post} /v1/admin/complaint/deal_square_owner 管理端处理广场主投诉
     * @apiVersion 1.0.0
     * @apiName 管理端处理广场主投诉
     * @apiGroup AdminComplaint
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} complaint_id 投诉ID
     * @apiParam {Numeric} verify_status 投诉状态 code=owner_complaint_verify_op
     * @apiParam {String}  [verify_reason] 投诉处理原因
     */
    public function dealSquareOwner(Request $request)
    {
        $verifyStatusOpCodes = data_get(config('display.owner_complaint_verify_op'), '*.code');

        $this->validate($request, [
            'complaint_id' => 'required',
            'verify_status' => 'required|numeric|in:'.implode(',', $verifyStatusOpCodes),
        ], [
            'complaint_id.*' => '投诉ID必传',
            'verify_status.required' => '投诉处理类型必传',
            'verify_status.numeric' => '投诉处理类型错误',
            'verify_status.in' => '投诉处理类型取值范围有误',
        ]);
        $params = $request->only([
            'complaint_id',
            'verify_status',
            'verify_reason',
        ]);
        $operationInfo = $this->getOperationInfo($request);
        $res = $this->complaintServices->dealSquareOwner($params, $operationInfo);
        return $this->buildSucceed($res);
    }

    /**
     * @api {get} /v1/admin/complaint/detail 管理端投诉详情
     * @apiVersion 1.0.0
     * @apiName 管理端投诉详情
     * @apiGroup AdminComplaint
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} complaint_id 投诉ID
     *
     * @apiSuccess {Numeric} id 投诉ID
     * @apiSuccess {String} content 投诉内容
     * @apiSuccess {String} photo 投诉图片URL
     */
    public function detail(Request $request)
    {
        $this->validate($request, [
            'complaint_id' => 'required',
        ], [
            'complaint_id.*' => '投诉ID必传'
        ]);
        $params = $request->only(['complaint_id']);
        $res = $this->complaintServices->detail($params);
        return $this->buildSucceed($res);
    }
}
