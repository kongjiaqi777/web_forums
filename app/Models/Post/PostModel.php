<?php

namespace App\Models\Post;

use App\Models\BaseModel;

class PostModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'posts';

    // 可以插入表的字段
    public $fillable = [
        'square_id',
        'title',
        'content',
        'photo',
        'creater_id',
        'post_type',
    ];
 
    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'square_id' => [
                'query_key' => 'square_id',
                'operator' => '=',
            ],
            'creater_id' => [
                'query_key' => 'creater_id',
                'operator' => '=',
            ],
            'title' => [
                'query_key' => 'title',
                'operator' => '=',
            ],
            'title_like' => [
                'query_key' => 'title',
                'operator' => 'like',
            ],
            'id' => [
                'query_key' => 'id',
                'operator' => '='
            ],
            'post_type' => [
                'query_key' => 'post_type',
                'operator' => '='
            ],
        ];    
    }
 
    // 可以更新的字段
    public $updateable = [
        'title',
        'content',
        'photo',
        'top_rule',
        'reply_count',
        'praise_count',
        'is_del',
        'deleted_at',
    ];

    public $findable = [
        'id',
        'square_id',
        'post_type',
        'creater_id',
        'title',
        'content',
        'top_rule',
        'photo',
        'reply_count',
        'praise_count',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_del',
    ];
}
