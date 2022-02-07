<?php

namespace App\Services;

use App\Exceptions\NoStackException;
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

    /**
     * 回复列表
     * @param [type] $params
     * @return void
     */
    public function getList($params)
    {
        return $this->replyRepos->getList($params);
    }

    /**
     * 评论广播
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function create($params, $operationInfo)
    {
        $params ['reply_type'] = config('display.reply_type.post.code');;
        return $this->replyRepos->create($params, $operationInfo, '添加广播评论');
    }

    /**
     * 回复广播的评论
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function createSub($params, $operationInfo)
    {
        $replyId = $params ['reply_id'] ?? 0;
        $replyInfo = $this->replyRepos->getById($replyId);

        if (empty($replyInfo)) {
            throw New NoStackException('回复不存在');
        }

        $params['post_id'] = $replyInfo['post_id'];

        $replyType = $replyInfo ['reply_type'] ?? 0;
        if ($replyType == config('display.reply_type.post.code')) {
            $params ['first_reply_id'] = $replyInfo['id'];
            $params ['reply_type'] = config('display.reply_type.reply.code');
        } else {
            $params ['first_reply_id'] = $replyInfo['first_reply_id'];
            $params ['reply_type'] = config('display.reply_type.reply_comment.code');;
        }

        $params ['user_id'] = $operationInfo['operator_id'] ?? 0;
        $params ['parent_id'] = $replyId;
        $params ['parent_user_id'] = $replyInfo['user_id'];
        return $this->replyRepos->create($params, $operationInfo, '回复广播评论', true);
    }

    /**
     * 删除回复
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function delete($params, $operationInfo)
    {
        return $this->replyRepos->delete($params, $operationInfo);
    }

    /**
     * 某一楼评论列表
     * @param [type] $params
     * @return void
     */
    public function getSubList($params)
    {
        return $this->replyRepos->getSubList($params);
    }
}
