<?php

namespace App\Repositories;

use App\Exceptions\NoStackException;
use App\Libs\MessageLib;
use App\Repositories\BaseRepository;
use App\Models\Post\PostModel;
use App\Models\Post\PostOpLogModel;
use App\Models\Square\SquareModel;
use App\Models\Post\ReplyModel;
use App\Models\Post\PraiseModel;
use App\Models\Post\BrowseRecordModel;
use App\Models\Follow\SquareFollowModel;
use App\Models\User\UserModel;
use Carbon\Carbon;
use App\Libs\UtilLib;
use DB;

class PostRepository extends BaseRepository
{
    private $postModel;
    private $postOpLogModel;
    private $squareModel;
    private $replyModel;
    private $praiseModel;
    private $browseRecordModel;
    private $followSquareModel;
    private $userModel;

    public function __construct(
        PostModel $postModel,
        PostOpLogModel $postOpLogModel,
        SquareModel $squareModel,
        ReplyModel $replyModel,
        PraiseModel $praiseModel,
        BrowseRecordModel $browseRecordModel,
        SquareFollowModel $followSquareModel,
        UserModel $userModel
 
    ) {
        $this->postModel = $postModel;
        $this->postOpLogModel = $postOpLogModel;
        $this->squareModel = $squareModel;
        $this->replyModel = $replyModel;
        $this->praiseModel = $praiseModel;
        $this->browseRecordModel = $browseRecordModel;
        $this->followSquareModel = $followSquareModel;
        $this->userModel = $userModel;
    }

    /**
     * 广播列表-分页
     * @param [type] $params
     * @param bool $isShowPraise 是否展示点赞记录
     * @param int $operatorId 当前登录用户ID
     * @return void
     */
    public function getList($params, $isShowPraise=false, $operatorId=0)
    {
        $page = $params['page'] ?? 1;
        $perpage = $params['perpage'] ?? 20;

        $fields = [
            'posts.id',
            'posts.square_id',
            'posts.post_type',
            'posts.creater_id',
            'posts.title',
            'posts.content',
            'posts.top_rule',
            'posts.photo',
            'posts.reply_count',
            'posts.praise_count',
            'posts.created_at',
            'posts.updated_at',
            'posts.deleted_at',
            'posts.is_del',
            DB::raw('0 as is_praise'),
            'users.nickname as creater_name',
            'users.avatar',
        ];

        $leftModels = [
            [
                'table_name' => 'users',
                'left' => 'posts.creater_id',
                'right' => 'users.id',
            ]
        ];

        $res = $this->getDataList(
            $this->postModel,
            $fields,
            $params,
            $page,
            $perpage,
            $leftModels,
            [
                'top_rule' => 'desc',
                'created_at' => 'desc'
            ]
        );

        $list = $res ['list'] ?? [];

        if($list && $isShowPraise && $operatorId) {
            $list = $this->joinPraiseFlag($list, $operatorId);
        }

        $list = $this->joinSqureName($list);
        $res ['list'] = $list;
        return $res;
    }

    /**
     * 广播列表-不分页
     * @param [type] $params
     * @return void
     */
    public function getAll($params, $sortInfo)
    {
        return $this->postModel->getAll($params, $sortInfo);
    }

    /**
     * 创建广播
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function createPost($params, $operationInfo)
    {
        $squareId = $params['square_id'] ?? 0;
        $postType = $params['post_type'] ?? 0;
        if ($postType == config('display.post_type.square.code')) {
            $squareInfo = $this->squareModel->getById($squareId);
            if (empty($squareInfo)) {
                throw New NoStackException('广场信息有误，无法创建');
            }

            $isFollow = $this->followSquareModel->getFirstByCondition([
                'square_id' => $squareId,
                'follow_user_id' => $operationInfo['operator_id'] ?? 0,
                'is_del' => 0
            ]);

            $squareCreaterId = $squareInfo['creater_id'] ?? 0;
            $operatorId = $operationInfo['operator_id'] ?? 0;

            if (empty($isFollow) && $squareCreaterId != $operatorId) {
                throw New NoStackException('未关注不能发布广播');
            }
        } else {
            $params['square_id'] = 0;
        }

        return $this->commonCreate(
            $this->postModel,
            $params,
            $this->postOpLogModel,
            $operationInfo,
            '创建广播'
        );
    }

    /**
     * 广播详情
     * @param [type] $params
     * @return void
     */
    public function detailPost($params, $joinPraiseFlag=false, $operatorId=0)
    {
        $postId = $params['post_id'] ?? 0;
        $detail = $this->postModel->getById($postId);

        $createrInfo = $this->userModel->getFirstByCondition([
            'id' => $detail['creater_id']
        ]);

        $detail['creater_name'] = $createrInfo['nickname'] ?? '';
        $detail['creater_avatar'] = $createrInfo['avatar'] ?? '';

        $detail ['is_praise'] = 0;

        if ($joinPraiseFlag && $operatorId && $detail) {
            $isPraise = $this->praiseModel->getFirstByCondition([
                'post_id' => $postId,
                'user_id' => $operatorId,
                'praise_type' => config('display.praise_type.post_type.code'),
                'is_del' => 0
            ]);

            if ($isPraise) {
                $detail ['is_praise'] = 1;
            }
        }
        return $detail;
    }

    /**
     * 更新广播
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function updatePost($params, $operationInfo, $message='更新广播')
    {
        $postId = $params['post_id'] ?? 0;
        $postInfo = $this->postModel->getById($postId);

        if (empty($postInfo)) {
            throw New NoStackException('广播信息不存在');
        }

        return $this->commonUpdate(
            $postId,
            $this->postModel,
            $this->postOpLogModel,
            $params, 
            $operationInfo,
            $message
        );
    }

    /**
     * 广播模糊搜索
     * @param [type] $params
     * @param bool $isShowPraise
     * @param int $operatorId
     * @return void
     */
    public function suggest($params, $isShowPraise=false, $operatorId=0)
    {
        $name = $params['name'] ?? '';
        $page = $params['page'] ?? 1;
        $perpage = $params['perpage'] ?? 20;
        unset($params['name']);

        $searchAble = $this->postModel->getSearchAble();
        $condsSearch = array_intersect_key($searchAble, $params);

        $fields = [
            'id',
            'title',
            'content',
            'photo',
            'reply_count',
            'praise_count',
            DB::raw('0 as is_follow')
        ];

        $query = $this->postModel;

        if ($condsSearch) {
            $query = $this->getQueryBuilder($query, $params, $condsSearch, $fields);
        }

        // 模糊搜索
        $query = $query->where(function ($query) use ($name) {
            $query->orWhere('title', 'like', '%'.$name.'%')
            ->orWhere('content', 'like', '%'.$name.'%');
        });

        $offset = ($page - 1) * $perpage;
        $pagination = $this->postModel->getPaginate($fields, $query, $page, $perpage);

        $list = $query->select($fields)
            ->offset($offset)
            ->limit($perpage)
            ->orderBy('reply_count', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->all();

        if ($list && $isShowPraise && $operatorId) {
            $list = $this->joinPraiseFlag($list, $operatorId);
        }

        return [
            'list' => $list,
            'pagination' => $pagination,
        ];
    }

    /**
     * 广场主设置置顶
     * @param [type] $postId
     * @param [type] $operationInfo
     * @param [type] $topType
     * @return void
     */
    public function setTop($postId, $operationInfo)
    {
        $postInfo = $this->postModel->getById($postId);
        $squareId = $postInfo['square_id'] ?? 0;
        if (!$squareId ) {
            throw New NoStackException('不符合置顶规则');
        }

        // 当前广场已经置顶的广播
        $maxTopRule = $this->postModel
            ->where('top_rule', '<=', 3)
            ->where('top_rule', '>', 0)
            ->where([
                'is_del' => 0,
                'square_id' => $squareId
            ])
            ->max('top_rule');

        if ($maxTopRule < 3) {
            // 当前广场置顶数目小于3
            return DB::transaction(function () use ($maxTopRule, $postId, $postInfo, $squareId, $operationInfo) {
                // 发消息
                MessageLib::sendMessage(
                    config('display.msg_type.owner_top.code'),
                    [$postInfo['creater_id']],
                    [
                        'post_id' => $postId,
                        'square_id' => $squareId
                    ]
                );
                return $this->updatePost(
                    [
                        'top_rule' => $maxTopRule + 1,
                        'post_id' => $postId
                    ],
                    $operationInfo,
                    '广场主设置置顶'
                );
            });

        } else if ($maxTopRule == 3) {
            $updateList = $this->postModel
            ->where('top_rule', '<=', 3)
            ->where('top_rule', '>', 0)
            ->where([
                'is_del' => 0,
                'square_id' => $squareId
            ])
            ->select(['id', 'top_rule'])
            ->get();

            $updateIds = array_column($updateList, 'id');
            if (in_array($postId, $updateIds)) {
                throw New NoStackException('当前广播已经置顶');
            }

            $news = [];
            $originals = [];
            foreach ($updateList as $updateList) {
                $id = $updateList['id'] ?? 0;
                $topRule = $updateList['top_rule'] ?? 0;
                $news[$id] = ['top_rule' => $topRule - 1];
                $originals [$id] = ['top_rule' => $topRule];  
            }
            return DB::transaction(function () use ($squareId, $postId, $news, $originals, $operationInfo, $postInfo) {
                try {
                    $this->postModel
                        ->where('top_rule', '<=', 3)
                        ->where('top_rule', '>', 0)
                        ->where([
                            'is_del' => 0,
                            'square_id' => $squareId
                        ])
                        ->decrement('top_rule');
                    // oplog
                    $this->postOpLogModel->saveUpdateOpLogDatas($news, $originals, $operationInfo, '广场主设置置顶');
                    // 发消息
                    MessageLib::sendMessage(
                        config('display.msg_type.owner_top.code'),
                        [$postInfo['creater_id']],
                        [
                            'post_id' => $postId,
                            'square_id' => $squareId
                        ]
                    );

                    return $this->updatePost(['post_id' => $postId, 'top_rule' => 3], $operationInfo, '广场主设置置顶');
                } catch (\Exception $e) {
                    throw New NoStackException('置顶失败');
                }
            });
        }
    }

    /**
     * 管理员设置置顶
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function adminSetTop($params, $operationInfo)
    {
        $homePageTop = $params['homepage_top'] ?? 0;
        $postId = $params ['post_id'] ?? 0;
        $postInfo = $this->postModel->getById($postId);
        $currentCheck = $postInfo['top_rule'] ?? 0;
        if ($currentCheck > 0 && $currentCheck <=3) {
            throw New NoStackException('当前广播已经由广场主设置了置顶规则，请勿重复操作');
        }

        if ($homePageTop) {
            // 首页置顶=5
            $topRule = 5;
            $topRuleCheck = $this->postModel
                ->where('top_rule', $topRule)
                ->where('is_del', 0)
                ->first();
            $msgCode =  config('display.msg_type.homepage_top.code');
        } else {
            // 广场置顶=4
            $topRule = 4;
            // $postInfo = $this->postModel->getById($postId);
            $squareId = $postInfo['square_id'] ?? 0;
            if (empty($squareId)) {
                throw New NoStackException('当前广播没有所属广场，无法设置广场置顶');
            }
            $topRuleCheck = $this->postModel
                ->where('top_rule', $topRule)
                ->where('is_del', 0)
                ->where('square_id', $squareId)
                ->first();
            $msgCode =  config('display.msg_type.square_top.code');
        }

        return DB::transaction(function () use ($postId, $topRule, $topRuleCheck, $operationInfo, $msgCode, $postInfo) {
            if ($topRuleCheck) {
                $this->updatePost(['post_id' => $topRuleCheck['id'], 'top_rule' => 0], $operationInfo, '管理员设置置顶');
            }

            // 发消息
            MessageLib::sendMessage(
                $msgCode,
                [$postInfo['creater_id']],
                [
                    'post_id' => $postId,
                    'square_id' => $postInfo['square_id']
                ]
            );
            return $this->updatePost(['post_id' => $postId, 'top_rule' => $topRule], $operationInfo, '管理员设置置顶');
        });
    }

    /**
     * 删除广播
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function delete($params, $operationInfo, $msgType)
    {
        $postId = $params['post_id'] ?? 0;
        $postDetail = $this->postModel->getById($postId);
        $squareId = $postDetail['square_id'] ?? 0;
        $currentTopRule = $postDetail['top_rule'] ?? 0;
        
        $operatorType = $operationInfo['operator_type'] ?? 0;
        if ($operatorType == 10) {
            // 广播创建人和关注人数1000以上的广场主
            $operatorId = $operationInfo['operator_id'] ?? 0;
            if ($operatorId != $postDetail['creater_id']) {
                $squareInfo = $this->squareModel->getById($squareId);
                if (!($squareInfo['follow_count'] >= 1000 && $operatorId == $squareInfo['creater_id'])) {
                    throw New NoStackException('当前用户没有删除权限');
                }
            } else {
                // 本人删除广播不发消息
                $msgType = null;
            }
        }
        return DB::transaction(function () use ($postId, $currentTopRule, $squareId, $postDetail, $operationInfo, $msgType){  
            // 删除广播
            $this->postModel->where('id',$postId)->update(
                [
                    'is_del' => 1,
                    'deleted_at' => Carbon::now()->toDateTimeString()
                ]
            );

            // 处理置顶规则
            if ($currentTopRule > 0 && $currentTopRule < 3) {
                // 处理top_rule
                // top_rule > 当前top_rule并且<=3的post，top_rule均-1
                $this->postModel
                    ->where('top_rule', '<=', 3)
                    ->where('top_rule', '>', $currentTopRule)
                    ->where([
                        'is_del' => 0,
                        'square_id' => $squareId
                    ])
                    ->decrement('top_rule');
            }

            // op log
            $this->postOpLogModel->saveDeleteOpLogDatas([$postId], $operationInfo);

            // 删除广播下的回复
            $this->replyModel->where('post_id', $postId)->update(
                [
                    'is_del' => 1,
                    'deleted_at' => Carbon::now()->toDateTimeString()
                ]
            );

            // 发消息
            MessageLib::sendMessage(
                $msgType,
                [$postDetail['creater_id']],
                ['post_id' => $postId]
            );
        });
    }

    /**
     * 查询当前登录用户浏览历史
     * @param [type] $params 参数，主要包含post_id
     * @param [type] $operatorId 用户ID
     * @return void
     */
    public function browseList($params, $operatorId)
    {
        $page = $params['page'] ?? 1;
        $perpage = $params['perpage'] ?? 20;

        $leftModels = [
            [
                'table_name' => 'posts',
                'left' => 'posts.id',
                'right' => 'post_browse_records.post_id',
                // 'conds' => [
                //     'is_del' => 0,
                // ],
                // 'conds_search' => [
                //     'is_del' => [
                //         'query_key' => 'is_del',
                //         'operator' => '='
                //     ],
                // ]
            ]
        ];

        $fields = [
            'posts.*',
            'posts.id as post_id',
            'post_browse_records.browsed_at'
        ];

        return $this->getDataList(
            $this->browseRecordModel,
            $fields,
            [
                'is_del' => 0,
                'user_id' => $operatorId
            ],
            $page,
            $perpage,
            $leftModels
        );
    }

    /**
     * 添加浏览记录
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function addBrowseRecord($params, $operationInfo)
    {
        $insertDate = [
            'post_id' => $params['post_id'] ?? 0,
            'user_id' => $operationInfo['operator_id'] ?? 0,
            'is_del' => 0
        ];

        $time = [
            'browsed_at' => Carbon::now()->toDateTimeString()
        ];

        $browseRecord = $this->browseRecordModel->getFirstByCondition($insertDate);
        if ($browseRecord) {
            $recordId = $browseRecord['id'] ?? 0;
            return $this->commonUpdate(
                $recordId,
                $this->browseRecordModel,
                null,
                $time,
                $operationInfo,
                '更新',
                'update',
                false
            );
        } else {
            return $this->commonCreateNoLog(
                $this->browseRecordModel,
                array_merge($insertDate, $time)
            );
        }
        
    }

    /**
     * 添加点赞标识
     * @param [type] $list
     * @param [type] $operatorId
     * @return void
     */
    private function joinPraiseFlag($list, $operatorId)
    {
        $postIds = array_column($list, 'id');

        $praiseList = $this->praiseModel->getAll([
            'post_id' => $postIds,
            'user_id' => $operatorId,
            'praise_type' => config('display.praise_type.post_type.code'),
            'is_del' => 0
        ], [
            'id',
            'post_id'
        ]);
          
        if ($praiseList) {
            $praiseList = UtilLib::indexBy($praiseList, 'post_id');

            foreach ($list as &$detail) {
                $postId = $detail['id'] ?? 0;
                $praiseFlag = $praiseList[$postId] ?? 0;
                if ($praiseFlag) {
                    $detail['is_praise'] = 1;
                }
            }     
        }
        return $list;
    }

    public function getById($postId)
    {
        return $this->postModel->getById($postId);
    }

    /**
     * 添加广场名称信息
     * @param [type] $list
     * @return void
     */
    private function joinSqureName($list)
    {
        if (empty($list)) {
            return [];
        }

        $squareIds = array_column($list, 'square_id');
        if ($squareIds) {
            $squareIds = array_unique($squareIds);
        }

        $squareNames = $this->squareModel->getAll([
            'id' => $squareIds
        ], [
            'id' => 'desc'
        ], [
            'id',
            'name'
        ]);

        if ($squareNames) {
            $squareNames = UtilLib::indexBy($squareNames, 'id'); 
        }

        foreach ($list as &$detail) {
            $squareId = $detail['square_id'] ?? 0;
            $detail['square_name'] = $squareNames[$squareId]['name'] ?? '';
        }  

        return $list;
    }
}