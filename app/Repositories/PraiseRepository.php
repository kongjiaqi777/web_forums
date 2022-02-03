<?php

namespace App\Repositories;

use App\Exceptions\NoStackException;
use App\Repositories\BaseRepository;
use App\Models\Post\PraiseModel;
use App\Models\Post\PostModel;
use App\Models\Post\ReplyModel;
use Carbon\Carbon;
use DB;

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

    public function createPraise($params, $operationInfo, $message = '创建点赞记录')
    {
        $params['is_del'] = 0;
        $praiseInfo = $this->praiseModel->getFirstByCondition($params);

        if ($praiseInfo) {
            throw New NoStackException('点赞记录已存在');
        }

        return DB::transaction(function () use ($params, $operationInfo, $message) {
            $postId = $params ['post_id'] ?? 0;
            $replyId = $params ['reply_id'] ?? 0;
            $praiseType = $params['praise_type'] ?? 0;

            $res = $this->commonCreate(
                $this->praiseModel,
                $params,
                null,
                $operationInfo,
                $message,
                false
            );

            if ($praiseType == config('display.praise_type.post_type.code')) {
                $this->postModel->where('id', $postId)->increment('praise_count');
            } else {
                $this->replyModel->where('id', $replyId)->increment('praise_count');
            }
            return $res;
        });
    }

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

    // public function getPraiseDetail()
    // {
    //     'post_id',
    //     'user_id',
    //     'praise_type',
    //     'reply_id',
    //     $this->praiseModel->
    // }
}