<?php

namespace App\Repositories;

use App\Exceptions\NoStackException;
use App\Repositories\BaseRepository;
use App\Models\Square\SquareModel;
use App\Models\Square\SquareOpLogModel;
use App\Models\Follow\SquareFollowModel;
use App\Models\Post\PostModel;
use App\Models\User\UserModel;
use DB;
use Carbon\Carbon;
use App\Libs\UtilLib;
use App\Libs\MessageLib;


class SquareRepository extends BaseRepository
{
    private $squareModel;
    private $squareOpLogModel;
    private $squareFollowModel;
    private $postModel;
    private $squareStatusEffective;
    private $userModel;

    public function __construct(
        SquareModel $squareModel,
        SquareOpLogModel $squareOpLogModel,
        SquareFollowModel $squareFollowModel,
        PostModel $postModel,
        UserModel $userModel
    ) {
        $this->squareModel = $squareModel;
        $this->squareOpLogModel = $squareOpLogModel;
        $this->squareFollowModel = $squareFollowModel;
        $this->postModel = $postModel;
        $this->userModel = $userModel;
        $this->squareStatusEffective = [
            config('display.square_verify_status.approved.code'),
            config('display.square_verify_status.dismissed.code'),
        ];
    }

    /**
     *  广场列表-分页
     * @param [type] $params 筛选条件
     * @param [boolean] $isJoinFollow 是否查询当前登录用户关注情况
     * @param [numeric] $operatorId 当前登录用户ID
     * @return array
     */
    public function getList($params, $isJoinFollow=false, $operatorId=0, $isJoinCreaterInfo=false)
    {
        $page = $params['page'] ?? 1;
        $perpage = $params['perpage'] ?? 20;

        $fields = array_merge($this->squareModel->findable, [
            DB::raw('0 as is_follow'),
            DB::raw('null as creater_email'),
            DB::raw('null as creater_nickname')
        ]);
        $res = $this->getDataList(
            $this->squareModel,
            $fields,
            $params,
            $page,
            $perpage
        );

        $list = $res['list'] ?? [];
        if ($list && $isJoinFollow && $operatorId) {
            $list = $this->joinFollowFlag($list, $operatorId);
        }

        if ($list && $isJoinCreaterInfo) {
            $list = $this->joinCreaterInfo($list);
        }

        $res ['list'] = $list;
        return $res;
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
     * @param string $message 更新的备注
     * @param string $sendMessage 消息的msgcode
     * @return array 更新之后的信息
     */
    public function updateSquare($params, $operationInfo, $message = '更新广场', $sendMessage=null)
    {
        $squareId = $params['square_id'] ?? 0;

        $squareInfo = $this->squareModel->getById($squareId);
        if (!$squareInfo) {
            throw New NoStackException('广场不存在');
        }

        DB::transaction(function () use ($squareId, $params, $operationInfo, $message, $operationTypeSpec, $sendMessage, $squareInfo) {
            try {
                // 更新广场
                $this->commonUpdate(
                    $squareId,
                    $this->squareModel,
                    $this->squareOpLogModel,
                    $params,
                    $operationInfo,
                    $message,
                    'update'
                );

                // 发消息
                if ($sendMessage) {
                    $userList = [$squareInfo['creater_id']];
                    if ($sendMessage == config('display.msg_type.switch_approve.code')) {
                        // 切换广场主的时候消息发给广场关注人和广场主
                        $followUser = $this->squareFollowModel->getAll(
                            [
                                'square_id' => $squareId,
                                'is_del' => 0,
                                'created_at' => Carbon::now()->toDateTimeString()
                            ], [
                                'id' => 'desc'
                            ], [
                                'follow_user_id'
                            ]
                        );
                        MessageLib::sendMessage(
                            config('display.msg_type.switch_notice.code'),
                            $followUser,
                            [
                                'square_id' => $squareId,
                                'user_id' => $params['user_id']
                            ]
                        );
                    }

                    MessageLib::sendMessage(
                        $sendMessage,
                        $userList,
                        [
                            'square_id' => $squareId
                        ]
                    );
                }
            } catch (\Exception $e) {
                Log::error(sprintf('更新广场失败[Param][%s][Code][%s][Message][%s]', json_encode($params), $e->getCode(), $e->getMessage()));
                throw New NoStackException('更新广场信息失败');
            }
        });
        return $this->squareModel->getById($squareId);
    }

    /**
     * 删除广场
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function deleteSquare($params, $operationInfo)
    {
        $squareId = $params['square_id'] ?? 0;
        return DB::transaction(function () use ($squareId, $operationInfo) {
            try {
                // 删除广场
                $updateData = [
                    'is_del' => 1,
                    'deleted_at' => Carbon::now()->toDateTimeString(),
                    'verify_status' => config('display.square_verify_status.dismissed.code')
                ];

                $this->commonUpdate(
                    $squareId,
                    $this->squareModel,
                    $this->squareOpLogModel,
                    $updateData,
                    $operationInfo,
                    '删除广场',
                    'delete',
                );

                // 删除广场时删除广场下全部广播
                $this->postModel->updateByCondition(
                    [
                        'square_id' => $squareId,
                        'is_del' => 0
                    ], [
                        'is_del' => 1,
                        'deleted_at' => Carbon::now()->toDateTimeString()
                    ],
                    false
                );
            } catch (\Exception $e) {
                Log::error(sprintf('删除广场失败[SquareId][%s][Code][%s][Message][%s]', $squareId, $e->getCode(), $e->getMessage()));
                throw New NoStackException('删除广场信息失败');
            }
        });
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

            $this->squareModel->where('id', $squareId)->where('follow_count', '>', 0)->decrement('follow_count');
            return $res;
        });
    }

    /**
     * 广场名称和标签模糊搜索，按照关注人数倒序排列，支持分页
     * @param [array] $params 筛选条件
     * @param [boolean] $isJoinFollow 是否查询当前登录用户关注情况
     * @param [numeric] $operatorId 当前登录用户ID
     * @return void
     */
    public function suggest($params, $isJoinFollow=false, $operatorId=0)
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
   
        if ($list && $isJoinFollow && $operatorId) {
            $list = $this->joinFollowFlag($list, $operatorId);
        }

        return [
            'list' => $list,
            'pagination' => $pagination,
        ];
    }

    /**
     * 给广场列表join用户是否关注标识
     * @param [array] $list 广场列表
     * @param [numeric] $operatorId 当前登录用户ID
     * @return array $list 广场列表
     */
    private function joinFollowFlag($list, $operatorId)
    {
        $squareIds = array_column($list, 'id');

        $followList = $this->squareFollowModel->getAll(
            [
                'square_id' => $squareIds,
                'follow_user_id' => $operatorId,
                'is_del' => 0
            ]
        );

        if ($followList) {
            $followList = UtilLib::indexBy($followList, 'square_id');

            foreach ($list as &$detail) {
                $squareId = $detail['id'] ?? 0;
                $followFlag = $followList[$squareId] ?? 0;
                if ($followFlag) {
                    $detail['is_follow'] = 1;
                } else {
                    $detail['is_follow'] = 0;
                }
            }
        }

        return $list;
    }

    /**
     * 我关注的广场列表
     * @param [type] $params 筛选条件
     * @param [type] $operatorId 当前登录用户ID
     * @return void
     */
    public function myFollowList($params, $operatorId)
    {
        $page = $params ['page'] ?? 1;
        $perpage = $params ['perpage'] ?? 20;

        $fields = [
            'squares.*',
            'follow_square_records.square_id'
        ];

        $conds = [
            'follow_user_id' => $operatorId,
            'is_del' => 0,
        ];

        $leftModels = [
            [
                'table_name' => 'squares',
                'left' => 'squares.id',
                'right' => 'follow_square_records.square_id',
                'conds' => [
                    'is_del' => 0,
                    'verify_status' => $this->squareStatusEffective
                ],
                'conds_search' => [
                    'is_del' => [
                        'query_key' => 'is_del',
                        'operator' => '='
                    ],
                    'verify_status' => [
                        'query_key' => 'verify_status',
                        'operator' => 'in'
                    ]
                ]
            ]
        ];
    
        return $this->getDataList(
            $this->squareFollowModel,
            $fields,
            $conds,
            $page,
            $perpage,
            $leftModels
        );
    }

    /**
     * 给广场列表join广场主email
     * @param [type] $list
     * @return void
     */
    private function joinCreaterInfo($list)
    {
        $createrIds = array_column($list, 'creater_id');

        if ($createrIds) {
            $createrIds = array_unique($createrIds);
        }

        $createrInfo = $this->userModel->getAll([
            'id' => $createrIds
        ], [
            'id' => 'desc'
        ], [
            'id',
            'email',
            'nickname'
        ]);

        if ($createrInfo) {
            $createrInfo = UtilLib::indexBy($createrInfo, 'id');
            foreach ($list as &$detail) {
                $createrId = $detail['creater_id'] ?? 0;
                $createrDetail = $createrInfo[$createrId] ?? [];
               
                $email = $createrDetail['email'] ?? '';
                $nickname = $createrDetail['nickname'] ?? '';

                $detail['creater_email'] = $email;
                $detail['creater_nickname'] = $nickname;
                
            }
        }
        return $list;
    }
}