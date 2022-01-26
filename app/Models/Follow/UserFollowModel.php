<?php

namespace App\Models\Follow;

use App\Models\BaseModel;

class UserFollowModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'follow_user_records';

    // 可以插入表的字段
    public $fillable = [
        'user_id',
        'follow_user_id',
    ];
 
    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'user_id' => [
                'query_key' => 'user_id',
                'operator' => '=',
            ],
            'follow_user_id' => [
                'query_key' => 'follow_user_id',
                'operator' => '=',
            ],
        ];    
    }
 
    // 可以更新的字段
    public $updateable = [
        'is_del',
        'deleted_at',
    ];

    public $findable = [
        'id',
        'follow_user_id',
        'user_id',
        'created_at',
        'is_del',
    ];
}
