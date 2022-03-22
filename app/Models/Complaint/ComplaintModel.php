<?php

namespace App\Models\Complaint;

use App\Models\BaseModel;


class ComplaintModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'complaints';

    // 可以插入表的字段
    public $fillable = [
        'post_id',
        'reply_id',
        'square_id',
        'complaint_user_id',
        'complaint_type',
        'user_id',
        'content',
        'photo',
    ];
 
    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'post_id' => [
                'query_key' => 'post_id',
                'operator' => '=',
            ],
            'reply_id' => [
                'query_key' => 'reply_id',
                'operator' => '='
            ],
            'square_id' => [
                'query_key' => 'square_id',
                'operator' => '='
            ],
            'complaint_user_id' => [
                'query_key' => 'complaint_user_id',
                'operator' => '='
            ],
            'complaint_type' => [
                'query_key' => 'complaint_type',
                'operator' => '='
            ],
            'user_id' => [
                'query_key' => 'user_id',
                'operator' => '='
            ],
            'created_start' => [
                'query_key' => 'created_at',
                'operator' => '>='
            ],
            'created_end' => [
                'query_key' => 'created_at',
                'operator' => '<='
            ],
            'verify_status' => [
                'query_key' => 'verify_status',
                'operator' => '='
            ],
            'complaint_type_in' => [
                'query_key' => 'complaint_type',
                'operator' => 'in'
            ],
        ];    
    }
 
    // 可以更新的字段
    public $updateable = [
        'verify_status',
        'verify_reason',
        'is_del',
        'deleted_at',
        'updated_at',
    ];

    // 可以查询到的字段
    public $findable = [
        'id',
        'post_id',
        'reply_id',
        'square_id',
        'complaint_user_id',
        'complaint_type',
        'user_id',
        'content',
        'photo',
        'verify_status',
        'verify_reason',
        'is_del',
        'deleted_at',
        'updated_at',
        'created_at',
    ];

    public $sortable = [
        'id',
        'created_at',
    ];
}
