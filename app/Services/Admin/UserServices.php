<?php

namespace App\Services\Admin;

use App\Repositories\UserRepository;
use App\Exceptions\NoStackException;

class UserServices
{
    private $userRepos;

    public function __construct(UserRepository $userRepos)
    {
        $this->userRepos = $userRepos;
    }

    public function login($params)
    {
        $email = $params['email'] ?? '';
        $password = $params['password'] ?? '';

        $user = $this->userRepos->checkAdminUserAuth($email, $password);

        if (empty($user)) {
            throw new NoStackException('用户名密码有误！');
        }

        $userId = $user['id'] ?? 0;
        $nickname = $user['nickname'] ?? ''; 
        
        return $this->userRepos->setToken($userId, $email, $password, $nickname, 20);
    }

    /**
     * 管理端登出
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

    /**
     * 管理端注册账户
     * @param [type] $params
     * @return void
     */
    public function signup($params)
    {
        return $this->userRepos->adminSignUp($params);  
    }

    public function suggest($params)
    {
        return $this->userRepos->suggestUser($params);

    }
}