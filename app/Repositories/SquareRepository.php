<?php

namespace App\Repositories;

use App\Exceptions\NoStackException;
use App\Repositories\BaseRepository;
use App\Models\Square\SquareModel;
use App\Models\Square\SquareOpLogModel;
use App\Models\Follow\SquareFollowModel;
use App\Models\Post\PostModel;
use DB;
use Carbon\Carbon;


class SquareRepository extends BaseRepository
{
    private $squareModel;
    private $squareOpLogModel;
    private $squareFollowModel;
    private $postModel;

    public function __construct(
        SquareModel $squareModel,
        SquareOpLogModel $squareOpLogModel,
        SquareFollowModel $squareFollowModel,
        PostModel $postModel
    ) {
        $this->squareModel = $squareModel;
        $this->squareOpLogModel = $squareOpLogModel;
        $this->squareFollowModel = $squareFollowModel;
        $this->postModel = $postModel;
    }

    /**
     * 广场列表-分页
     * @param [array] $params 筛选条件
     * @return array
     */
    public function getList($params)
    {
        // $params['status'] = $this->squareStatusEffective;
        return $this->squareModel->getList($params);
    }

    /**
     * 广场列表-不分页
     * @param [array] $params 筛选条件
     * @return array
     */
    public function getAll($params)
    {
        return $this->squareModel->getAll($params);
    }

    /**
     * 根据广场ID获取广场详情
     * @param [numeric] $squareId
     * @param [boolean] $isJoinPostCount 是否查询广场下广播数目
     * @param [boolean] $isJoinFollow 是否查询当前登录用户关注情况
     * @param [numeric] $operatorId 当前登录用户ID
     * @return array
     */
    public function detail($squareId, $isJoinPostCount=false, $isJoinFollow=false, $operatorId=0)
    {
        $detail = $this->squareModel->getById($squareId);

        if (empty($detail)) {
            return $detail;
        }

        if ($isJoinPostCount) {
            $postCount = $this->postModel->where([
                'square_id' => $squareId,
                'is_del' => 0
            ])->count();
    
            $detail['post_count'] = $postCount;
        }
       
        if ($isJoinFollow && $operatorId) {
            $followFlag = $this->squareFollowModel->where([
                'square_id' => $squareId,
                'follow_user_id' => $operatorId,
                'is_del' => 0
            ])->first();
    
            $detail['is_follow'] = $followFlag ? 1 : 0;
        }
        return $detail;
    }

    /**
     * 创建广场
     * @param [array] $params 创建信息
     * @param [array] $operationInfo 操作人信息
     * @return numeric square_id 广场ID
     */
    public function createSquare($params, $operationInfo)
    {
        return $this->commonCreate(
            $this->squareModel,
            $params,
            $this->squareOpLogModel,
            $operationInfo,
            '创建广场'
        );
    }

    /**
     * 更新广场信息
     * @param [array] $params 更新信息
     * @param [array] $operationInfo 操作人信息
     * @return array 更新之后的信息
     */
    public function updateSquare($params, $operationInfo, $message = '更新广场', $operationTypeSpec='update')
    {
        $squareId = $params['square_id'] ?? 0;

        $squareInfo = $this->squareModel->getById($squareId);
        if (!$squareInfo) {
            throw New NoStackException('广场不存在');
        }

        $this->commonUpdate(
            $squareId,
            $this->squareModel,
            $this->squareOpLogModel,
            $params,
            $operationInfo,
            $message,
            $operationTypeSpec
        );

        return $this->squareModel->getById($squareId);
    }

    /**
     * 关注广场
     * @param [numeric] $squareId 广场ID
     * @param [numeric] $userId 登录用户ID
     * @return array 广场信息
     */
    public function setFollow($squareId, $userId)
    {
        $squareInfo = $this->squareModel->findOne([
            'id' => $squareId,
            'verify_status' => $this->squareStatusEffective,
            'is_del' => 0
        ]);
        if (!$squareInfo) {
            throw New NoStackException('广场信息不合法！');
        }

        $squareCheck = $this->squareFollowModel->getFirstByCondition([
            'square_id' => $squareId,
            'follow_user_id' => $userId,
            'is_del' => 0
        ]);

        if ($squareCheck) {
            throw New NoStackException('已关注，请勿重复操作！');
        }

        $squareInfo = DB::transaction(function () use ($squareId, $userId) {
            $followId = $this->squareFollowModel->insertGetId(
                [
                    'square_id' => $squareId,
                    'follow_user_id' => $userId,
                    'created_at' => Carbon::now()->toDateTimeString()
                ]
            );
    
            $this->squareModel->where('id', $squareId)->increment('follow_count');
            return $followId;
        });
        return $squareInfo;
    }

    /**
     * 取关广场
     * @param [numeric] $squareId 广场ID
     * @param [numeric] $userId 当前登录用户ID
     * @return void
     */
    public function cancelFollow($squareId, $userId)
    {
        $squareInfo = $this->squareModel->findOne([
            'id' => $squareId,
            'verify_status' => $this->squareStatusEffective,
            'is_del' => 0
        ]);
        if (!$squareInfo) {
            throw New NoStackException('广场信息不存在！');
        }

        $squareCheck = $this->squareFollowModel->getFirstByCondition([
            'square_id' => $squareId,
            'follow_user_id' => $userId,
            'is_del' => 0
        ]);

        if (!$squareCheck) {
            throw New NoStackException('还未关注');
        }

        DB::transaction(function () use ($squareId, $userId) {
            $res = $this->squareFollowModel->updateByCondition([
                'square_id' => $squareId,
                'follow_user_id' => $userId,
                'is_del' => 0
            ], [
                'is_del' => 1,
                'deleted_at' => Carbon::now()->toDateTimeString()
            ]);

            $this->squareModel->where('id', $squareId)->decrement('follow_count');
            return $res;
        });
    }

    /**
     * 广场名称和标签模糊搜索，按照关注人数倒序排列，支持分页
     * @param [array] $params 筛选条件
     * @return void
     */
    public function suggest($params)
    {
        $name = $params['name'] ?? '';
        $page = $params['page'] ?? 1;
        $perpage = $params['perpage'] ?? 20;
        unset($params['name']);

        $searchAble = $this->squareModel->getSearchAble();
        $condsSearch = array_intersect_key($searchAble, $params);

        $fields = [
            'id',
            'name',
            'label',
            'avatar',
            'verify_status',
            'follow_count',
            'is_del',
            DB::raw('0 as is_follow')
        ];

        $query = $this->squareModel;

        if ($condsSearch) {
            $query = $this->getQueryBuilder($query, $params, $condsSearch, $fields);
        }

        // 模糊搜索
        $query = $query->where(function ($query) use ($name) {
            $query->orWhere('name', 'like', '%'.$name.'%')
            ->orWhere('label', 'like', '%'.$name.'%');
        });

        $offset = ($page - 1) * $perpage;
        $pagination = $this->squareModel->getPaginate($fields, $query, $page, $perpage);

        $list = $query->select($fields)
            ->offset($offset)
            ->limit($perpage)
            ->orderBy('follow_count', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->all();

        return [
            'list' => $list,
            'pagination' => $pagination,
        ];
    }
}