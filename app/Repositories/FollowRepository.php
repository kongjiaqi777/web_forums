<?php

namespace App\Repositories;

use App\Exceptions\NoStackException;
use App\Repositories\BaseRepository;
use App\Models\Follow\UserFollowModel;
use App\Models\Follow\SquareFollowModel;
use App\Models\User\UserModel;
use App\Libs\MessageLib;
use DB;
use Carbon\Carbon;
use Log;


class FollowRepository extends BaseRepository
{
    private $userFollowModel;
    private $squareFollowModel;
    private $userModel;

    public function __construct(
        UserFollowModel $userFollowModel,
        SquareFollowModel $squareFollowModel,
        UserModel $userModel
    ) {
        $this->userFollowModel = $userFollowModel;
        $this->squareFollowModel = $squareFollowModel;
        $this->userModel = $userModel;
    }

    /**
     * 查询用户ID关注的用户列表
     * @param [type] $userId
     * @param int $page
     * @param int $perpage
     * @return void
     */
    public function getFollowUserList($userId, $page=1, $perpage=20)
    {
        return $this->userFollowModel->getList(
            [
                'follow_user_id' => $userId,
                'is_del' => 0,
                'sort', ['created_at' => 'desc'],
                'page' => $page,
                'perpage' => $perpage
            ]
        );
    }

    /**
     * 查询用户ID关注的广场列表
     * @param [type] $userId
     * @param int $page
     * @param int $perpage
     * @return void
     */
    public function getFollowSquareList($userId, $page=1, $perpage=20)
    {
        return $this->squareFollowModel->getList(
            [
                'follow_user_id' => $userId,
                'is_del' => 0,
                'sort' => ['created_at' => 'desc'],
                'page' => $page,
                'perpage' => $perpage
            ]
        );
    }

    /**
     * 根据广场ID获取关注广场的用户列表
     * @param [type] $params
     * @return void
     */
    public function getSquareFollowAll($params)
    {
        return $this->squareFollowModel->getAll($params);
    }

    /**
     * 关注用户
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function setFollowUser($params, $operationInfo)
    {
        $userId = $operationInfo['operator_id'] ?? 0;
        $followUserId = $params['follow_user_id'] ?? 0;

        $followInfo = $this->userFollowModel->getFirstByCondition([
            'user_id' => $userId,
            'follow_user_id' => $followUserId,
            'is_del' => 0
        ]);

        if ($followInfo) {
            throw New NoStackException('已关注，请勿重复操作');
        }

        if ($userId == $followUserId) {
            throw New NoStackException('操作有误');
        }

        $insert = [
            'user_id' => $userId,
            'follow_user_id' => $followUserId,
            'is_mutual' => 0
        ];

        $userFollow = $this->userFollowModel->getFirstByCondition([
            'user_id' => $followUserId,
            'follow_user_id' => $userId,
            'is_del' => 0
        ]);

        return DB::transaction(function () use ($insert, $userFollow, $userId, $followUserId, $params) {
            try {
                // 相互关注记录修改
                if ($userFollow) {
                    $insert ['is_mutual'] = 1;
                    $userFollowId = $userFollow['id'] ?? 0;
                    $this->userFollowModel
                    ->where('id', $userFollowId)
                    ->update([
                        'is_mutual' => 1
                    ]);
                }

                // 发消息
                MessageLib::sendMessage(
                    config('display.msg_type.follow.code'),
                    [$followUserId],
                    [
                        'user_id' => $followUserId
                    ]
                );

                // 关注人数+1
                $this->userModel->incrementField($userId, 'follows_count');
                // 粉丝数+1
                $this->userModel->incrementField($followUserId, 'fans_count');

                // 添加关注记录
                return $this->commonCreateNoLog(
                    $this->userFollowModel,
                    $insert
                );
            } catch (\Exception $e) {
                Log::error(sprintf('取消关注失败[Params][%s][Code][%s][Message][%s]',json_encode($params), $e->getCode(), $e->getMessage()));
                throw New NoStackException('关注用户失败');
            }
            
        });
    }

    /**
     * 取关用户
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function cancelFollowUser($params, $operationInfo)
    {
        $userId = $operationInfo['operator_id'] ?? 0;
        $followUserId = $params['follow_user_id'] ?? 0;

        $followInfo = $this->userFollowModel->getFirstByCondition([
            'user_id' => $userId,
            'follow_user_id' => $followUserId,
            'is_del' => 0
        ]);

        if (empty($followInfo)) {
            throw New NoStackException('没找到关注记录');
        }

        return DB::transaction(function () use ($followInfo, $followUserId, $userId, $params) {
            try {
                // 相互关注记录修改
                $isMutual = $followInfo['is_mutual'] ?? 0;
                if ($isMutual) {
                    $this->userFollowModel->where([
                        'is_del' => 0,
                        'user_id' => $followUserId,
                        'follow_user_id' => $userId
                    ])->update([
                        'is_mutual' => 0
                    ]);
                }

                // 关注人数-1
                $this->userModel->decrementField($userId, 'follows_count');
                // 粉丝数-1
                $this->userModel->decrementField($followUserId, 'fans_count');

                // 删除关注记录
                $followRecordId = $followInfo['id'];
                return $this->userFollowModel->where('id', $followRecordId)->update([
                    'is_del' => 1,
                    'deleted_at' => Carbon::now()->toDateTimeString()
                ]);
            } catch (\Exception $e) {
                Log::error(sprintf('取消关注失败[Params][%s][Code][%s][Message][%s]',json_encode($params), $e->getCode(), $e->getMessage()));
                throw New NoStackException('取消关注失败');
            }
        });

    }

    /**
     * 关注我的用户列表
     * @param [type] $params
     * @return void
     */
    public function myFollowUserList($params)
    {
        $userId = $params['user_id'] ?? 0;
        $page = $params['page'] ?? 1;
        $perpage = $params['perpage'] ?? 20;

        $leftModels = [
            [
                'table_name' => 'users',
                'left' => 'users.id',
                'right' => 'follow_user_records.follow_user_id',
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

        return $this->getDataList(
            $this->userFollowModel,
            [
                'follow_user_records.id',
                DB::raw('follow_user_records.follow_user_id as user_id'),
                'follow_user_records.is_mutual',
                'users.nickname',
                'users.avatar',
                'users.label',
            ], [
                'user_id' => $userId,
                'is_del' => 0
            ],
            $page,
            $perpage,
            $leftModels,
            ['id' => 'desc']
        );
    }

    /**
     * 我的粉丝列表
     * @param [type] $params
     * @return void
     */
    public function myFansUserList($params)
    {
        $followUserId = $params['follow_user_id'] ?? 0;
        $page = $params['page'] ?? 1;
        $perpage = $params['perpage'] ?? 20;

        $leftModels = [
            [
                'table_name' => 'users',
                'left' => 'users.id',
                'right' => 'follow_user_records.user_id',
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

        return $this->getDataList(
            $this->userFollowModel,
            [
                'follow_user_records.id',
                'follow_user_records.user_id',
                'follow_user_records.is_mutual',
                'users.nickname',
                'users.avatar',
                'users.label',
            ], [
                'follow_user_id' => $followUserId,
                'is_del' => 0
            ],
            $page,
            $perpage,
            $leftModels,
            ['id' => 'desc']
        );
    }
}