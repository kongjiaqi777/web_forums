<?php

namespace App\Services\Admin;

use App\Repositories\ComplaintRepository;
use App\Repositories\UserRepository;
use App\Libs\UtilLib;

class ComplaintServices
{
    private $complaintRepos;
    private $userRepos;

    public function __construct(
        ComplaintRepository $complaintRepos,
        UserRepository $userRepos
    ) {
        $this->complaintRepos = $complaintRepos;
        $this->userRepos = $userRepos;
    }

    /**
     * 广播投诉列表
     * @param [type] $params
     * @return void
     */
    public function getPostComplaintList($params)
    {
        $params['complaint_type_in'] = [10, 20];
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
        $params['complaint_type'] = 30;
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
            $joinUserIdA = array_column($list, 'complaint_user_id');
            $joinUserIdB = array_column($list, 'user_id');
            $userIds = array_merge($joinUserIdA, $joinUserIdB);
            $userNameList = [];
            if ($userIds) {
                $userIds = array_unique($userIds);
                $userNameList = $this->userRepos->getUserNameList($userIds);
                $userNameList = UtilLib::indexBy($userNameList, 'id');
            }
           
            foreach ($list as &$info) {
                $info['verify_status_display'] = UtilLib::getConfigByCode(
                    $info['verify_status'],
                    'display.'.$codeType,
                    'desc'
                );

                $complaintUserId = $info['complaint_user_id'] ?? 0;
                $info['complaint_user_email'] = $userNameList[$complaintUserId]['email'] ?? '';

                $userId = $info['user_id'] ?? 0;
                $info['user_email'] = $userNameList[$userId]['email'] ?? '';
            }

            $res['list'] = $list;
        }
        return $res;
    }
}