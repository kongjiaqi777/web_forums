<?php

namespace App\Models\Message;

use App\Models\BaseModel;

class MessageModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'system_messages';

    // 可以插入表的字段
    public $fillable = [
        'template_id',
        'user_id',
        'msg_type',
        'msg_body',
        'msg_title',
        'url',
        'is_read',
        'created_at',
        'updated_at',
    ];
 
    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'id' => [
                'query_key' => 'id',
                'operator' => '=',
            ],
            'user_id' => [
                'query_key' => 'user_id',
                'operator' => '='
            ],
            'is_read' => [
                'query_key' => 'is_read',
                'operator' => '='
            ],
        ];    
    }
 
    // 可以更新的字段
    public $updateable = [
        'is_read',
        'is_del',
        'updated_at',
        'deleted_at',
    ];

    // 可以查询到的字段
    public $findable = [
        'id',
        'user_id',
        'msg_type',
        'msg_body',
        'msg_title',
        'url',
        'is_read',
        'created_at',
    ];

    public $sortable = [
        'id',
        'created_at'
    ];
}
