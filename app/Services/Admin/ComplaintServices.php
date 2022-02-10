<?php

namespace App\Services\Admin;

use App\Services\BaseServices;
use App\Repositories\ComplaintRepository;

class ComplaintServices extends BaseServices
{
    private $complaintRepos;

    public function __construct(ComplaintRepository $complaintRepos)
    {
        $this->complaintRepos = $complaintRepos;
    }

    public function getList($params)
    {
        $res = $this->complaintRepos->getList($params);
        return $res;
    }

    public function deal($params, $operationInfo)
    {
        return $this->complaintRepos->dealPostComplaint($params, $operationInfo);
    }

    public function detail($params)
    {
        return $this->complaintRepos->detail($params);
    }
}