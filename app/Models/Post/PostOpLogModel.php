<?php

namespace App\Models\Post;

use App\Models\BaseOpLogModel;

class PostOpLogModel extends BaseOpLogModel
{

    protected $connection  = 'mysql';
    protected $table       = 'post_op_logs';
    protected $idKey       = 'post_id';
    public $timestamps     = true;

    public $fillable = [
        'post_id',
        'operation_type',
        'before_change',
        'after_change',
        'comment',
        'operator_id',
        'operator_type',
        'operator_ip',
    ];

    public $findable = [
        'id',
        'post_id',
        'operation_type',
        'before_change',
        'after_change',
        'comment',
        'operator_id',
        'operator_type',
        'operator_ip',
        'created_at'
    ];
}
