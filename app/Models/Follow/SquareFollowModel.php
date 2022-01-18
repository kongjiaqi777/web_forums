<?php

namespace App\Models\Square;

use App\Models\BaseModel;

class SquareFollowModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'square_followers';

    public $timestamps = false;

    // 可以插入表的字段
    public $fillable = [
        'square_id',
        'follower_id',
    ];
 
    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'follower_id' => [
                'query_key' => 'follower_id',
                'operator' => '=',
            ],
            'square_id' => [
                'query_key' => 'square_id',
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
        'follower_id',
        'created_at',
        'deleted_at',
        'is_del',
    ];
}
