<?php

namespace App\Repositories;

use App\Exceptions\NoStackException;
use App\Repositories\BaseRepository;
use App\Models\Post\ReplyModel;
use App\Libs\UtilLib;


class ReplyRepository extends BaseRepository
{
    private $replyModel;
    
    public function __construct(ReplyModel $replyModel)
    {
        $this->replyModel = $replyModel;
    }

    public function getList($params)
    {
        $postId = $params['post_id'] ?? 0;

        $replyRes = $this->replyModel->getList([
            'post_id' => $postId,
            'page' => 1,
            'perpage' => 10,
            'sort' => ['created_at' => 'asc'],
            'is_del' => 0,
            'reply_type' => 10
        ]);

        $replyList = $replyRes['list'] ?? [];

        if (empty($replyList)) {
            return $replyRes;
        }

        $replyIds = array_column($replyList, 'id');

        $subList = $this->replyModel->getAll([
            'post_id' => $postId,
            'sort' => ['created_at' => 'asc'],
            'is_del' => 0,
            'reply_type' => 20,
            'parent_id' => $replyIds
        ]);

        $indexList = UtilLib::groupBy($subList, 'parent_id');

        foreach ($replyList as &$reply) {
            $replyId = $reply['id'] ?? 0;
            $subReply = $indexList[$replyId] ?? [];
            $totalSubReplyCount = count($subReply) ?? 0;
            $totalSubReplyPage = ceil($totalSubReplyCount/5) ?? 0;

            if ($totalSubReplyCount > 5) {
                $subReply = array_slice($subReply, 0, 5);
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

    public function create($params, $operationInfo)
    {

    }

    public function delete($params, $operationInfo)
    {

    }
}
