<?php

namespace App\Services;

use App\Services\BaseServices;
use App\Repositories\UserRepository;

class UserServices extends BaseServices
{
    private $userRepos;

    public function __construct(UserRepository $userRepos)
    {
        $this->userRepos = $userRepos;
    }

    public function suggestUser($params)
    {
        return $this->userRepos->suggestUser($params);
    }

    public function myFollowUserList($params)
    {
        
    }
}
