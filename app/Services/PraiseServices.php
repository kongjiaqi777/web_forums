<?php

namespace App\Services;

use App\Repositories\PraiseRepository;
use App\Repositories\ReplyRepository;
use Illuminate\Support\Arr;

class PraiseServices
{
    private $praiseRepos;
    private $replyRepos;

    public function __construct(
        PraiseRepository $praiseRepos,
        ReplyRepository $replyRepos
    ) {
        $this->praiseRepos = $praiseRepos;
        $this->replyRepos = $replyRepos;
    }

    public function createPost($params, $operationInfo)
    {
        $params = Arr::only($params, ['post_id']);
        $params ['user_id'] = $operationInfo['operator_id'] ?? 0;
        $params ['praise_type'] = config('display.praise_type.post_type.code');
        return $this->praiseRepos->createPraise($params, $operationInfo, '点赞广播');
    }

    public function cancelPost($params, $operationInfo)
    {
        $params = Arr::only($params, ['post_id']);
        $params ['user_id'] = $operationInfo['operator_id'] ?? 0;
        $params ['praise_type'] = config('display.praise_type.post_type.code');
        return $this->praiseRepos->cancelPraise($params, $operationInfo, '取消点赞广播');
    }

    public function createReply($params, $operationInfo)
    {
        $params = Arr::only($params, ['reply_id']);
        $replyInfo = $this->replyRepos->getById($params['reply_id']);

        $params ['post_id'] = $replyInfo ['post_id'] ?? 0;
        $params ['user_id'] = $operationInfo['operator_id'] ?? 0;
        $params ['praise_type'] = config('display.praise_type.reply_type.code');
        return $this->praiseRepos->createPraise($params, $operationInfo, '点赞评论');
    }

    public function cancelReply($params, $operationInfo)
    {
        $params = Arr::only($params, ['reply_id']);
        $replyInfo = $this->replyRepos->getById($params['reply_id']);

        $params ['post_id'] = $replyInfo ['post_id'] ?? 0;
        $params ['user_id'] = $operationInfo['operator_id'] ?? 0;
        $params ['praise_type'] = config('display.praise_type.reply_type.code');
        return $this->praiseRepos->cancelPraise($params, $operationInfo, '取消点赞评论');
    }
}
