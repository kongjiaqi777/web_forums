<?php

namespace App\Models\Post;

use App\Models\BaseModel;

class ReplyModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'post_replys';

    // 可以插入表的字段
    public $fillable = [
        'reply_type',
        'parent_id',
        'parent_user_id',
        'reply_count',
        'praise_count',
        'post_id',
        'content',
        'user_id',
    ];
 
    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'reply_type' => [
                'query_key' => 'reply_type',
                'operator' => '=',
            ],
            'post_id' => [
                'query_key' => 'post_id',
                'operator' => '=',
            ],
            'user_id' => [
                'query_key' => 'user_id',
                'operator' => '=',
            ],
        ];    
    }
 
    // 可以更新的字段
    public $updateable = [
        'is_del',
        'deleted_at',
        'reply_count',
        'praise_count',
        'updated_at',
    ];

    public $findable = [
        'id',
        'reply_type',
        'parent_id',
        'parent_user_id',
        'reply_count',
        'praise_count',
        'post_id',
        'content',
        'user_id',
        'created_at',
        // 'deleted_at',
        'is_del',
        // 'updated_at',
    ];
}
