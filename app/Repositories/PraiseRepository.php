<?php

namespace App\Repositories;

use App\Exceptions\NoStackException;
use App\Repositories\BaseRepository;
use App\Models\Post\PraiseModel;
use App\Models\Post\PostModel;
use App\Models\Post\ReplyModel;
use Carbon\Carbon;
use DB;
use App\Libs\MessageLib;
use Log;

class PraiseRepository extends BaseRepository
{
    private $praiseModel;
    private $postModel;
    private $replyModel;

    public function __construct(
        PraiseModel $praiseModel,
        PostModel $postModel,
        ReplyModel $replyModel
    ) {
        $this->praiseModel = $praiseModel;
        $this->postModel = $postModel;
        $this->replyModel = $replyModel;
    }

    /**
     * 点赞
     * @param [type] $params
     * @param [type] $operationInfo
     * @param string $message
     * @return void
     */
    public function createPraise($params, $operationInfo, $message = '创建点赞记录')
    {
        $params['is_del'] = 0;
        $praiseInfo = $this->praiseModel->getFirstByCondition($params);

        if ($praiseInfo) {
            throw New NoStackException('点赞记录已存在');
        }

        return DB::transaction(function () use ($params, $operationInfo, $message) {
            try {
                $postId = $params ['post_id'] ?? 0;
                $replyId = $params ['reply_id'] ?? 0;
                $praiseType = $params['praise_type'] ?? 0;

                $res = $this->commonCreateNoLog(
                    $this->praiseModel,
                    $params
                );

                if ($praiseType == config('display.praise_type.post_type.code')) {
                    $this->postModel->where('id', $postId)->increment('praise_count');
                    // 发消息
                    $postInfo = $this->postModel->getById($postId);
                    MessageLib::sendMessage(
                        config('display.msg_type.post_praise.code'),
                        [$postInfo ['creater_id']],
                        [
                            'post_id' => $postId,
                            'user_id' => $operationInfo['operator_id'] ?? 0
                        ]
                    );
                } else {
                    // 发消息
                    $this->replyModel->where('id', $replyId)->increment('praise_count');
                    $replyInfo = $this->replyModel->getById($replyId);
                    MessageLib::sendMessage(
                        config('display.msg_type.reply_praise.code'),
                        [$replyInfo ['user_id']],
                        [
                            'reply_id' => $replyId,
                            'user_id' => $operationInfo['operator_id'] ?? 0
                        ]
                    );
                }
                return $res;
            } catch (\Exception $e) {
                Log::error(sprintf($message . '失败[Params][%s][Code][%s][Message][%s]',json_encode($params), $e->getCode(), $e->getMessage()));
                throw New NoStackException('点赞失败');
            }
        });
    }

    /**
     * 取消点赞
     * @param [type] $params
     * @param [type] $operationInfo
     * @param string $message
     * @return void
     */
    public function cancelPraise($params, $operationInfo, $message = '取消点赞记录')
    {
        $params['is_del'] = 0;
        $praiseInfo = $this->praiseModel->getFirstByCondition($params);

        if (empty($praiseInfo)) {
            throw New NoStackException('点赞记录不存在');
        }

        return DB::transaction(function () use ($praiseInfo, $operationInfo, $message) {
            $praiseId =$praiseInfo['id'] ?? 0;
            $postId = $praiseInfo ['post_id'] ?? 0;
            $replyId = $praiseInfo ['reply_id'] ?? 0;
            $praiseType = $praiseInfo ['praise_type'] ?? 0;
            $res = $this->commonUpdate(
                $praiseId,
                $this->praiseModel,
                null,
                [
                    'is_del' => 1,
                    'deleted_at' => Carbon::now()->toDateTimeString() 
                ],
                $operationInfo,
                $message,
                'delete',
                false
            );

            if ($praiseType == config('display.praise_type.post_type.code')) {
                $this->postModel->where('id', $postId)->where('praise_count', '>', 0)->decrement('praise_count');
            } else {
                $this->replyModel->where('id', $replyId)->where('praise_count', '>', 0)->decrement('praise_count');
            }
            return $res;
        });
    }
}