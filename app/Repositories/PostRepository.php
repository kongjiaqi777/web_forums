<?php

namespace App\Repositories;

use App\Exceptions\NoStackException;
use App\Repositories\BaseRepository;
use App\Models\Post\PostModel;
use App\Models\Post\PostOpLogModel;
use App\Models\Square\SquareModel;
use App\Models\Post\ReplyModel;
use Carbon\Carbon;

class PostRepository extends BaseRepository
{
    private $postModel;
    private $postOpLogModel;
    private $squareModel;
    private $replyModel;

    public function __construct(
        PostModel $postModel,
        PostOpLogModel $postOpLogModel,
        SquareModel $squareModel,
        ReplyModel $replyModel
    ) {
        $this->postModel = $postModel;
        $this->postOpLogModel = $postOpLogModel;
        $this->squareModel = $squareModel;
        $this->replyModel = $replyModel;
    }

    public function getList($params)
    {
        return $this->postModel->getList($params);
    }

    public function getAll($params)
    {
        return $this->postModel->getAll($params);
    }

    public function createPost($params, $operationInfo)
    {
        $squareId = $params['square_id'] ?? 0;
        $squareInfo = $this->squareModel->getById($squareId);

        if (empty($squareInfo)) {
            throw New NoStackException('广场信息有误，无法创建');
        }

        return $this->commonCreate(
            $this->postModel,
            $params,
            $this->postOpLogModel,
            $operationInfo,
            '创建广播'
        );
    }

    public function detailPost($params)
    {
        $postId = $params['post_id'] ?? 0;
        return $this->postModel->getById($postId);
    }

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

    public function suggest($params)
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

        return [
            'list' => $list,
            'pagination' => $pagination,
        ];
    }

    public function setTop()
    {

    }

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

    public function browseList()
    {

    }

    public function addBrowseRecord()
    {

    }
}