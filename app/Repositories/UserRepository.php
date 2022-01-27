<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Models\User\UserModel;
use App\Models\User\AdminUserModel;
use DB;

class UserRepository extends BaseRepository
{
    private $userModel;
    private $adminUserModel;

    public function __construct(
        UserModel $userModel,
        AdminUserModel $adminUserModel
    ) {
        $this->userModel = $userModel;
        $this->adminUserModel = $adminUserModel;
    }

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

    public function getById($userId)
    {
        return $this->userModel->getById($userId);
    }
}