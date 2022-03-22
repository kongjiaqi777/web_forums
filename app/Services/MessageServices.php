<?php

namespace App\Services;

use App\Repositories\MessageRepository;

class MessageServices
{
    private $messageRepos;

    public function __construct(MessageRepository $messageRepos)
    {
        $this->messageRepos = $messageRepos;
    }

    /**
     * 消息详情
     * @param [type] $params
     * @return void
     */
    public function detail($params)
    {
        return $this->messageRepos->detail($params);
    }

    /**
     * 读消息
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */ 
    public function read($params, $operationInfo)
    {
        return $this->messageRepos->read($params, $operationInfo);
    }

    /**
     * 我的消息列表
     * @param [type] $params
     * @return void
     */
    public function myMessageList($params)
    {
        return $this->messageRepos->myMessageList($params);
    }

    public function delete($operationInfo)
    {
        return $this->messageRepos->delete($operationInfo);
    }
}
