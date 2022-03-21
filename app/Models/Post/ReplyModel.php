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
        'post_id',
        'user_id',
        'content',
        'first_reply_id',
        'created_at',
        'updated_at',
    ];
 
    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'parent_id' => [
                'query_key' => 'parent_id',
                'operator' => '=',
            ],
            'parent_user_id' => [
                'query_key' => 'parent_user_id',
                'operator' => '=',
            ],
            'first_reply_id' => [
                'query_key' => 'first_reply_id',
                'operator' => '='
            ],
            'post_id' => [
                'query_key' => 'post_id',
                'operator' => '='
            ],
            'user_id' => [
                'query_key' => 'user_id',
                'operator' => '='
            ],
            'reply_id' => [
                'query_key' => 'id',
                'operator' => '='
            ],
            'reply_type' => [
                'query_key' => 'reply_type',
                'operator' => '='
            ],
            'is_del' => [
                'query_key' => 'is_del',
                'operator' => '='
            ],
        ];    
    }
 
    // 可以更新的字段
    public $updateable = [
        'reply_count',
        'praise_count',
        'is_del',
        'updated_at',
        'deleted_at',
    ];

    // 可以查询到的字段
    public $findable = [
        'id',
        'reply_count',
        'praise_count',
        'reply_type',
        'parent_id',
        'parent_user_id',
        'post_id',
        'user_id',
        'content',
        'created_at',
        'updated_at',
        'is_del',
        'deleted_at',
        'first_reply_id',
    ];

    // 可以排序的字段
    public $sortable = [
        'id',
        'created_at'
    ];
}
