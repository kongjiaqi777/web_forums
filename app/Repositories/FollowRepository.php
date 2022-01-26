<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Models\Follow\UserFollowModel;
use App\Models\Follow\SquareFollowModel;


class FollowRepository extends BaseRepository
{
    private $userFollowModel;
    private $squareFollowModel;

    public function __construct(
        UserFollowModel $userFollowModel,
        SquareFollowModel $squareFollowModel
    ) {
        $this->userFollowModel = $userFollowModel;
        $this->squareFollowModel = $squareFollowModel;
    }

    /**
     * 查询用户ID关注的用户列表
     * @param [type] $userId
     * @param int $page
     * @param int $perpage
     * @return void
     */
    public function getFollowUserList($userId, $page=1, $perpage=20)
    {
        return $this->userFollowModel->getList(
            [
                'follow_user_id' => $userId,
                'is_del' => 0,
                'sort', ['created_at' => 'desc'],
                'page' => $page,
                'perpage' => $perpage
            ]
        );
    }

    /**
     * 查询用户ID关注的广场列表
     * @param [type] $userId
     * @param int $page
     * @param int $perpage
     * @return void
     */
    public function getFollowSquareList($userId, $page=1, $perpage=20)
    {
        return $this->squareFollowModel->getList(
            [
                'follow_user_id' => $userId,
                'is_del' => 0,
                'sort' => ['created_at' => 'desc'],
                'page' => $page,
                'perpage' => $perpage
            ]
        );
    }

    /**
     * 根据广场ID获取关注广场的用户列表
     * @param [type] $params
     * @return void
     */
    public function getSquareFollowAll($params)
    {
        return $this->squareFollowModel->getAll($params);
    }
}