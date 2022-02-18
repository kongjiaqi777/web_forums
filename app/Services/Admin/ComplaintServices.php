<?php

namespace App\Services\Admin;

use App\Services\BaseServices;
use App\Repositories\ComplaintRepository;
use App\Libs\UtilLib;

class ComplaintServices extends BaseServices
{
    private $complaintRepos;

    public function __construct(ComplaintRepository $complaintRepos)
    {
        $this->complaintRepos = $complaintRepos;
    }

    /**
     * 广播投诉列表
     * @param [type] $params
     * @return void
     */
    public function getPostComplaintList($params)
    {
        $params['complaint_type'] = [10, 20];
        $res = $this->complaintRepos->getList($params);
        return $this->joinDisplayCode($res, 'complaint_verify_status');
    }

    /**
     * 广场主投诉列表
     * @param [type] $params
     * @return void
     */
    public function getUserComplaintList($params)
    {
        $params['complaint_type'] = [30];
        $res = $this->complaintRepos->getList($params);
        return $this->joinDisplayCode($res, 'owner_complaint_verify_status');
    }

    /**
     * 处理广播投诉
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function dealPost($params, $operationInfo)
    {
        return $this->complaintRepos->dealPostComplaint($params, $operationInfo);
    }

    /**
     * 处理广场主投诉
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function dealSquareOwner($params, $operationInfo)
    {
        return $this->complaintRepos->dealSquareOwnerComplaint($params, $operationInfo);
    }

    /**
     * 投诉详情
     * @param [type] $params
     * @return void
     */
    public function detail($params)
    {
        return $this->complaintRepos->detail($params);
    }

    private function joinDisplayCode($res, $codeType)
    {
        $list = $res['list'] ?? [];
        if ($list) {
            foreach ($list as &$info) {
                $info['verify_status_display'] = UtilLib::getConfigByCode(
                    $info['verify_status'],
                    'display.'.$codeType,
                    'desc'
                );
            }
            $res['list'] = $list;
        }
        return $res;
    }
}