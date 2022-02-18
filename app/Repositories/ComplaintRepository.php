<?php

namespace App\Repositories;

use App\Exceptions\NoStackException;
use App\Libs\MessageLib;
use App\Repositories\BaseRepository;
use App\Repositories\PostRepository;
use App\Repositories\ReplyRepository;
use App\Models\Complaint\ComplaintModel;
use App\Models\Complaint\ComplaintOpLogModel;
use App\Models\User\UserModel;
use App\Models\User\UserOpLogModel;
use Carbon\Carbon;
use Log;
use DB;

class ComplaintRepository extends BaseRepository
{
    private $complaintModel;
    private $complaintOpLogModel;
    private $userModel;
    private $userOpLogModel;
    private $postRepos;
    private $replyRepos;

    public function __construct(
        ComplaintModel $complaintModel,
        ComplaintOpLogModel $complaintOpLogModel,
        UserModel $userModel,
        UserOpLogModel $userOpLogModel,
        PostRepository $postRepos,
        ReplyRepository $replyRepos
    ) {
        $this->complaintModel = $complaintModel;
        $this->complaintOpLogModel = $complaintOpLogModel;
        $this->userModel = $userModel;
        $this->userOpLogModel = $userOpLogModel;
        $this->replyRepos = $replyRepos;
        $this->postRepos = $postRepos;
    }

    /**
     * 投诉列表
     * @param [type] $params
     * @return void
     */
    public function getList($params)
    {
        return $this->complaintModel->getList($params);
    }

    /**
     * 投诉详情
     * @param [type] $params
     * @return void
     */
    public function detail($params)
    {
        $complaintId = $params ['complaint_id'] ?? 0;
        return $this->complaintModel->getById($complaintId);
    }

    /**
     * 创建投诉
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function create($params, $operationInfo)
    {
        if ($params['complaint_type'] == config('display.complaint_type.post.code')) {
            $postId = $params['post_id'] ?? 0;
            $postInfo = $this->postRepos->getById($postId);
            $params['square_id'] = $postInfo['square_id'] ?? 0;
        }

        if ($params['complaint_type'] == config('display.complaint_type.reply.code')) {
            $replyId = $params['reply_id'] ?? 0;
            $replyInfo = $this->replyRepos->getById($replyId);
            $params['post_id'] = $replyInfo['post_id'] ?? 0;
            $postInfo = $this->postRepos->getById($params['post_id']);
            $params['square_id'] = $postInfo['square_id'] ?? 0;
        }

        return $this->commonCreate(
            $this->complaintModel,
            $params,
            $this->complaintOpLogModel,
            $operationInfo,
            '创建投诉'
        );
    }

    /**
     * 处理广播投诉
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function dealPostComplaint($params, $operationInfo)
    {
        $complaintId = $params['complaint_id'] ?? 0;
        $operationCode = $params['verify_status'] ?? 0;

        $complaintInfo = $this->complaintModel->getById($complaintId);
        $complaintType = $complaintInfo['complaint_type'] ?? 0;
        $complaintStatus = $complaintInfo ['verify_status'] ?? 0;

        if ($complaintStatus != config('display.complaint_verify_status.undeal.code') || !in_array($complaintType, [
            config('display.complaint_type.post.code'),
            config('display.complaint_type.reply.code'),
        ])) {
            throw New NoStackException('当前投诉状态不正确或处理投诉类型和方法不正确');
        }

        switch($operationCode) {
            case config('display.complaint_verify_status_op.reject.code'):
                // 驳回投诉
                return $this->dealRejectPost($complaintId, $complaintInfo, $params, $operationInfo);
            case config('display.complaint_verify_status_op.deleted_only.code'):
                // 删除帖子或回复
                return $this->dealDeletePost($complaintId, $complaintInfo, $params, $operationInfo);
            case config('display.complaint_verify_status_op.deleted_and_forbidden7days.code'):
                // 删除帖子或回复并禁言七天
                $complaintStatus = config('display.complaint_verify_status.forbidden.code');
                return $this->dealForbidPost($complaintId, $complaintInfo, $params, $operationInfo, $complaintStatus, 0);
            case config('display.complaint_verify_status_op.deleted_and_forbiddenforever.code'):
                // 删除帖子或回复并永久禁言
                $complaintStatus =config('display.complaint_verify_status.forbidden_forever.code');
                return $this->dealForbidPost($complaintId, $complaintInfo, $params, $operationInfo, $complaintStatus, 1);
        }
    }

    /**
     * 驳回投诉广播/回复
     * @param [type] $complaintId 投诉ID
     * @param [array] $params 参数
     * @param [type] $operationInfo 操作人信息
     * @return void
     */
    public function dealRejectPost($complaintId, $complaintInfo, $params, $operationInfo)
    {
        return DB::transaction(function () use ($complaintId, $params, $complaintInfo, $operationInfo) {
            try {
                MessageLib::sendMessage(
                    config('display.msg_type.complaint_reject.code'),
                    [$complaintInfo['user_id']],
                    [
                        'user_id' =>  $complaintInfo['complaint_user_id'],
                    ]
                );

                return $this->commonUpdate(
                    $complaintId,
                    $this->complaintModel,
                    $this->complaintOpLogModel,
                    [
                        'verify_status' => config('display.complaint_verify_status.over.code'),
                        'verify_reason' => $params['verify_reason'] ?? ''
                    ],
                    $operationInfo
                );
                
            } catch (\Exception $e) {
                Log::error(sprintf('处理投诉失败:[ComplaintId][%s][Param][%s][Code][%s][Message][%s]', $complaintId, json_encode($params), $e->getCode(), $e->getMessage()));
                throw New NoStackException('处理投诉失败');
            }
        });
    }

    /**
     * 删除广播/回复
     * @param [type] $complaintId 投诉ID
     * @param [array] $params 参数
     * @param [type] $operationInfo 操作人信息
     * @return void
     */
    public function dealDeletePost($complaintId, $complaintInfo, $params ,$operationInfo)
    {
        return DB::transaction(function () use ($complaintId, $complaintInfo, $params ,$operationInfo) {
            try {
                $complaintType = $complaintInfo['complaint_type'] ?? 0;
                if ($complaintType == config('display.complaint_type.post.code')) {
                    // 删除广播
                    $postId = $complaintInfo['post_id'] ?? 0;
                    $this->postRepos->delete(['post_id' => $postId], $operationInfo, null);
                    $msgType = config('display.msg_type.complaint_post_delete.code');
                } else if ($complaintType == config('display.complaint_type.reply.code')) {
                    // 删除回复
                    $replyId = $complaintInfo['reply_id'] ?? 0;
                    $this->replyRepos->delete(['reply_id' => $replyId], $operationInfo);
                    $msgType = config('display.msg_type.complaint_reply_delete.code');
                }
        
                // 投诉人消息
                MessageLib::sendMessage(
                    config('display.msg_type.complaint_deal.code'),
                    [$complaintInfo['user_id']],
                    [
                        'user_id' => $complaintInfo['complaint_user_id'],
                    ]
                );
        
                // 被投诉人消息
                MessageLib::sendMessage(
                    $msgType,
                    [$complaintInfo['complaint_user_id']],
                    [
                        'reply_id' => $complaintInfo['reply_id'] ?? 0,
                        'post_id' => $complaintInfo['post_id'] ?? 0,
                    ]
                );

                // 修改投诉
                return $this->commonUpdate(
                    $complaintId,
                    $this->complaintModel,
                    $this->complaintOpLogModel,
                    [
                        'verify_status' => config('display.complaint_verify_status.deleted.code'),
                        'verify_reason' => $params ['verify_reason'] ?? ''
                    ],
                    $operationInfo
                );
            } catch (\Exception $e) {
                Log::error(sprintf('处理投诉失败:[ComplaintId][%s][Param][%s][Code][%s][Message][%s]', $complaintId, json_encode($params), $e->getCode(), $e->getMessage()));
                throw New NoStackException('处理投诉失败');
            }
        });
       
    }

    /**
     * 删除广播/回复并禁言
     * @param [type] $complaintId 投诉ID
     * @param [array] $params 参数
     * @param [type] $operationInfo 操作人信息
     * @param [type] $complaintStatus 修改之后的状态
     * @param int $forbiddenType 0:禁言七天/1:永久禁言
     * @return void
     */
    public function dealForbidPost($complaintId, $complaintInfo, $params, $operationInfo, $complaintStatus, $forbiddenType=1)
    {
        return DB::transaction(function () use ($complaintId, $operationInfo, $params, $complaintStatus, $complaintInfo, $forbiddenType) {
            try {
                $complaintUserId = $complaintInfo['complaint_user_id'] ?? 0;
                $complaintType = $complaintInfo['complaint_type'] ?? 0;

                if ($complaintType == config('display.complaint_type.post.code')) {
                    $postId = $complaintInfo['post_id'] ?? 0;
                    // 删除广播
                    $this->postRepos->delete(['post_id' => $postId], $operationInfo, null);
                    if ($forbiddenType) {
                        $msgType = config('display.msg_type.complaint_post_forbidden_forever.code');
                    } else {
                        $msgType = config('display.msg_type.complaint_post_forbidden.code');
                    }
                } else if ($complaintType == config('display.complaint_type.reply.code')) {
                    $replyId = $complaintInfo['reply_id'] ?? 0;
                    // 删除回复
                    $this->replyRepos->delete(['reply_id' => $replyId], $operationInfo);
                    if ($forbiddenType) {
                        $msgType = config('display.msg_type.complaint_reply_forbidden_forever.code');
                    } else {
                        $msgType = config('display.msg_type.complaint_reply_forbidden.code');
                    }
                }

                // 投诉人消息
                MessageLib::sendMessage(
                    config('display.msg_type.complaint_deal.code'),
                    [$complaintInfo['user_id']],
                    [
                        'user_id' => $complaintUserId,
                    ]
                );

                // 被投诉人消息
                MessageLib::sendMessage(
                    $msgType,
                    [$complaintUserId],
                    [
                        'reply_id' => $complaintInfo['reply_id'] ?? 0,
                        'post_id' => $complaintInfo['post_id'] ?? 0,
                    ]
                );

                if ($forbiddenType) {
                    $forbiddenEnd = '2999-12-31 00:00:00';
                } else {
                    $forbiddenEnd = Carbon::today()->addDays(7)->toDateTimeString();
                }

                // 用户禁言
                $this->commonUpdate(
                    $complaintUserId,
                    $this->userModel,
                    $this->userOpLogModel,
                    [
                        'status' => config('display.user_status.forbidden.code'),
                        'forbidden_end' => $forbiddenEnd
                    ],
                    $operationInfo,
                    '因投诉禁言'
                );

                // 修改投诉
                return $this->commonUpdate(
                    $complaintId,
                    $this->complaintModel,
                    $this->complaintOpLogModel,
                    [
                        'verify_status' => $complaintStatus,
                        'verify_reason' => $params ['verify_reason'] ?? ''
                    ],
                    $operationInfo
                );
            } catch (\Exception $e) {
                Log::error(sprintf('处理投诉失败:[ComplaintId][%s][Param][%s][Code][%s][Message][%s]', $complaintId, json_encode($params), $e->getCode(), $e->getMessage()));
                throw New NoStackException('处理投诉失败');
            }
        });
    }

    /**
     * 处理广场主投诉
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function dealSquareOwnerComplaint($params, $operationInfo)
    {
        $complaintId = $params['complaint_id'] ?? 0;
        $operationCode = $params['verify_status'] ?? 0;
        $complaintInfo = $this->complaintModel->getById($complaintId);

        $complaintType = $complaintInfo['complaint_type'] ?? 0;
        $complaintStatus = $complaintInfo ['verify_status'] ?? 0;
        if ($complaintType !=  config('display.complaint_type.square_owner.code') || $complaintStatus != config('display.owner_complaint_verify_status.undeal.code')) {
            throw New NoStackException('当前投诉状态不正确或处理投诉类型和方法不正确');
        }

        switch($operationCode) {
            case config('display.owner_complaint_verify_op.reject.code'):
                // 驳回
                return $this->dealRejectOwner($complaintId, $complaintInfo, $params, $operationInfo);
            case config('display.owner_complaint_verify_op.warning.code'):
                // 警告广场主
                return $this->dealWarningOwner($complaintId, $complaintInfo, $params, $operationInfo);
            default:
                throw New NoStackException('操作类型不合法');
        }
    }

    public function dealWarningOwner($complaintId, $complaintInfo, $params, $operationInfo)
    {
        return DB::transaction(function () use ($complaintId, $params, $complaintInfo, $operationInfo) {
            try {
                // 投诉人消息
                MessageLib::sendMessage(
                    config('display.msg_type.complaint_deal.code'),
                    [$complaintInfo['user_id']],
                    [
                        'user_id' =>  $complaintInfo['complaint_user_id'],
                    ]
                );

                // 被投诉人消息
                MessageLib::sendMessage(
                    config('display.msg_type.owner_warning.code'),
                    [$complaintInfo['complaint_user_id']],
                    [
                        'square_id' => $complaintInfo['square_id'] ?? 0,
                    ]
                );

                return $this->commonUpdate(
                    $complaintId,
                    $this->complaintModel,
                    $this->complaintOpLogModel,
                    [
                        'verify_status' => config('display.owner_complaint_verify_status.warning.code'),
                        'verify_reason' => $params['verify_reason'] ?? ''
                    ],
                    $operationInfo
                );
                
            } catch (\Exception $e) {
                Log::error(sprintf('处理投诉失败:[ComplaintId][%s][Param][%s][Code][%s][Message][%s]', $complaintId, json_encode($params), $e->getCode(), $e->getMessage()));
                throw New NoStackException('处理投诉失败');
            }
        });
    }

    public function dealRejectOwner($complaintId, $complaintInfo, $params, $operationInfo)
    {
        return DB::transaction(function () use ($complaintId, $params, $complaintInfo, $operationInfo) {
            try {
                // 投诉人消息
                MessageLib::sendMessage(
                    config('display.msg_type.complaint_reject.code'),
                    [$complaintInfo['user_id']],
                    [
                        'user_id' =>  $complaintInfo['complaint_user_id'],
                    ]
                );

                return $this->commonUpdate(
                    $complaintId,
                    $this->complaintModel,
                    $this->complaintOpLogModel,
                    [
                        'verify_status' => config('display.owner_complaint_verify_status.over.code'),
                        'verify_reason' => $params['verify_reason'] ?? ''
                    ],
                    $operationInfo
                );
                
            } catch (\Exception $e) {
                Log::error(sprintf('处理投诉失败:[ComplaintId][%s][Param][%s][Code][%s][Message][%s]', $complaintId, json_encode($params), $e->getCode(), $e->getMessage()));
                throw New NoStackException('处理投诉失败');
            }
        });
    }
}