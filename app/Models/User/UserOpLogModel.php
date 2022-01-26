<?php

namespace App\Models\User;

use App\Models\BaseOpLogModel;

class UserOpLogModel extends BaseOpLogModel
{
    protected $connection  = 'mysql';
    protected $table       = 'user_op_logs';
    protected $idKey       = 'user_id';
    public $timestamps     = true;

    public $fillable = [
        'user_id',
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
        'user_id',
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
