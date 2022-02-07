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
        'reply_comment' => [
            'code' => 30,
            'desc' => '回复评论'
        ]
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
    // 投诉处理
    'complaint_verify_status_op' => [
        'reject' => [
            'code' => 10,
            'desc' => '驳回'
        ],
        'deleted_only' => [
            'code' => 20,
            'desc' => '删除帖子或回复'
        ],
        'deleted_and_forbidden7days' => [
            'code' => 30,
            'desc' => '删除帖子或回复并禁言七天'
        ],
        'deleted_and_forbiddenforever' => [
            'code' => 40,
            'desc' => '删除帖子或回复并永久禁言'
        ]
    ],

    // 投诉状态
    'complaint_verify_status' => [
        'undeal' => [
            'code' => 10,
            'desc' => '未处理'
        ],
        'over' => [
            'code' => 20,
            'desc' => '正常'
        ],
        'deleted' => [
            'code' => 30,
            'desc' => '已删帖'
        ],
        'forbidden' => [
            'code' => 40,
            'desc' => '禁言中'
        ],
        'forbidden_forever' => [
            'code' => 50,
            'desc' => '永久禁言'
        ],
    ],
    'owner_complaint_verify_op' => [
        'reject' => [
            'code' => 10,
            'desc' => '驳回'
        ],
        'warning' => [
            'code' => 20,
            'desc' => '警告广场主'
        ],
    ],
    'owner_complaint_verify_status' => [
        'over' => [
            'code' => 10,
            'desc' => '正常'
        ],
        'warning' => [
            'code' => 20,
            'desc' => '警告'
        ]
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