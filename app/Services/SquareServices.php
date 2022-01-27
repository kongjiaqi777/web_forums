<?php

namespace App\Services;

use App\Repositories\SquareRepository;

class SquareServices extends BaseServices
{

    private $squareRepos;
    private $squareStatusEffective;

    public function __construct(
        SquareRepository $squareRepos
    ) {
        $this->squareRepos = $squareRepos;
        $this->squareStatusEffective = [
            config('display.square_verify_status.approved.code'),
            config('display.square_verify_status.dismissed.code'),
        ];
    }

    /**
     * 创建广场
     */
    public function createSquare($params, $operationInfo)
    {
        return $this->squareRepos->createSquare($params, $operationInfo);
    }

    /**
     * 更新广场
     */
    public function updateSquare($params, $operationInfo)
    {
        return $this->squareRepos->updateSquare($params, $operationInfo);
    }

    /**
     * 广场详情
     */
    public function getDetail($squareId, $operatorId)
    {
        return $this->squareRepos->detail($squareId, true, true, $operatorId);
    }

    /**
     * 关注广场
     */
    public function setFollow($squareId, $operatorId)
    {
        return $this->squareRepos->setFollow($squareId, $operatorId);
    }

    /**
     * 取关广场
     */
    public function cancelFollow($squareId, $operatorId)
    {
        return $this->squareRepos->cancelFollow($squareId, $operatorId);
    }

    /**
     * 广场模糊查询
     */
    public function suggestList($params, $isJoinFollow=false, $operatorId=0)
    {
        $params['is_del'] = 0;
        $params['verify_status'] = $this->squareStatusEffective;

        return $this->squareRepos->suggest($params, $isJoinFollow, $operatorId);
    }

    /**
     * 我关注的广场
     */
    public function myFollowList($params, $operatorId)
    {
        return $this->squareRepos->myFollowList($params, $operatorId);
    }
}