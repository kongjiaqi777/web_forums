<?php

namespace App\Models\Square;

use App\Models\BaseOpLogModel;

class SquareOpLogModel extends BaseOpLogModel
{

    protected $connection  = 'mysql';
    protected $table       = 'square_op_logs';
    protected $idKey       = 'square_id';
    public $timestamps     = true;

    public $fillable = [
        'square_id',
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
        'square_id',
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
