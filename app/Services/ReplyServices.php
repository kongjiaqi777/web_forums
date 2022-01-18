<?php

namespace App\Services;

use App\Services\BaseServices;
use App\Repositories\ReplyRepository;

class ReplyServices extends BaseServices
{
    private $replyRepos;

    public function __construct(
        ReplyRepository $replyRepos
    ) {
        $this->replyRepos = $replyRepos;
    }

    public function getList($params)
    {
        return $this->replyRepos->getList($params);
    }

    public function add($params, $operationInfo)
    {
        return $this->replyRepos->create($params, $operationInfo);
    }

    public function delete($params, $operationInfo)
    {
        return $this->replyRepos->delete($params, $operationInfo);
    }
}
