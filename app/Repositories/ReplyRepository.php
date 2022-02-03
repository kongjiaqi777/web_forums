<?php

namespace App\Repositories;

use App\Exceptions\BaseException;
use App\Exceptions\NoStackException;
use App\Repositories\BaseRepository;
use App\Models\Post\ReplyModel;
use App\Models\Post\PostModel;
use App\Models\User\UserModel;
use App\Libs\UtilLib;
use Carbon\Carbon;
use DB;
use Exception;
use Log;

class ReplyRepository extends BaseRepository
{
    private $replyModel;
    private $postModel;
    private $userModel;
    
    public function __construct(
        ReplyModel $replyModel,
        PostModel $postModel,
        UserModel $userModel
    ) {
        $this->replyModel = $replyModel;
        $this->postModel = $postModel;
        $this->userModel = $userModel;
    }

    /**
     * 回复列表
     * @param [type] $params
     * @return void
     */
    public function getList($params)
    {
        $postId = $params['post_id'] ?? 0;
        $params ['reply_type'] = config('display.reply_type.post.code');
        $params ['is_del'] = 0;
        $params ['sort'] = ['created_at' => 'asc'];
        $replyRes = $this->replyModel->getList($params);

        $replyList = $replyRes['list'] ?? [];

        if (empty($replyList)) {
            return $replyRes;
        }

        $replyIds = array_column($replyList, 'id');
        $userIds = array_column($replyList, 'user_id');
        $subList = $this->replyModel->getAll([
            'post_id' => $postId,
            'sort' => ['created_at' => 'asc'],
            'is_del' => 0,
            'reply_type' => config('display.reply_type.reply.code'),
            'first_reply_id' => $replyIds
        ]);

        $subIds = array_column($subList, 'user_id');
        $subParentId = array_column($subList, 'parent_user_id');
        $userIds = array_unique(array_merge($userIds, $subIds, $subParentId));
        $userNames = $this->userModel->getUserNameList($userIds);
        $userNames = UtilLib::indexBy($userNames, 'id');
        $indexList = UtilLib::groupBy($subList, 'first_reply_id');

        foreach ($replyList as &$reply) {
            $replyId = $reply['id'] ?? 0;
            $userId = $reply['user_id'] ?? 0;
            $reply ['user_nickname'] = $userNames[$userId]['nickname'] ?? '';
            $subReply = $indexList[$replyId] ?? [];
            $totalSubReplyCount = count($subReply) ?? 0;
            $totalSubReplyPage = ceil($totalSubReplyCount/5) ?? 0;

            if ($totalSubReplyCount > 5) {
                $subReply = array_slice($subReply, 0, 5);
            }

            foreach ($subReply as &$sub) {
                $subUserId = $sub ['user_id'] ?? 0;
                $subParentUserId = $sub ['parent_user_id'] ?? 0;

                $sub ['user_nickname'] = $userNames[$subUserId]['nickname'] ?? '';
                $sub ['parent_user_name'] = $userNames[$subParentUserId]['nickname'] ?? '';
            }
            $reply ['sub_reply_list'] = $subReply;
            $reply ['sub_reply_pagination'] = [
                'page' => 1,
                'perpage' => 5,
                'total_page' => (int)$totalSubReplyPage,
                'total_count' => (int)$totalSubReplyCount,
            ];
        }

        $replyRes ['list'] = $replyList;
        return $replyRes;
    }

    public function create($params, $operationInfo, $message, $isIncrementParent=false)
    {
        $params ['user_id'] = $operationInfo['operator_id'] ?? 0;
        return DB::transaction(function () use ($params, $operationInfo, $message, $isIncrementParent) {
            $postId = $params['post_id'] ?? 0;
            $this->commonCreate(
                $this->replyModel,
                $params,
                null,
                $operationInfo,
                $message,
                false
            );
            $this->postModel->where('id', $postId)->increment('reply_count');
            if ($isIncrementParent) {
                $replyId = $params ['first_reply_id'] ?? 0;
                $this->replyModel->where('id', $replyId)->increment('reply_count');
            }
        });
    }

    public function delete($params, $operationInfo)
    {
        $replyId = $params ['reply_id'] ?? 0;

        $replyInfo = $this->replyModel->getById($replyId);

        if ($replyId) {
            throw New NoStackException('回复信息不存在');
        }

        $postId = $replyInfo['post_id'] ?? 0;
        $firstReplyId = $replyInfo['first_reply_id'] ?? 0;

        return DB::transaction(function () use ($replyId, $postId, $firstReplyId, $operationInfo) {
            try {
                $this->postModel->where('id', $postId)->decrement('reply_count');
                $this->replyModel->where('id', $firstReplyId)->decrement('reply_count');
                return $this->replyModel->where('id', $replyId)->update([
                    'is_del' => 1,
                    'deleted_at' => Carbon::now()->toDateTimeString()
                ]);
            } catch (Exception $e) {
                Log::info(sprintf('删除回复失败[ReplyId][%s][Code][%s][Message][%s]', $replyId, $e->getCode(), $e->getMessage()));
                throw New BaseException('删除回复失败');
            }
        });
    }

    public function getById($replyId)
    {
        return $this->replyModel->getById($replyId);
    }
}
