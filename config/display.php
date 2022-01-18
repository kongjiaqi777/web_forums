<?php

return [
    // 广场审核状态
    'post_verify_status' => [
        [
            'code' => 100,
            'desc' => '待审核',
        ],
        [
            'code' => 200,
            'desc' => '审核成功',
        ],
        [
            'code' => 300,
            'desc' => '审核驳回'
        ],
        [
            'code' => 400,
            'desc' => '申请变更广场主'
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