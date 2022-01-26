<?php

return [
    // 广场审核状态
    'square_verify_status' => [
        'waitting_approve' => [
            'code' => 10,
            'desc' => '申请创建',
        ],
        'approved' => [
            'code' => 20,
            'desc' => '已通过',
        ],
        'rejected' => [
            'code' => 30,
            'desc' => '审核驳回'
        ],
        // 申请更换广场主
        'apply_relieve' => [
            'code' => 40,
            'desc' => '申请解除'
        ],
        'dismissed' => [
            'code' => 50,
            'desc' => '已解散'
        ],
    ],
    // 用户状态
    'user_status' => [
        [
            'code' => 10,
            'desc' => '',
        ],
    ],

    // 操作类型
    'operation_type' => [
        [
            'code' => 10,
            'desc' => '创建',
        ],
        [
            'code' => 20,
            'desc' => '更新',
        ],
        [
            'code' => 30,
            'desc' => '删除',
        ],
    ],
    // 操作人类型
    'operator_type' => [
        [
            'code' => 10,
            'desc' => '',
        ],
    ],
    // 评论类型
    'reply_type' => [
        [
            'code' => 10,
            'desc' => '',
        ],
    ],

    'complaint_type',
    'complaint_verify_status'
];