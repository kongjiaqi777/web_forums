-- ----------------------------
-- Records of users
-- ----------------------------
CREATE TABLE IF NOT EXISTS `users` (
	`id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT '用户ID',
    `source_id` INT(11) UNSIGNED NOT NULL COMMENT '用户来源网站ID',
	`nickname` VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户昵称',
    `avatar` VARCHAR(256) COMMENT '头像',
    `status` TINYINT(3) DEFAULT 10 COMMENT '用户状态:10正常/20禁言',
    `is_auth` TINYINT(1) DEFAULT 0 COMMENT '实名认证状态:否0/是1',
    `email` VARCHAR(256) NOT NULL COMMENT '邮箱账号',
    `label` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标签',
    `follows_count` INT(8) UNSIGNED DEFAULT 0 COMMENT '关注人数目',
    `posts_count` INT(8) UNSIGNED DEFAULT 0 COMMENT '发广播数目',
    `fans_count` INT(8) UNSIGNED DEFAULT 0 COMMENT '粉丝数目',
	`is_del` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否删除:未删除0/已删除1',
	`created_at` DATETIME NOT NULL COMMENT '创建时间',
	`updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `deleted_at` DATETIME COMMENT '删除时间',
	PRIMARY KEY (`id`),
    KEY `emailx` (`email`(20)),
    KEY `source_idx` (`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4 COLLATE=UTF8MB4_UNICODE_CI AUTO_INCREMENT=100 COMMENT '用户信息表';

-- ----------------------------
-- Records of user operation log
-- ----------------------------
CREATE TABLE IF NOT EXISTS `user_op_logs` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT 'ID',
    `user_id` INT(11) NOT NULL COMMENT '用户ID',
    `operation_type` tinyint(3) unsigned NOT NULL COMMENT '操作类型:创建10/更新20/删除30',
    `before_change` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '修改前信息',
    `after_change` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '修改后信息',
    `comment` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '操作信息',
    `operator_id` int(11) unsigned NOT NULL COMMENT '操作人ID',
    `operator_type` int(3) unsigned DEFAULT 10 COMMENT '操作人类型:普通用户10/管理员20',
    `operator_ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '操作人IP',
    `created_at` datetime NOT NULL COMMENT '创建时间',
    PRIMARY KEY (`id`),
    KEY `user_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4 COLLATE=UTF8MB4_UNICODE_CI COMMENT '用户信息操作日志表';

-- ----------------------------
-- Records of user login log
-- ----------------------------
CREATE TABLE IF NOT EXISTS `user_login_logs` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT 'ID',
    `user_id` INT(11) NOT NULL COMMENT '用户ID',
    `request_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '请求信息',
    `created_at` datetime NOT NULL COMMENT '创建时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 COLLATE=UTF8_UNICODE_CI COMMENT '用户登陆日志表';

-- ----------------------------
-- Records of user followers
-- ----------------------------
CREATE TABLE IF NOT EXISTS `follow_user_records` (
	`id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT 'ID',
	`user_id` INT(11) NOT NULL COMMENT '用户ID',
    `follow_user_id` INT(11) NOT NULL COMMENT '关注人ID',
    `is_mutual` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否是互相关注',
    `created_at` DATETIME NOT NULL COMMENT '创建时间',
    `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `deleted_at` DATETIME COMMENT '删除时间',
    `is_del` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否删除:未删除0/已删除1',
	PRIMARY KEY (`id`),
    KEY `user_idx` (`user_id`),
    KEY `follow_user_idx` (`follow_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 COLLATE=UTF8_UNICODE_CI COMMENT '用户关注表';

-- ----------------------------
-- Records of square
-- ----------------------------
CREATE TABLE IF NOT EXISTS `squares` (
	`id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT '广场ID',
	`name` VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '广场名称',
    `creater_id` INT(11) NOT NULL COMMENT '创建人ID',
    `avatar` VARCHAR(256) COMMENT '广场头像',
    `label` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '广场标签',
    `verify_status` TINYINT(3) DEFAULT 10 COMMENT '审核状态:待审核10/审核通过20/审核驳回30/申请更换广场主40/解除50',
    `verify_reason` VARCHAR(256) COMMENT '审核原因',
    `follow_count` INT(8) DEFAULT 0 COMMENT '关注成员数目',
	`created_at` DATETIME NOT NULL COMMENT '创建时间',
	`updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `deleted_at` DATETIME COMMENT '删除时间',
    `is_del` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否删除:未删除0/已删除1',
	PRIMARY KEY (`id`),
    KEY `creater_idx` (`creater_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1000 COMMENT '广场信息表';

-- ----------------------------
-- Records of square operation log
-- ----------------------------
CREATE TABLE IF NOT EXISTS `square_op_logs` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT 'ID',
    `square_id` INT(11) NOT NULL COMMENT '广场ID',
    `operation_type` tinyint(3) unsigned NOT NULL COMMENT '操作类型:创建10/更新20/删除30',
    `before_change` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '修改前信息',
    `after_change` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '修改后信息',
    `comment` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '操作信息',
    `operator_id` int(11) unsigned NOT NULL COMMENT '操作人ID',
    `operator_type` int(3) unsigned DEFAULT 100 COMMENT '操作人类型',
    `operator_ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '操作人IP',
    `created_at` datetime NOT NULL COMMENT '创建时间',
    PRIMARY KEY (`id`),
    KEY `square_idx` (`square_id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 COLLATE=UTF8_UNICODE_CI COMMENT '广场信息操作日志表';

-- ----------------------------
-- Records of square followers
-- ----------------------------
CREATE TABLE IF NOT EXISTS `follow_square_records` (
	`id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT 'ID',
    `square_id` INT(11) NOT NULL COMMENT '广场ID',
    `follow_user_id` INT(11) NOT NULL COMMENT '关注人ID',
    `created_at` datetime NOT NULL COMMENT '关注时间',
    `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `is_del` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除:未删除0/已删除1',
    `deleted_at` DATETIME COMMENT '删除时间',
    PRIMARY KEY (`id`),
    KEY `square_idx` (`square_id`),
    KEY `follow_user_idx` (`follow_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=UTF8_UNICODE_CI COMMENT '广场关注表';

-- ----------------------------
-- Records of post
-- ----------------------------
CREATE TABLE IF NOT EXISTS `posts` (
	`id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT '广播ID',
    `square_id` INT(11) DEFAULT 0 NOT NULL COMMENT '所属广场ID',
    `post_type` INT(3) DEFAULT 10 NOT NULL COMMENT '广播类型:广场广播10/个人广播20',
	`title` VARCHAR(40) NOT NULL COMMENT '标题',
    `content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
    `photo` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '图片',
    `creater_id` int(11) NOT NULL COMMENT '创建人ID',
    `top_rule` TINYINT(1) DEFAULT 0 COMMENT '置顶规则:首页置顶5/广场置顶1～4/无置顶0',
    `reply_count` INT(8) UNSIGNED DEFAULT 0 COMMENT '回复数目',
    `praise_count` INT(8) UNSIGNED DEFAULT 0 COMMENT '点赞数目',
	`created_at` datetime NOT NULL COMMENT '创建时间',
	`updated_at` datetime ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `deleted_at` datetime COMMENT '删除时间',
    `is_del` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除:未删除0/已删除1',
	PRIMARY KEY (`id`),
    KEY `square_idx` (`square_id`),
    KEY `creater_idx` (`creater_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=10000 COMMENT '广播信息表';

-- ----------------------------
-- Records of post browse history
-- ----------------------------
CREATE TABLE IF NOT EXISTS `post_browse_records` (
	`id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT '浏览记录ID',
    `user_id` INT(11) NOT NULL COMMENT '用户ID',
	`post_id` INT(11) NOT NULL COMMENT '广播ID',
	`created_at` datetime NOT NULL COMMENT '创建时间',
    `updated_at` datetime ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `browsed_at` datetime NOT NULL COMMENT '最新浏览时间',
    `deleted_at` datetime COMMENT '删除时间',
    `is_del` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除:未删除0/已删除1',
	PRIMARY KEY (`id`),
    KEY `user_idx` (`user_id`),
    KEY `post_idx` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT '广播浏览记录表';

-- ----------------------------
-- Records of post operation log
-- ----------------------------
CREATE TABLE IF NOT EXISTS `post_op_logs` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT 'ID',
    `post_id` INT(11) NOT NULL COMMENT '广播ID',
    `operation_type` tinyint(3) unsigned NOT NULL COMMENT '操作类型:创建10/更新20/删除30',
    `before_change` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '修改前信息',
    `after_change` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '修改后信息',
    `comment` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '操作信息',
    `operator_id` int(11) unsigned NOT NULL COMMENT '操作人ID',
    `operator_type` int(3) unsigned DEFAULT 100 COMMENT '操作人类型',
    `operator_ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '操作人IP',
    `created_at` datetime NOT NULL COMMENT '创建时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT '广播信息操作日志表';

-- ----------------------------
-- Records of reply for post
-- ----------------------------
CREATE TABLE IF NOT EXISTS `post_replys` (
	`id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT '评论ID',
    `reply_type` TINYINT(3) DEFAULT 10 COMMENT '回复类型:广播评论10/对reply_type=10的评论20/对reply_type=20的评论30',
    `first_reply_id` INT(11) NOT NULL COMMENT '一级评论ID，用于统计回复数目',
    `parent_id` INT(11) NOT NULL COMMENT '广播评论ID，用于筛选',
    `parent_user_id` INT(11) NOT NULL COMMENT '上级评论用户的ID',
    `reply_count` INT(11) DEFAULT 0 COMMENT '回复数目',
    `praise_count` INT(8) UNSIGNED DEFAULT 0 COMMENT '点赞数目',
    `post_id` INT(11) NOT NULL COMMENT '广播ID',
    `user_id` INT(11) NOT NULL COMMENT '操作人ID',
    `content` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '内容',
    `created_at` datetime NOT NULL COMMENT '创建时间',
    `updated_at` datetime ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `is_del` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除:未删除0/已删除1',
    `deleted_at` datetime COMMENT '删除时间',
    PRIMARY KEY (`id`),
    KEY `post_idx` (`post_id`),
    KEY `parent_idx` (`parent_id`),
    KEY `user_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=UTF8_UNICODE_CI COMMENT '广播回复表';

-- ----------------------------
-- Records of praise for post
-- ----------------------------
CREATE TABLE IF NOT EXISTS `post_praises` (
	`id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT '点赞记录ID',
    `post_id` INT(11) NOT NULL COMMENT '广播ID',
    `reply_id` INT(11) NOT NULL DEFAULT 0 COMMENT '回复ID',
    `praise_type` tinyint(2) NOT NULL DEFAULT 10 COMMENT '点赞类型:广播点赞10/回复点赞20', 
    `user_id` INT(11) NOT NULL COMMENT '操作人ID',
    `created_at` datetime NOT NULL COMMENT '创建时间',
    `updated_at` datetime ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `is_del` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除:未删除0/已删除1',
    `deleted_at` datetime COMMENT '删除时间',
    PRIMARY KEY (`id`),
    KEY `post_idx` (`post_id`),
    KEY `user_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=UTF8_UNICODE_CI COMMENT '广播点赞表';

-- ----------------------------
-- Records of complaints
-- ----------------------------
CREATE TABLE IF NOT EXISTS `complaints` (
	`id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT '投诉记录ID',
    `post_id` INT(11) DEFAULT 0 COMMENT '广播ID',
    `reply_id` INT(11) DEFAULT 0 COMMENT '评论ID',
    `square_id` INT(11) DEFAULT 0 COMMENT '广场ID',
    `complaint_user_id` INT(11) NOT NULL COMMENT '被投诉人ID',
    `complaint_type` TINYINT(3) DEFAULT 10 COMMENT '投诉类型：广播投诉10/评论投诉20/广场主投诉30',
    `user_id` INT(11) NOT NULL COMMENT '投诉人ID',
    `content` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '内容',
    `photo` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '图片',
    `verify_status` TINYINT(3) DEFAULT 10 COMMENT '审核状态',
    `verify_reason` VARCHAR(256) COMMENT '审核原因',
    `created_at` datetime NOT NULL COMMENT '创建时间',
    `updated_at` datetime ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `is_del` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除:未删除0/已删除1',
    `deleted_at` datetime COMMENT '删除时间',
    PRIMARY KEY (`id`),
    KEY `user_idx` (`user_id`),
    KEY `complaint_user_idx` (`complaint_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=UTF8_UNICODE_CI COMMENT '广播投诉表';

-- ----------------------------
-- Records of complaint operation log
-- ----------------------------
CREATE TABLE IF NOT EXISTS `complaints_op_logs` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT 'ID',
    `complaint_id` INT(11) NOT NULL COMMENT '投诉ID',
    `operation_type` tinyint(3) unsigned NOT NULL COMMENT '操作类型:创建10/更新20/删除30',
    `before_change` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '修改前信息',
    `after_change` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '修改后信息',
    `comment` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '操作信息',
    `operator_id` int(11) unsigned NOT NULL COMMENT '操作人ID',
    `operator_type` int(3) unsigned DEFAULT 100 COMMENT '操作人类型',
    `operator_ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '操作人IP',
    `created_at` datetime NOT NULL COMMENT '创建时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT '投诉信息操作日志表';

-- ----------------------------
-- Records of system message template
-- ----------------------------
CREATE TABLE IF NOT EXISTS `system_message_template` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT '模版ID',
    `msg_type` TINYINT(5) DEFAULT 10 NOT NULL COMMENT '消息类型',
    `msg_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '消息内容',
    `msg_title` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '消息标题',
    `url` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '跳转链接URL',
    `param` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '替换参数',
    `created_at` datetime NOT NULL COMMENT '创建时间',
    `updated_at` datetime ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `is_del` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除:未删除0/已删除1',
    `deleted_at` datetime COMMENT '删除时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=UTF8_UNICODE_CI COMMENT '系统消息模版';

-- ----------------------------
-- Records of system message
-- ----------------------------
CREATE TABLE IF NOT EXISTS `system_messages` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT '消息ID',
    `template_id` INT(11) NOT NULL COMMENT '模版ID',
    `user_id` INT(11) NOT NULL COMMENT '收件人ID',
    `msg_type` TINYINT(5) DEFAULT 10 NOT NULL COMMENT '消息类型',
    `msg_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '消息内容',
    `msg_title` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '消息标题',
    `url` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '跳转链接URL',
    `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已读:未读0/已读1',
    `created_at` datetime NOT NULL COMMENT '创建时间',
    `updated_at` datetime ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `is_del` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除:未删除0/已删除1',
    `deleted_at` datetime COMMENT '删除时间',
    PRIMARY KEY (`id`),
    KEY `user_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=UTF8_UNICODE_CI COMMENT '系统消息';

-- ----------------------------
-- Records of message operation log
-- ----------------------------
CREATE TABLE IF NOT EXISTS `message_op_logs` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT 'ID',
    `message_id` INT(11) NOT NULL COMMENT '消息ID',
    `operation_type` tinyint(3) unsigned NOT NULL COMMENT '操作类型:创建10/更新20/删除30',
    `before_change` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '修改前信息',
    `after_change` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '修改后信息',
    `comment` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '操作信息',
    `operator_id` int(11) unsigned NOT NULL COMMENT '操作人ID',
    `operator_type` int(3) unsigned DEFAULT 100 COMMENT '操作人类型',
    `operator_ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '操作人IP',
    `created_at` datetime NOT NULL COMMENT '创建时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT '消息信息操作日志表';

-- ----------------------------
-- Records of admin user
-- ----------------------------
CREATE TABLE IF NOT EXISTS `admin_users` (
	`id` INT(11) UNSIGNED AUTO_INCREMENT COMMENT '用户ID',
	`nickname` VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户昵称',
    `email` VARCHAR(256) NOT NULL COMMENT '邮箱账号',
	`is_del` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否删除:未删除0/已删除1',
	`created_at` DATETIME NOT NULL COMMENT '创建时间',
	`updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `deleted_at` DATETIME COMMENT '删除时间',
	PRIMARY KEY (`id`),
    KEY `emailx` (`email`(20))
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4 COLLATE=UTF8MB4_UNICODE_CI AUTO_INCREMENT=3000 COMMENT '管理端用户信息表';
