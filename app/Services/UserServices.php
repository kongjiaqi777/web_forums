<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\FollowRepository;
use App\Exceptions\NoStackException;


class UserServices
{
    private $userRepos;
    private $followRepos;

    public function __construct(
        UserRepository $userRepos,
        FollowRepository $followRepos
    ) {
        $this->userRepos = $userRepos;
        $this->followRepos = $followRepos;
    }

    /**
     * 用户模糊搜索
     * @param [type] $params
     * @return void
     */
    public function suggestUser($params, $isJoinFollow, $operatorId)
    {
        return $this->userRepos->suggestUser($params, $isJoinFollow, $operatorId);
    }

    /**
     * 我关注的用户列表
     * @param [type] $params
     * @return void
     */
    public function myFollowUserList($params)
    {
        return $this->followRepos->myFollowUserList($params);
    }

    /**
     * 关注我的用户列表
     * @param [type] $params
     * @return void
     */
    public function myFansUserList($params)
    {
        return $this->followRepos->myFansUserList($params);
    }

    /**
     * 根据ID获取用户信息
     * @param [type] $userId
     * @return void
     */
    public function getById($userId)
    {
        return $this->userRepos->getById($userId);
    }

    /**
     * 更新用户信息
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function update($params, $operationInfo)
    {
        return $this->userRepos->update($params, $operationInfo, '用户修改标签');
    }

    /**
     * 关注某个用户
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function setFollowUser($params, $operationInfo)
    {
        return $this->followRepos->setFollowUser($params, $operationInfo);
    }

    /**
     * 取关某个用户
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function cancelFollowUser($params, $operationInfo)
    {
        return $this->followRepos->cancelFollowUser($params, $operationInfo);
    }

    /**
     * 登出
     * @param [type] $token
     * @return void
     */
    public function logout($token)
    {
        if (empty($token)) {
            throw New NoStackException('token为空');
        }
        return $this->userRepos->deleteToken($token);
    }
}
