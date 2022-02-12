<?php

namespace App\Services;

use App\Services\BaseServices;
use App\Repositories\ComplaintRepository;

class ComplaintServices extends BaseServices
{
    private $complaintRepos;

    public function __construct(ComplaintRepository $complaintRepos)
    {
        $this->complaintRepos = $complaintRepos;
    }

    /**
     * 根据ID获取投诉详情
     * @param [type] $params
     * @return void
     */
    public function detail($params)
    {
        return $this->complaintRepos->detail($params);
    }

    /**
     * 创建投诉
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function create($params, $operationInfo)
    {
        $params['user_id'] = $operationInfo['operator_id'];
        return $this->complaintRepos->create($params, $operationInfo);
    }
}
