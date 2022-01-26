<?php

namespace App\Models\Message;

use App\Models\BaseOpLogModel;

class MessageOpLogModel extends BaseOpLogModel
{
    protected $connection  = 'mysql';
    protected $table       = 'message_op_logs';
    protected $idKey       = 'message_id';
    public $timestamps     = true;

    public $fillable = [
        'message_id',
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
        'message_id',
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
