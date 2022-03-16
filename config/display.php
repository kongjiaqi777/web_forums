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
        'undeal' => [
            'code' => 10,
            'desc' => '未处理'
        ],
        'over' => [
            'code' => 60,
            'desc' => '正常'
        ],
        'warning' => [
            'code' => 70,
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
    ],
    'msg_type' => [
        'square_approve' => [
            'code' => 10,
            'desc' => '广场审核通过'
        ],
        'square_reject' => [
            'code' => 11,
            'desc' => '广场审核驳回'
        ],
        'complaint_reject' => [
            'code' => 12,
            'desc' => '投诉驳回'
        ],
        'complaint_deal' => [
            'code' => 13,
            'desc' => '处理投诉'
        ],
        'complaint_post_delete' => [
            'code' => 14,
            'desc' => '后台删帖'
        ],
        'complaint_post_forbidden' => [
            'code' => 15,
            'desc' => '后台禁言七天'
        ],
        'complaint_post_forbidden_forever' => [
            'code' => 16,
            'desc' => '后台永久禁言'
        ],
        'complaint_reply_delete' => [
            'code' => 17,
            'desc' => '后台删回复'
        ],
        'complaint_reply_forbidden' => [
            'code' => 18,
            'desc' => '后台禁言七天'
        ],
        'complaint_reply_forbidden_forever' => [
            'code' => 19,
            'desc' => '后台永久禁言'
        ],
        'post_praise' => [
            'code' => 20,
            'desc' => '广播被点赞'
        ],
        'reply_praise' => [
            'code' => 21,
            'desc' => '回复被点赞'
        ],
        'post_reply' => [
            'code' => 22,
            'desc' => '广播被回复'
        ],
        'reply_reply' => [
            'code' => 23,
            'desc' => '回复被回复'
        ],
        'follow' => [
            'code' => 24,
            'desc' => '被关注' 
        ],
        'square_top' => [
            'code' => 25,
            'desc' => '后台广场置顶'
        ],
        'homepage_top' => [
            'code' => 26,
            'desc' => '后台首页置顶'
        ],
        'owner_top' => [
            'code' => 27,
            'desc' => '广场主置顶'
        ],
        'admin_delete_post' => [
            'code' => 28,
            'desc' => '后台删广播'
        ],
        'owner_delete_post' => [
            'code' => 29,
            'desc' => '广场主删广播'
        ],
        'admin_delete_reply' => [
            'code' => 30,
            'desc' => '后台删回复'
        ],
        'owner_delete_reply' => [
            'code' => 31,
            'desc' => '广场主删回复'
        ],
        'switch_approve' => [
            'code' => 32,
            'desc' => '卸任通过'
        ],
        'switch_reject' => [
            'code' => 33,
            'desc' => '卸任驳回'
        ],
        'switch_notice' => [
            'code' => 34,
            'desc' => '通知用户广场主变更'
        ],
        'owner_warning' => [
            'code' => 35,
            'desc' => '通知广场主被投诉'
        ],
    ],
    'is_del' => [
        'deleted' => [
            'code' => 1,
            'desc' => '已删除',
        ],
        'displaying' => [
            'code' => 0,
            'desc' => '展示中'
        ],
    ],
    'top_rule' => [
        'zero' => [
            'code' => 0,
            'desc' => '未置顶'
        ],
        'one' => [
            'code' => 1,
            'desc' => '吧内置顶'
        ],
        'two' => [
            'code' => 2,
            'desc' => '吧内置顶'
        ],
        'three' => [
            'code' => 3,
            'desc' => '吧内置顶'
        ],
        'four' => [
            'code' => 4,
            'desc' => '吧内置顶'
        ],
        'five' => [
            'code' => 5,
            'desc' => '首页置顶'
        ],
    ],
    'top_rule_select' => [
        'none' => [
            'code' => 0,
            'desc' => '未置顶'
        ],
        'internal_top' => [
            'code' => 1,
            'desc' => '吧内置顶'
        ],
        'homepage_top' => [
            'code' => 5,
            'desc' => '首页置顶'
        ],
    ],
];