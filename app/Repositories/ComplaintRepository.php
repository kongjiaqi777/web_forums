<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Models\Complaint\ComplaintModel;
use App\Models\Complaint\ComplaintOpLogModel;

class ComplaintRepository extends BaseRepository
{
    private $complaintModel;
    private $complaintOpLogModel;

    public function __construct(
        ComplaintModel $complaintModel,
        ComplaintOpLogModel $complaintOpLogModel
    ) {
        $this->complaintModel = $complaintModel;
        $this->complaintOpLogModel = $complaintOpLogModel;
    }

    public function getList($params)
    {
        return $this->complaintModel->getList($params);
    }

    public function detail($params)
    {
        $complaintId = $params ['complaint_id'] ?? 0;
        return $this->complaintModel->getById($complaintId);
    }

    public function create($params, $operationInfo)
    {
        return $this->commonCreate(
            $this->complaintModel,
            $params,
            $this->complaintOpLogModel,
            $operationInfo,
            '创建投诉'
        );
    }

    /**
     * 根据处理投诉的类型返回处理之后的类型
     * @param numeric $operationCode 处理的操作类型
     * @return numeric operationCode 处理后的状态类型
     */
    public function getAfterStatus($operationCode)
    {
        switch($operationCode) {
            case config('display.complaint_verify_status_op.reject.code'):
                // 驳回
                // 正常
                return config('display.complaint_verify_status.over.code');
                break;
            case config('display.complaint_verify_status_op.deleted_only.code'):
                // 删除帖子或回复
                // 已删帖
                return config('display.complaint_verify_status.deleted.code');
                break;
            case config('display.complaint_verify_status_op.deleted_and_forbidden7days.code'):
                // 删除帖子或回复并禁言七天
                // 禁言中
                return config('display.complaint_verify_status.forbidden.code');
                break;
            
            case config('display.complaint_verify_status_op.deleted_and_forbiddenforever.code'):
                // 删除帖子或回复并永久禁言
                // 永久禁言
                return config('display.complaint_verify_status.forbidden_forever.code');
                break;
        }
    }

    public function dealPostComplaint($params, $operationInfo)
    {
        
    }
}