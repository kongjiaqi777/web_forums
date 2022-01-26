<?php

namespace App\Models\User;

use App\Models\BaseModel;

class AdminUserModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'admin_users';

    // 可以插入表的字段
    public $fillable = [
        'nickname',
        'email',
    ];

    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'email' => [
                'query_key' => 'email',
                'operator' => '=',
            ],
        ];    
    }

    // 可以更新的字段
    public $updateable = [
    ];

    // 可以查询到的字段
    public $findable = [
        'nickname',
        'email',
    ];
}