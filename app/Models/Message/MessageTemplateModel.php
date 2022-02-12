<?php

namespace App\Models\Message;

use App\Models\BaseModel;

class MessageTemplateModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'system_message_template';

    // 可以插入表的字段
    public $fillable = [
        'msg_type',
        'msg_body',
        'msg_title',
        'url',
        'param',
        'created_at',
        'updated_at',
    ];
 
    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'msg_type' => [
                'query_key' => 'msg_type',
                'operator' => '=',
            ],
        ];    
    }
 
    // 可以更新的字段
    public $updateable = [
    ];

    // 可以查询到的字段
    public $findable = [
        'id',
        'msg_type',
        'msg_body',
        'msg_title',
        'url',
        'param',
    ];
}
