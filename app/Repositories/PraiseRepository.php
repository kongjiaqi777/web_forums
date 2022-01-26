<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Models\Post\PraiseModel;
use App\Models\Post\PostModel;
use App\Models\Post\ReplyModel;

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

    public function createPostPraise($params, $operationInfo)
    {
        $postId = $params['post_id'] ?? 0;
        
        DB::transaction(function () use () {

        });
    }

    public function getPraiseDetail()
    {
        'post_id',
        'user_id',
        'praise_type',
        'reply_id',
        $this->praiseModel->
    }
}