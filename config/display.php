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
        'available' => [
            'code' => 10,
            'desc' => '账户可用',
        ],
        'unavailable' => [
            'code' => 20,
            'desc' => '账户不可用'
        ],
        'forbidden' => [
            'code' => 30,
            'desc' => '禁言中'
        ],
    ],

    // 操作类型
    'operation_type' => [
        'create' => [
            'code' => 10,
            'desc' => '创建',
        ],
        'update' => [
            'code' => 20,
            'desc' => '更新',
        ],
        'delete' => [
            'code' => 30,
            'desc' => '删除',
        ],
    ],
    // 操作人类型
    'operator_type' => [
        'common' => [
            'code' => 10,
            'desc' => '论坛用户',
        ],
        'admin' => [
            'code' => 20,
            'desc' => '管理员用户'
        ],
    ],
    // 评论类型
    'reply_type' => [
        'post' => [
            'code' => 10,
            'desc' => '广播评论',
        ],
        'reply' => [
            'code' => 20,
            'desc' => '评论的评论'
        ],
    ],
    // 点赞类型
    'praise_type' => [
        'post_type' => [
            'code' => 10,
            'desc' => '广播点赞'
        ],
        'reply_type' => [
            'code' => 20,
            'desc' => '回复点赞'
        ]
    ],
    // 投诉类型
    'complaint_type' => [
        'post' => [
            'code' => 10,
            'desc' => '广播投诉'
        ],
        'reply' => [
            'code' => 20,
            'desc' => '评论投诉'
        ],
        'square_owner' => [
            'code' => 30,
            'desc' => '广场主投诉'
        ]
    ],
    // 投诉处理状态
    'complaint_verify_status' => [

    ],
    // 广播类型
    'post_type' => [
        'square' => [
            'code' => 10,
            'desc' => '广场广播'
        ],
        'personal' => [
            'code' => 20,
            'desc' => '个人广播'
        ],
    ]
];