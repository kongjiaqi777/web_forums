<?php

namespace App\Services;

use App\Exceptions\NoStackException;
use App\Services\BaseServices;
use App\Repositories\ReplyRepository;
use App\Repositories\UserRepository;

class ReplyServices extends BaseServices
{
    private $replyRepos;
    private $userRepos;

    public function __construct(
        ReplyRepository $replyRepos,
        UserRepository $userRepos
    ) {
        $this->replyRepos = $replyRepos;
        $this->userRepos = $userRepos;
    }

    public function getList($params)
    {
        return $this->replyRepos->getList($params);
    }

    public function create($params, $operationInfo)
    {
        $params ['reply_type'] = config('display.reply_type.post.code');;
        return $this->replyRepos->create($params, $operationInfo, '添加广播评论');
    }

    public function createSub($params, $operationInfo)
    {
        $replyId = $params ['reply_id'] ?? 0;
        $replyInfo = $this->replyRepos->getById($replyId);

        if (empty($replyInfo)) {
            throw New NoStackException('回复不存在');
        }

        $params ['reply_type'] = config('display.reply_type.reply.code');;
        $params ['user_id'] = $operationInfo['operator_id'] ?? 0;
        $params ['parent_id'] = $replyId;
        $params ['parent_user_id'] = $replyInfo['user_id'];
        $params ['first_reply_id'] = $replyInfo['first_reply_id'];
        return $this->replyRepos->create($params, $operationInfo, '回复广播评论', true);
    }

    public function delete($params, $operationInfo)
    {
        return $this->replyRepos->delete($params, $operationInfo);
    }

    public function joinUserName($list, $fields, $userIds)
    {
        return $this->userRepos->getUserNameList($userIds);
    }
}
