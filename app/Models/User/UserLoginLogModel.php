<?php

namespace App\Models\User;

use App\Models\BaseModel;

class UserLoginLogModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'user_login_logs';

    // 可以插入表的字段
    public $fillable = [
        'user_id',
        'request_json',
    ];

    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'user_id' => [
                'query_key' => 'user_id',
                'operator' => '=',
            ],
        ];    
    }

    // 可以更新的字段
    public $updateable = [
    ];

    // 可以查询到的字段
    public $findable = [
        'user_id',
        'request_json',
    ];
}
