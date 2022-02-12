<?php

namespace App\Models\User;

use App\Exceptions\NoStackException;
use App\Models\BaseModel;

class UserModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'users';

    // 可以插入表的字段
    public $fillable = [
        'nickname',
        'avatar',
        'label',
        'status',
        'is_auth',
        'source_id',
        'email',
        'created_at',
        'updated_at',
    ];
 
    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'nickname' => [
                'query_key' => 'nickname',
                'operator' => '=',
            ],
            'nickname_like' => [
                'query_key' => 'nickname',
                'operator' => 'like',
            ],
            'label_like' => [
                'query_key' => 'label',
                'operator' => 'like',
            ],
            'email' => [
                'query_key' => 'email',
                'operator' => '=',
            ],
            'user_id' => [
                'query_key' => 'id',
                'operator' => '='
            ],
            'ids' => [
                'query_key' => 'id',
                'operator' => 'in'
            ],
        ];    
    }
 
    // 可以更新的字段
    public $updateable = [
        'nickname',
        'avatar',
        'label',
        'status',
        'is_auth',
        'email',
        'follows_count',
        'posts_count',
        'fans_count',
        'is_del',
    ];

    public $findable = [
        'id',
        'nickname',
        'avatar',
        'label',
        'status',
        'is_auth',
        'source_id',
        'email',
        'follows_count',
        'posts_count',
        'fans_count',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_del',
    ];

    /**
     * 根据用户ID获取昵称列表
     * @param [type] $userIds
     * @return void
     */
    public function getUserNameList($userIds)
    {
        $userIds = array_unique($userIds);
        if ($userIds) {
            return $this->getAll(
                ['id' => $userIds],
                ['id' => 'desc'],
                ['nickname', 'email', 'id', 'avatar']
            );
        }
        return [];
    }

    /**
     * 字段自增1
     * @param [type] $userId
     * @param [type] $fieldName
     * @return void
     */
    public function incrementField($userId, $fieldName)
    {
        if (!in_array($fieldName, $this->updateable)) {
            throw New NoStackException('不支持修改的字段');
        }
        return $this->where('id', $userId)->increment($fieldName);
    }

    /**
     * 字段自减1
     * @param [type] $userId
     * @param [type] $fieldName
     * @return void
     */
    public function decrementField($userId, $fieldName)
    {
        if (!in_array($fieldName, $this->updateable)) {
            throw New NoStackException('不支持修改的字段');
        }
        return $this->where('id', $userId)->where($fieldName, '>', 0)->decrement($fieldName);
    }
}
