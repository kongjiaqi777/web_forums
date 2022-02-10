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

    public function detail($params)
    {
        return $this->complaintRepos->detail($params);
    }

    public function create($params, $operationInfo)
    {
        $params['user_id'] = $operationInfo['operator_id'];
        return $this->complaintRepos->create($params, $operationInfo);
    }
}
