<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Models\User\UserModel;
use App\Models\User\AdminUserModel;
use App\Models\User\UserOpLogModel;
use DB;
use App\Exceptions\NoStackException;
use App\Libs\UtilLib;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Hash;
use App\Models\Follow\UserFollowModel;
use App\Models\Post\PostModel;


class UserRepository extends BaseRepository
{
    private $userModel;
    private $adminUserModel;
    private $userOpLogModel;
    private $userFollowModel;
    private $postModel;

    public function __construct(
        UserModel $userModel,
        AdminUserModel $adminUserModel,
        UserOpLogModel $userOpLogModel,
        UserFollowModel $userFollowModel,
        PostModel $postModel
    ) {
        $this->userModel = $userModel;
        $this->adminUserModel = $adminUserModel;
        $this->userOpLogModel = $userOpLogModel;
        $this->userFollowModel = $userFollowModel;
        $this->postModel = $postModel;
    }

    /**
     * 模糊搜索普通用户
     * @param [type] $params
     * @return void
     */
    public function suggestUser($params, $isJoinFollow=false, $operatorId=0)
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

        if ($list && $isJoinFollow && $operatorId) {
            $list = $this->joinUserFollow($list, $operatorId);
        }

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
    public function getById($userId, $isJoinFollow=false, $operatorId=0)
    {

        $detail = $this->userModel->getById($userId);
        if ($detail && $isJoinFollow && $operatorId) {
            $detail = $this->joinUserFollow([$detail], $operatorId)[0];
        }

        $userId = $detail['id'] ?? 0;

        // 广播数目和获赞数目
        $postInfo = $this->postModel->where([
            'is_del' => 0,
            'creater_id' => $userId
        ])->select(
            DB::raw('count(id) as posts_count, sum(praise_count) as praise_count')
        )->first()->toArray();

        return array_merge($detail, $postInfo);
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

    /**
     * 验证管理端用户登录
     * @param [type] $email
     * @param [type] $password
     * @return void
     */
    public function checkAdminUserAuth($email, $password)
    {
        $user = $this->adminUserModel->getFirstByCondition(['email' => $email]);
        if (empty($user)) {
            throw new NoStackException('用户不存在！');
        }

        $dbPassword = $user['password'] ?? '';
        if (\password_verify($password, $dbPassword)) {
            $user = Arr::except($user, ['password']);
            return $user;
        } else {
            throw New NoStackException('密码不正确！');
        }
    }

    /**
     * 设置token
     * @param [type] $userId 用户ID
     * @param [type] $email  邮箱
     * @param [type] $password 密码
     * @param [type] $userName 用户名
     * @param [type] $operatorType 用户类型
     * @return void
     */
    public function setToken($userId, $email, $password, $userName, $operatorType)
    {

        $key = md5($email. $password . $userId . $operatorType);
       
        $content = [
            'user_id' => $userId,
            'nickname' => $userName,
            'email' => $email
        ];
        $value = json_encode($content);
        Redis::set($key, $value);
        Redis::expire($key, intval(env('SESSION_EXPIRE', 86400)));
        return $key;
    }

    /**
     * 删除token
     * @param [type] $token
     * @return void
     */
    public function deleteToken($token)
    {
        $data = Redis::del($token);
        return $data;
    }

    /**
     * 管理端用户注册
     * @param [type] $params
     * @return void
     */
    public function adminSignUp($params)
    {
        $params ['password'] = Hash::make($params['password']);
        return $this->commonCreateNoLog($this->adminUserModel, $params);
    }

    private function joinUserFollow($list, $operatorId)
    {
        $userIds = array_column($list, 'id');

        $followList = $this->userFollowModel->getAll(
            [
                'follow_user_id' => $userIds,
                'user_id' => $operatorId,
                'is_del' => 0
            ]
        );

        if ($followList) {
            $followList = UtilLib::indexBy($followList, 'follow_user_id');
        }

        foreach ($list as &$userInfo) {
            $userInfo['is_follow'] = 0;
            $userId = $userInfo['id'] ?? 0;
            $followFlag = $followList[$userId] ?? false;
            if ($followFlag) {
                $userInfo['is_follow'] = 1;
            }
        }

        return $list;
    }
}