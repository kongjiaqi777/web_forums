<?php

namespace App\Services;

use App\Libs\UtilLib;
use App\Repositories\SquareRepository;
use App\Repositories\FollowRepository;

class SquareServices extends BaseServices
{

    private $squareRepos;
    private $followRepos;
    private $squareStatusEffective;

    public function __construct(
        SquareRepository $squareRepos,
        FollowRepository $followRepos
    ) {
        $this->squareRepos = $squareRepos;
        $this->followRepos = $followRepos;
        $this->squareStatusEffective = [
            config('display.square_verify_status.approved.code'),
            config('display.square_verify_status.dismissed.code'),
        ];
    }

    /**
     * 查询广场列表
     */
    public function getList($params)
    {
        $params['is_del'] = 0;
        $params['verify_status'] = $this->squareStatusEffective;

        return $this->squareRepos->getList($params);
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
    public function getDetail($squareId, $userId)
    {
        return $this->squareRepos->detail($squareId, true, true, $userId);
    }

    /**
     * 关注广场
     */
    public function setFollow($squareId, $userId)
    {
        return $this->squareRepos->setFollow($squareId, $userId);
    }

    /**
     * 取关广场
     */
    public function cancelFollow($squareId, $userId)
    {
        return $this->squareRepos->cancelFollow($squareId, $userId);
    }

    /**
     * 广场模糊查询
     */
    public function suggestList($params)
    {
        $operatorId = $params['operator_id'] ?? 0;
        $params['is_del'] = 0;
        $params['verify_status'] = $this->squareStatusEffective;

        $suggestRes = $this->squareRepos->suggest($params);
        $suggestList = $suggestRes['list'] ?? [];
        if ($operatorId && $suggestList) {
            $suggestList = $this->joinFollowFlag($suggestList, $operatorId);
            $suggestRes['list'] = $suggestList;
        }
        return $suggestRes;
    }

    /**
     * 我关注的广场
     */
    public function myFollowList($params, $userId)
    {
        $page = $param['page'] ?? 1;
        $perpage = $params['perpage'] ?? 20;

        // 当前用户关注广场列表
        $followRes = $this->followRepos->getFollowSquareList($userId, $page, $perpage);

        $followList = $followRes['list'] ?? [];
        if ($followList) {
            $squareIds = array_column($followList, 'square_id');

            $squareList = $this->squareRepos->getAll(['id' => $squareIds, 'is_del' => 0]);
            $squareList = UtilLib::indexBy($squareList, 'id');

            foreach ($followList as &$follow) {
                $squareId = $follow['square_id'];
                $squareDetail = $squareList[$squareId] ?? [
                    // 默认值
                ];

                $follow = $squareDetail;
            }

            $followRes['list'] = $followList;
        }
        return $followRes;
    }

    private function joinFollowFlag($squareList, $userId)
    {
        $squareIds = array_column($squareList, 'id');
    
        $followList = $this->followRepos->getSquareFollowAll([
            'square_id' => $squareIds,
            'follow_user_id' => $userId,
            'is_del' => 0
        ]);

        if ($followList) {
            $followList = UtilLib::indexBy($followList, 'square_id');

            foreach ($squareList as &$suqare) {
                $squareId = $suqare['id'] ?? 0;

                $followFlag = $followList[$squareId] ?? 0;
                if ($followFlag) {
                    $suqare['is_follow'] = 1;
                }
            }
        }
        
        return $squareList;
    }
}