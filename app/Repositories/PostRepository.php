<?php

namespace App\Repositories;

use App\Exceptions\NoStackException;
use App\Repositories\BaseRepository;
use App\Models\Post\PostModel;
use App\Models\Post\PostOpLogModel;
use App\Models\Square\SquareModel;
use App\Models\Post\ReplyModel;
use App\Models\Post\PraiseModel;
use App\Models\Post\BrowseRecordModel;
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

    public function __construct(
        PostModel $postModel,
        PostOpLogModel $postOpLogModel,
        SquareModel $squareModel,
        ReplyModel $replyModel,
        PraiseModel $praiseModel,
        BrowseRecordModel $browseRecordModel
    ) {
        $this->postModel = $postModel;
        $this->postOpLogModel = $postOpLogModel;
        $this->squareModel = $squareModel;
        $this->replyModel = $replyModel;
        $this->praiseModel = $praiseModel;
        $this->browseRecordModel = $browseRecordModel;
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

        $fields = array_merge($this->postModel->findable, [
            DB::raw('0 as is_praise'),
        ]);
        $res = $this->getDataList(
            $this->postModel,
            $fields,
            $params,
            $page,
            $perpage
        );

        $list = $res ['list'] ?? [];
        if($list && $isShowPraise && $operatorId) {
            $list = $this->joinPraiseFlag($list, $operatorId);
        }

        $res ['list'] = $list;
        return $res;
    }

    /**
     * 广播列表-不分页
     * @param [type] $params
     * @return void
     */
    public function getAll($params)
    {
        return $this->postModel->getAll($params);
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
    public function detailPost($params)
    {
        $postId = $params['post_id'] ?? 0;
        return $this->postModel->getById($postId);
    }

    /**
     * 更新广播
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function updatePost($params, $operationInfo)
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
            '更新广播'
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
     * 设置置顶
     * @param [type] $postId
     * @param [type] $operationInfo
     * @param [type] $topType
     * @return void
     */
    public function setTop($postId, $operationInfo, $topType)
    {
        
    }

    /**
     * 删除广播
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function delete($params, $operationInfo)
    {
        $postId = $params['post_id'] ?? 0;

        DB::transaction(function () use ($postId, $operationInfo){
            $this->postModel->where('id',$postId)->update(
                [
                    'is_del' => 1,
                    'deleted_at' => Carbon::now()->toDateTimeString()
                ]
            );

            $this->postOpLogModel->saveDeleteOpLogDatas([$postId], $operationInfo);

            $this->replyModel->where('post_id', $postId)->update(
                [
                    'is_del' => 1,
                    'deleted_at' => Carbon::now()->toDateTimeString()
                ]
            );
        });
    }

    public function browseList($params, $operatorId)
    {
        $page = $params['page'] ?? 1;
        $perpage = $params['perpage'] ?? 20;

        $leftModels = [
            [
                'table_name' => 'posts',
                'left' => 'posts.id',
                'right' => 'post_browse_records.post_id',
                'conds' => [
                    'is_del' => 0,
                ],
                'conds_search' => [
                    'is_del' => [
                        'query_key' => 'is_del',
                        'operator' => '='
                    ],
                ]
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
            return $this->commonCreate(
                $this->browseRecordModel,
                array_merge($insertDate, $time),
                null,
                $operationInfo,
                '浏览广播',
                false
            );
        }
        
    }

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
}