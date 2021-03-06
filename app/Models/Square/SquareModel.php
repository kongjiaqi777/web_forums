<?php

namespace App\Models\Square;

use App\Models\BaseModel;

class SquareModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'squares';

    // 可以插入表的字段
    public $fillable = [
        'name',
        'creater_id',
        'avatar',
        'label',
        'verify_status',
        'verify_reason',
        'follow_count',
        'created_at',
        'updated_at',
    ];
 
    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'name' => [
                'query_key' => 'name',
                'operator' => '=',
            ],
            'name_like' => [
                'query_key' => 'name',
                'operator' => 'like',
            ],
            'creater_id' => [
                'query_key' => 'creater_id',
                'operator' => '=',
            ],
            'label_like' => [
                'query_key' => 'label',
                'operator' => 'like',
            ],
            'verify_status' => [
                'query_key' => 'verify_status',
                'operator' => '=',
            ],
            'square_id' => [
                'query_key' => 'id',
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
            'verify_status_in' => [
                'query_key' => 'verify_status',
                'operator' => 'in',
            ],
        ];    
    }
 
    // 可以更新的字段
    public $updateable = [
        'name',
        'creater_id',
        'avatar',
        'label',
        'verify_status',
        'verify_reason',
        'follow_count',
        'is_del',
        'deleted_at',
    ];

    public $findable = [
        'id',
        'name',
        'creater_id',
        'avatar',
        'label',
        'verify_status',
        'verify_reason',
        'follow_count',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_del',
    ];
    
    // 可以排序的字段
    public $sortable = [
        'id',
        'created_at'
    ];
}
