<?php

namespace App\Services;

use App\Services\BaseServices;
use App\Repositories\MessageRepository;

class MessageServices extends BaseServices
{
    private $messageRepos;

    public function __construct(MessageRepository $messageRepos)
    {
        $this->messageRepos = $messageRepos;
    }

    public function detail($params)
    {
        return $this->messageRepos->detail($params);
    }

    public function read($params, $operationInfo)
    {
        return $this->messageRepos->read($params, $operationInfo);
    }

    public function myMessageList($params)
    {
        return $this->messageRepos->myMessageList($params);
    }
}
