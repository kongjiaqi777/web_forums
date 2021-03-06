<?php

namespace App\Models\Complaint;

use App\Models\BaseOpLogModel;

class ComplaintOpLogModel extends BaseOpLogModel
{
    protected $connection  = 'mysql';
    protected $table       = 'complaints_op_logs';
    protected $idKey       = 'complaint_id';
    public $timestamps     = true;

    public $fillable = [
        'complaint_id',
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
        'complaint_id',
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
