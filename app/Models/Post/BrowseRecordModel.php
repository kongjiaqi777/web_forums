<?php

namespace App\Models\Post;

use App\Models\BaseModel;


class BrowseRecordModel extends BaseModel
{
    // 用来放验证，表字段过滤
    protected $connection = 'mysql';

    // 表名称
    protected $table = 'post_browse_records';

    // 可以插入表的字段
    public $fillable = [
        'user_id',
        'post_id',
        'browsed_at',
    ];
  
    // 可以作为筛选条件的字段
    function getSearchAble()
    {
        return [
            'user_id' => [
                'query_key' => 'user_id',
                'operator' => '='
            ],
            'post_id' => [
                'query_key' => 'post_id',
                'operator' => '='
            ],
        ];    
    }

    // 可以更新的字段
    public $updateable = [
        'is_del',
        'updated_at',
        'browsed_at',
        'deleted_at',
    ];
 
    public $findable = [
        'id',
        'user_id',
        'post_id',
        'browsed_at',
        'is_del',
    ];
}
