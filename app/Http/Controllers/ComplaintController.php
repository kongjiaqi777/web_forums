<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ComplaintServices;

class ComplaintController extends Controller
{
    private $complaintServices;

    public function __construct(ComplaintServices $complaintServices)
    {
        $this->complaintServices = $complaintServices;
    }

    /**
     * @api {get} /v1/complaint/detail 投诉详情
     * @apiVersion 1.0.0
     * @apiName 投诉详情
     * @apiGroup Complaint
     *
     * @apiParam {Numeric} complaint_id 投诉ID
     *
     * @apiSuccess {Numeric} id 投诉ID
     * @apiSuccess {Numeric} post_id 广播ID
     * @apiSuccess {Numeric} reply_id 评论ID
     * @apiSuccess {Numeric} square_id 广场ID
     * @apiSuccess {Numeric} complaint_user_id 被投诉人
     * @apiSuccess {Numeric} complaint_type 投诉类型:广播投诉10/评论投诉20
     * @apiSuccess {String} content 投诉内容
     * @apiSuccess {String} photo 投诉图片URL
     * @apiSuccess {Numeric} verify_status 投诉审核状态
     * @apiSuccess {String} verify_reason 审核理由
     */
    public function detail(Request $request)
    {
        $this->validate($request, [
            'complaint_id' => 'required',
        ], [
            'complaint_id.*' => '投诉ID必传'
        ]);

        $params = $request->only(['complaint_id']);
        $detail = $this->complaintServices->detail($params);
        return $this->buildSucceed($detail);
    }

    /**
     * @api {post} /v1/complaint/create 投诉广播/评论
     * @apiVersion 1.0.0
     * @apiName 投诉广播/评论
     * @apiGroup Complaint
     *
     * @apiParam {Numeric} [post_id] 广播ID，如果投诉类型是广播投诉必传
     * @apiParam {Numeric} [reply_id] 评论ID，如果投诉类型是评论投诉必传
     * @apiParam {Numeric} [square_id] 广场ID，如果投诉是广场主投诉必传
     * @apiParam {Numeric} complaint_user_id 被投诉人ID
     * @apiParam {Numeric} complaint_type 投诉类型:广播投诉10/评论投诉20/广场主投诉30
     * @apiParam {String} content 投诉内容
     * @apiParam {String} photo 投诉图片URL
     *
     * @apiParamExample 广播投诉
     * {
     *      "post_id": 1001,
     *      "complaint_user_id" : 1(发广播那个人ID)
     *      "complaint_type:10
     * }
     * 
     * @apiParamExample 评论投诉
     * {
     *      "reply_id": 1001,
     *      "complaint_user_id" : 1(发评论那个人ID)
     *      "complaint_type:20
     * }
     * 
     * @apiParamExample 广场主投诉
     * {
     *      "square_id": 1001,
     *      "complaint_user_id" : 1(广场主ID)
     *      "complaint_type:30
     * }
     *
     * @apiSuccess complaint_id 投诉成功返回投诉ID
     */
    public function create(Request $request)
    {
        $complaintTypes = data_get(config('display.complaint_type'), '*.code');
        $this->validate($request, [
            'complaint_user_id' => 'required|numeric|min:1',
            'complaint_type' => 'required|numeric|in:'.implode(',', $complaintTypes),
            'post_id' => 'required_if:complaint_type,10',
            'reply_id' => 'required_if:complaint_type,20',
            'square_id' => 'required_if:complaint_type,30',
            'content' => 'required|string',
            'photo' => 'string',
        ], [
            'complaint_user_id.*' => '被投诉用户ID必传',
            'complaint_type.*' => '投诉类型必传',
            'post_id.*' => '广播ID必传',
            'reply_id.*' => '回复ID必传',
            'square_id.*' => '广场ID必传',
            'content.*' => '内容必传',
            'photo.*' => '投诉图片格式不正确',
        ]);
        $params = $request->only([
            'post_id',
            'reply_id',
            'square_id',
            'complaint_type',
            'complaint_user_id',
            'content',
            'photo',
        ]);
        $operationInfo = $this->getOperationInfo($request);
        $detail = $this->complaintServices->create($params, $operationInfo);
        return $this->buildSucceed($detail);
    }
}
