<?php

namespace App\Models\Post;

use App\Models\BaseModel;

class PraiseModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'post_praises';

    // 可以插入表的字段
    public $fillable = [
        'post_id',
        'user_id',
        'praise_type',
        'reply_id',
    ];

    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'post_id' => [
                'query_key' => 'post_id',
                'operator' => '=',
            ],
            'user_id' => [
                'query_key' => 'user_id',
                'operator' => '=',
            ],
            'praise_type' => [
                'query_key' => 'praise_type',
                'operator' => '='
            ],
            'reply_id' => [
                'query_key' => 'reply_id',
                'operator' => '='
            ],
        ];    
    }

    // 可以更新的字段
    public $updateable = [
        'is_del',
        'deleted_at',
    ];

    // 可以查询到的字段
    public $findable = [
        'id',
        'post_id',
        'user_id',
        'created_at',
        'is_del',
        'deleted_at',
        'praise_type',
    ];
}
