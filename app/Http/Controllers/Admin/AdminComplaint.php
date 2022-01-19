<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminComplaintController extends Controller
{
    /**
     * @api {get} /v1/admin/complaint/post_list 广播/评论投诉列表
     * @apiVersion 1.0.0
     * @apiName 广播投诉列表
     * @apiGroup AdminComplaint
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} [user_id] 投诉人账号
     * @apiParam {Numeric} [post_id] 广播ID
     * @apiParam {Numeric} [reply_id] 评论ID
     * @apiParam {Numeric} [complaint_type] 投诉类型:广播投诉10/评论投诉20
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

    }

    /**
     * @api {get} /v1/admin/complaint/user_list 广场主投诉列表
     * @apiVersion 1.0.0
     * @apiName 广场主投诉列表
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
     * @apiSuccess {Numeric} complaint_type 投诉类型:广播投诉10/评论投诉20/广场主投诉30
     * @apiSuccess {String} content 投诉内容
     * @apiSuccess {String} photo 投诉图片URL
     * @apiSuccess {Numeric} verify_status 投诉审核状态
     * @apiSuccess {String} verify_reason 审核理由
     */
    public function getUserComplaintList()
    {

    }

    /**
     * @api {post} /v1/admin/complaint/deal 处理投诉
     * @apiVersion 1.0.0
     * @apiName 处理投诉
     * @apiGroup AdminComplaint
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} complaint_id 投诉ID
     * @apiParam {Numeric} verify_status 投诉状态
     * @apiParam {String}  [verify_reason] 投诉处理原因
     */
    public function deal(Request $request)
    {

    }

    /**
     * @api {post} /v1/admin/complaint/detail 投诉详情
     * @apiVersion 1.0.0
     * @apiName 投诉详情
     * @apiGroup AdminComplaint
     * @apiPermission 必须登录
     *
     * @apiParam {Numeric} complaint_id 投诉ID
     *
     * @apiSuccess {Numeric} id 投诉ID
     * @apiSuccess {String} content 投诉内容
     * @apiSuccess {String} photo 投诉图片URL
     */

}
