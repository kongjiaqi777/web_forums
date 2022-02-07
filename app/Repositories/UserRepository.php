<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Models\User\UserModel;
use App\Models\User\AdminUserModel;
use App\Models\User\UserOpLogModel;
use DB;

class UserRepository extends BaseRepository
{
    private $userModel;
    private $adminUserModel;
    private $userOpLogModel;

    public function __construct(
        UserModel $userModel,
        AdminUserModel $adminUserModel,
        UserOpLogModel $userOpLogModel
    ) {
        $this->userModel = $userModel;
        $this->adminUserModel = $adminUserModel;
        $this->userOpLogModel = $userOpLogModel;
    }

    /**
     * 模糊搜索普通用户
     * @param [type] $params
     * @return void
     */
    public function suggestUser($params)
    {
        $name = $params['nickname'] ?? '';
        $page = $params['page'] ?? 1;
        $perpage = $params['perpage'] ?? 20;
        unset($params['nickname']);

        $searchAble = $this->userModel->getSearchAble();
        $condsSearch = array_intersect_key($searchAble, $params);

        $fields = [
            'id',
            'nickname',
            'label',
            'avatar',
            'email',
            DB::raw('0 as is_mutual'),
            DB::raw('0 as is_follow')
        ];

        $query = $this->userModel;

        if ($condsSearch) {
            $query = $this->getQueryBuilder($query, $params, $condsSearch, $fields);
        }

        // 模糊搜索
        $query = $query->where(function ($query) use ($name) {
            $query->orWhere('nickname', 'like', '%'.$name.'%')
            ->orWhere('label', 'like', '%'.$name.'%');
        });

        $offset = ($page - 1) * $perpage;
        $pagination = $this->userModel->getPaginate($fields, $query, $page, $perpage);

        $list = $query->select($fields)
            ->offset($offset)
            ->limit($perpage)
            ->orderBy('follows_count', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->all();

        return [
            'list' => $list,
            'pagination' => $pagination,
        ];
    }

    /**
     * 根据用户ID获取普通用户信息
     * @param [type] $userId
     * @return void
     */
    public function getById($userId)
    {
        return $this->userModel->getById($userId);
    }

    /**
     * 修改普通用户信息
     * @param [type] $params
     * @param [type] $operationInfo
     * @param string $message
     * @return void
     */
    public function update($params, $operationInfo, $message = '修改用户信息')
    {
        $userId = $params['id'] ?? 0;

        return $this->commonUpdate(
            $userId,
            $this->userModel,
            $this->userOpLogModel,
            $params,
            $operationInfo,
            $message
        );
    }
}