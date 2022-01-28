<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     /**
     * @api {get} /v1/common/config 状态配置code[持续更新]
     * @apiVersion 1.0.0
     * @apiName 状态配置code
     * @apiGroup Common
     * @apiPermission 允许不登录
     * @apiSuccessExample Success-Response
     * {
    "code": 0,
    "msg": "success",
    "info": {
        "square_verify_status": {
            "waitting_approve": {
                "code": 10,
                "desc": "申请创建"
            },
            "approved": {
                "code": 20,
                "desc": "已通过"
            },
            "rejected": {
                "code": 30,
                "desc": "审核驳回"
            },
            "apply_relieve": {
                "code": 40,
                "desc": "申请解除"
            },
            "dismissed": {
                "code": 50,
                "desc": "已解散"
            }
        },
        "user_status": {
            "available": {
                "code": 10,
                "desc": "账户可用"
            },
            "unavailable": {
                "code": 20,
                "desc": "账户不可用"
            },
            "forbidden": {
                "code": 30,
                "desc": "禁言中"
            }
        },
        "operation_type": {
            "create": {
                "code": 10,
                "desc": "创建"
            },
            "update": {
                "code": 20,
                "desc": "更新"
            },
            "delete": {
                "code": 30,
                "desc": "删除"
            }
        },
        "operator_type": {
            "common": {
                "code": 10,
                "desc": "论坛用户"
            },
            "admin": {
                "code": 20,
                "desc": "管理员用户"
            }
        },
        "reply_type": [
            {
                "code": 10,
                "desc": ""
            }
        ],
        "praise_type": {
            "post_type": {
                "code": 10,
                "desc": "广播点赞"
            },
            "reply_type": {
                "code": 20,
                "desc": "回复点赞"
            }
        },
        "complaint_type": {
            "post": {
                "code": 10,
                "desc": "广播投诉"
            },
            "reply": {
                "code": 20,
                "desc": "评论投诉"
            },
            "square_owner": {
                "code": 30,
                "desc": "广场主投诉"
            }
        },
        "complaint_verify_status": [],
        "post_type": {
            "square": {
                "code": 10,
                "desc": "广场广播"
            },
            "personal": {
                "code": 20,
                "desc": "个人广播"
            }
        }
    }
}
     */
    public function getConfigList()
    {
        return $this->buildSucceed(config('display'));
    }
}
