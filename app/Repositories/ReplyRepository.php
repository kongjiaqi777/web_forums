<?php

namespace App\Repositories;

use App\Exceptions\BaseException;
use App\Exceptions\NoStackException;
use App\Repositories\BaseRepository;
use App\Models\Post\ReplyModel;
use App\Models\Post\PostModel;
use App\Models\User\UserModel;
use App\Models\Square\SquareModel;
use App\Libs\UtilLib;
use Carbon\Carbon;
use DB;
use Exception;
use Log;
use App\Libs\MessageLib;

class ReplyRepository extends BaseRepository
{
    private $replyModel;
    private $postModel;
    private $userModel;
    private $squareModel;
    
    public function __construct(
        ReplyModel $replyModel,
        PostModel $postModel,
        UserModel $userModel,
        SquareModel $squareModel
    ) {
        $this->replyModel = $replyModel;
        $this->postModel = $postModel;
        $this->userModel = $userModel;
        $this->squareModel = $squareModel;
    }

    /**
     * 回复列表
     * @param [type] $params
     * @return void
     */
    public function getList($params)
    {
        $sortType = $params ['sort_type'] ?? 'asc';
        unset($params ['sort_type']);
        $postId = $params['post_id'] ?? 0;
        $params ['reply_type'] = config('display.reply_type.post.code');
        $params ['is_del'] = 0;
        $params ['sort'] = ['created_at' => $sortType];
        $replyRes = $this->replyModel->getList($params);

        $replyList = $replyRes['list'] ?? [];

        if (empty($replyList)) {
            return $replyRes;
        }

        $replyIds = array_column($replyList, 'id');
        $userIds = array_column($replyList, 'user_id');
        $subList = $this->replyModel->getAll(
            [
                'post_id' => $postId,
                'is_del' => 0,
                'reply_type' => [
                    config('display.reply_type.reply.code'),
                    config('display.reply_type.reply_comment.code'),
                ],
                'first_reply_id' => $replyIds
            ],
            ['created_at' => $sortType],
        );

        $subUserIds = array_column($subList, 'user_id');
        $subParentIds = array_column($subList, 'parent_user_id');
        $userIds = array_unique(array_merge($userIds, $subUserIds, $subParentIds));
        $userNames = $this->userModel->getUserNameList($userIds);
        $userNames = UtilLib::indexBy($userNames, 'id');
        $indexList = UtilLib::groupBy($subList, 'first_reply_id');

        foreach ($replyList as &$reply) {
            $replyId = $reply['id'] ?? 0;
            $userId = $reply['user_id'] ?? 0;
            $reply ['user_nickname'] = $userNames[$userId]['nickname'] ?? '';
            $reply ['user_avatar'] = $userNames[$userId]['avatar'] ?? '';
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

    /**
     * 创建评论
     * @param [type] $params
     * @param [type] $operationInfo
     * @param bool $isIncrementParent
     * @param string $msgCode
     * @return void
     */
    public function create($params, $operationInfo, $isIncrementParent=false, $msgCode=null, $messageUsers)
    {
        $params ['user_id'] = $operationInfo['operator_id'] ?? 0;

        return DB::transaction(function () use ($params, $operationInfo, $isIncrementParent, $msgCode, $messageUsers) {
            try {
                // add reply without log
                $postId = $params['post_id'] ?? 0;
                $replyId = $this->commonCreateNoLog(
                    $this->replyModel,
                    $params
                );
                // post reply_count+1 
                $this->postModel->where('id', $postId)->increment('reply_count');
                if ($isIncrementParent) {
                    $firstReplyId = $params ['first_reply_id'] ?? 0;
                    // sub_reply reply reply_count+1
                    $this->replyModel->where('id', $firstReplyId)->increment('reply_count');
                }

                // add message
                if ($msgCode && $messageUsers) {
                    MessageLib::sendMessage(
                        $msgCode,
                        $messageUsers,
                        [
                            'user_id' => $params ['user_id'],
                            'post_id' => $postId,
                            'reply_id' => $params['reply_id'] ?? 0
                        ]
                    );
                }
                return $replyId;
            } catch (\Exception $e) {
                Log::error(sprintf('添加回复失败[Param][%s][Code][%s][Message][%s][OperationInfo][%s]', json_encode($params), $e->getCode(), $e->getMessage(), json_encode($operationInfo)));
                throw New NoStackException('添加回复失败');
            } 
            
        });
    }

    /**
     * 删除评论
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function delete($params, $operationInfo)
    {
        $replyId = $params ['reply_id'] ?? 0;
        $replyInfo = $this->replyModel->getById($replyId);
        $postId = $replyInfo['post_id'] ?? 0;
        $operatorId = $operationInfo['operator_id'] ?? 0;
        $replyCreaterId = $replyInfo['user_id'] ?? 0;

        if (empty($replyInfo) || $replyInfo['is_del'] == 1) {
            throw New NoStackException('回复信息不存在');
        }

        $operatorType = $operationInfo['operator_type'] ?? 0;
        if ($operatorType == 10) {
            // 是否是回复创建人
            $isReplyCreater = ($operatorId == $replyCreaterId) ? 1 : 0;

            // 是否是广播创建人
            $postInfo = $this->postModel->getById($postId);
            $postCreaterId = $postInfo['creater_id'] ?? 0;
            $isPostCreater = ($operatorId == $postCreaterId) ? 1 : 0;
    
            // 是否是关注度>1000的广场创建人
            $isSquareCreater = 0;
            $squareId = $postInfo['square_id'] ?? 0;
            if ($squareId) {
                $squareInfo = $this->squareModel->getById($squareId);
                $squareCreaterId = $squareInfo['creater_id'] ?? 0;
                if ($squareInfo['follow_count'] >= 1000 && $operatorId == $squareCreaterId) {
                    $isSquareCreater = 1;
                }
            }

            if (!($isReplyCreater || $isPostCreater || $isSquareCreater)) {
                throw New NoStackException('当前用户没有删除权限');
            }
        }

        $replyType = $replyInfo['reply_type'] ?? 0;
        return DB::transaction(function () use ($replyId, $postId, $replyType) {
            try {
                $postDecrement = 1;

                if ($replyType == config('display.reply_type.post.code')) {
                    $count = $this->replyModel->where('first_reply_id', $replyId)->where('is_del', 0)->count();
                    $res = $this->replyModel
                    ->where(function ($query) use ($replyId){
                        $query->orWhere('id', $replyId)
                        ->orWhere('first_reply_id', $replyId);
                    })
                    ->where('is_del', 0)
                    ->update([
                        'is_del' => 1,
                        'deleted_at' => Carbon::now()->toDateTimeString()
                    ]);
                    $postDecrement = $count + 1;
                    
                } else {
                    $firstReplyId = $replyInfo['first_reply_id'] ?? 0;

                    $res = $this->replyModel
                    ->where('id', $replyId)
                    ->where('is_del', 0)
                    ->update([
                        'is_del' => 1,
                        'deleted_at' => Carbon::now()->toDateTimeString()
                    ]);
                    $this->replyModel->where('id', $firstReplyId)->decrement('reply_count');
                }
                $this->postModel->where('id', $postId)->decrement('reply_count', $postDecrement);
                return $res;
            } catch (Exception $e) {
                Log::info(sprintf('删除回复失败[ReplyId][%s][Code][%s][Message][%s]', $replyId, $e->getCode(), $e->getMessage()));
                throw New BaseException('删除回复失败');
            }
        });
    }

    /**
     * 根据ID获取评论详情
     * @param [type] $replyId
     * @return void
     */
    public function getById($replyId)
    {
        return $this->replyModel->getById($replyId);
    }

    /**
     * 某一楼评论列表
     * @param [type] $params
     * @return void
     */
    public function getSubList($params)
    {
        $sortType = $params ['sort_type'] ?? 'asc';
        unset($params ['sort_type']);

        $replyId = $params['reply_id'] ?? 0;
        // $replyInfo = $this->replyModel->getById($replyId);

        $subRes = $this->replyModel->getList([
            'sort' => ['created_at' => $sortType],
            'is_del' => 0,
            'reply_type' => [
                config('display.reply_type.reply.code'),
                config('display.reply_type.reply_comment.code'),

            ],
            'first_reply_id' => $replyId
        ]);

        $subList = $subRes['list'] ?? [];
        if (empty($subList)) {
            return $subRes;
        }

        $subUserIds = array_column($subList, 'user_id');
        $subParentIds = array_column($subList, 'parent_user_id');
        $userIds = array_unique(array_merge($subUserIds, $subParentIds));
        $userNames = $this->userModel->getUserNameList($userIds);
        $userNames = UtilLib::indexBy($userNames, 'id');

        foreach ($subList as &$sub) {
            $subUserId = $sub ['user_id'] ?? 0;
            $subParentUserId = $sub ['parent_user_id'] ?? 0;

            $sub ['user_nickname'] = $userNames[$subUserId]['nickname'] ?? '';
            $sub ['parent_user_name'] = $userNames[$subParentUserId]['nickname'] ?? '';
        }
        $subRes ['list'] = $subList;
        return $subRes;
    }

    public function getMyReplyList($params, $operatorId)
    {
        $page = $params['page'] ?? 1;
        $perpage = $params['perpage'] ?? 20;

        $leftModels = [
            [
                'table_name' => 'posts',
                'left' => 'posts.id',
                'right' => 'post_replys.post_id',
            ]
        ];

        return $this->getDataList(
            $this->replyModel,
            [
                'post_replys.id',
                'post_replys.post_id',
                'post_replys.content',
                'post_replys.created_at',
                'posts.title',
            ], [
                'is_del' => 0,
                'user_id' => $operatorId
            ],
            $page,
            $perpage,
            $leftModels,
            ['post_replys.created_at' => 'desc']
        );
    }
}
