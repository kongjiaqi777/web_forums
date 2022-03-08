<?php

namespace App\Services;

use App\Exceptions\NoStackException;
use App\Repositories\SquareRepository;

class SquareServices
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
        $squareId = $params['square_id'] ?? 0;
        $squareInfo = $this->squareRepos->getById($squareId);

        $squareCreaterId = $squareInfo['creater_id'] ?? 0;
        $operatorId = $operationInfo['operator_id'] ?? 0;

        if ($operatorId != $squareCreaterId) {
            throw New NoStackException('只有广场主可以修改广场信息');
        }

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

    /**
     * 申请卸任广场主
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function applyRelieve($params, $operationInfo)
    {
        $squareId = $params['square_id'] ?? 0;
        $squareInfo = $this->squareRepos->getById($squareId);

        $squareCreaterId = $squareInfo['creater_id'] ?? 0;
        $operatorId = $operationInfo['operator_id'] ?? 0;

        if ($operatorId != $squareCreaterId) {
            throw New NoStackException('只有广场主可以申请卸任');
        }

        $params['verify_status'] = config('display.square_verify_status.apply_relieve.code');
        return $this->squareRepos->updateSquare($params, $operationInfo);
    }

    public function getList($params, $operatorId)
    {
        return $this->squareRepos->getList($params, true, $operatorId);
    }
}