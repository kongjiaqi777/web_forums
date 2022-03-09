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
        'is_mutual',
        'created_at',
        'updated_at',
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
            'is_del' => [
                'query_key' => 'is_del',
                'operator' => '='
            ],
        ];    
    }
 
    // 可以更新的字段
    public $updateable = [
        'is_del',
        'deleted_at',
        'is_mutual',
        'updated_at',
    ];

    public $findable = [
        'id',
        'follow_user_id',
        'user_id',
        'created_at',
        'is_del',
        'is_mutual',
    ];

    // 可以排序的字段
    public $sortable = [
        'id',
        'created_at'
    ];
}
