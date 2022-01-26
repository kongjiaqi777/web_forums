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
}