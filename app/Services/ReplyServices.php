<?php

namespace App\Services;

use App\Exceptions\NoStackException;
use App\Repositories\ReplyRepository;
use App\Repositories\PostRepository;

class ReplyServices
{
    private $replyRepos;
    private $postRepos;

    public function __construct(
        ReplyRepository $replyRepos,
        PostRepository $postRepos
    ) {
        $this->replyRepos = $replyRepos;
        $this->postRepos = $postRepos;
    }

    /**
     * 回复列表
     * @param [type] $params
     * @return void
     */
    public function getList($params, $isShowPraise, $operatorId)
    {
        return $this->replyRepos->getList($params, $isShowPraise, $operatorId);
    }

    /**
     * 评论广播
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function create($params, $operationInfo)
    {
        $params ['reply_type'] = config('display.reply_type.post.code');
        $msgCode = config('display.msg_type.post_reply.code');
        $postInfo = $this->postRepos->getById($params['post_id']);
        $messageUsers = [$postInfo['creater_id']];
        return $this->replyRepos->create($params, $operationInfo, false, $msgCode, $messageUsers);
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
            $params ['reply_type'] = config('display.reply_type.reply_comment.code');
        }

        $params ['user_id'] = $operationInfo['operator_id'] ?? 0;
        $params ['parent_id'] = $replyId;
        $params ['parent_user_id'] = $replyInfo['user_id'];
        $msgCode = config('display.msg_type.reply_reply.code');
        $messageUsers = [$replyInfo['user_id']];
        return $this->replyRepos->create($params, $operationInfo, true, $msgCode, $messageUsers);
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
    public function getSubList($params, $isShowPraise, $operatorId)
    {
        return $this->replyRepos->getSubList($params, $isShowPraise, $operatorId);
    }

    /**
     * 我的回复列表
     * @param [type] $params
     * @param [type] $operatorId
     * @return void
     */
    public function getMyReplyList($params, $operatorId)
    {
        return $this->replyRepos->getMyReplyList($params, $operatorId);
    }
}
