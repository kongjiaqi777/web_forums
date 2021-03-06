<?php

namespace App\Models\Follow;

use App\Models\BaseModel;

class SquareFollowModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'follow_square_records';

    // 可以插入表的字段
    public $fillable = [
        'square_id',
        'follow_user_id',
        'created_at',
        'updated_at',
    ];
 
    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'follow_user_id' => [
                'query_key' => 'follow_user_id',
                'operator' => '=',
            ],
            'square_id' => [
                'query_key' => 'square_id',
                'operator' => '=',
            ],
            'is_del' => [
                'query_key' => 'is_del',
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
        'square_id',
        'follow_user_id',
        'created_at',
        'is_del',
    ];

    // 可以排序的字段
    public $sortable = [
        'id',
        'created_at'
    ];
}
