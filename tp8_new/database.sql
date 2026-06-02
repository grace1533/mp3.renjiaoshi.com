<?php

return [
    // 数据库结构 - JYMusic ThinkPHP 8 版本
    
    // 用户表
    'jy_member' => "CREATE TABLE IF NOT EXISTS `jy_member` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户 ID',
        `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
        `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
        `salt` varchar(32) NOT NULL DEFAULT '' COMMENT '密码盐',
        `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
        `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
        `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
        `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
        `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-禁用，1-正常',
        `create_time` datetime DEFAULT NULL COMMENT '创建时间',
        `update_time` datetime DEFAULT NULL COMMENT '更新时间',
        `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`),
        UNIQUE KEY `email` (`email`),
        KEY `mobile` (`mobile`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表'",

    // 歌曲表
    'jy_songs' => "CREATE TABLE IF NOT EXISTS `jy_songs` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '歌曲 ID',
        `name` varchar(100) NOT NULL DEFAULT '' COMMENT '歌曲名称',
        `singer` varchar(100) NOT NULL DEFAULT '' COMMENT '歌手',
        `album_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '专辑 ID',
        `artist_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '艺人 ID',
        `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件 URL',
        `cover_url` varchar(255) NOT NULL DEFAULT '' COMMENT '封面 URL',
        `duration` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '时长 (秒)',
        `file_size` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '文件大小 (字节)',
        `play_num` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '播放次数',
        `download_num` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '下载次数',
        `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-下架，1-上架',
        `create_time` datetime DEFAULT NULL COMMENT '创建时间',
        `update_time` datetime DEFAULT NULL COMMENT '更新时间',
        `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
        PRIMARY KEY (`id`),
        KEY `name` (`name`),
        KEY `singer` (`singer`),
        KEY `album_id` (`album_id`),
        KEY `artist_id` (`artist_id`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='歌曲表'",

    // 专辑表
    'jy_album' => "CREATE TABLE IF NOT EXISTS `jy_album` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '专辑 ID',
        `name` varchar(100) NOT NULL DEFAULT '' COMMENT '专辑名称',
        `artist_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '艺人 ID',
        `cover_url` varchar(255) NOT NULL DEFAULT '' COMMENT '封面 URL',
        `description` text COMMENT '专辑描述',
        `song_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '歌曲数量',
        `play_num` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '播放次数',
        `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-下架，1-上架',
        `publish_time` datetime DEFAULT NULL COMMENT '发布时间',
        `create_time` datetime DEFAULT NULL COMMENT '创建时间',
        `update_time` datetime DEFAULT NULL COMMENT '更新时间',
        `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
        PRIMARY KEY (`id`),
        KEY `name` (`name`),
        KEY `artist_id` (`artist_id`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='专辑表'",

    // 艺人表
    'jy_artist' => "CREATE TABLE IF NOT EXISTS `jy_artist` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '艺人 ID',
        `name` varchar(100) NOT NULL DEFAULT '' COMMENT '艺名',
        `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
        `gender` tinyint(1) NOT NULL DEFAULT 0 COMMENT '性别：0-未知，1-男，2-女',
        `country` varchar(50) NOT NULL DEFAULT '' COMMENT '国家/地区',
        `birthday` date DEFAULT NULL COMMENT '生日',
        `description` text COMMENT '艺人介绍',
        `fans_num` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '粉丝数',
        `song_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '歌曲数量',
        `album_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '专辑数量',
        `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-禁用，1-正常',
        `create_time` datetime DEFAULT NULL COMMENT '创建时间',
        `update_time` datetime DEFAULT NULL COMMENT '更新时间',
        `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
        PRIMARY KEY (`id`),
        KEY `name` (`name`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='艺人表'",

    // 用户收藏表
    'jy_favorite' => "CREATE TABLE IF NOT EXISTS `jy_favorite` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '收藏 ID',
        `user_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '用户 ID',
        `song_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '歌曲 ID',
        `create_time` datetime DEFAULT NULL COMMENT '创建时间',
        PRIMARY KEY (`id`),
        UNIQUE KEY `user_song` (`user_id`, `song_id`),
        KEY `user_id` (`user_id`),
        KEY `song_id` (`song_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户收藏表'",

    // 管理员组表
    'jy_auth_group' => "CREATE TABLE IF NOT EXISTS `jy_auth_group` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '组 ID',
        `title` varchar(50) NOT NULL DEFAULT '' COMMENT '组标题',
        `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-禁用，1-正常',
        `rules` text COMMENT '权限规则 IDs',
        `create_time` datetime DEFAULT NULL COMMENT '创建时间',
        `update_time` datetime DEFAULT NULL COMMENT '更新时间',
        PRIMARY KEY (`id`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员组表'",

    // 管理员组访问表
    'jy_auth_group_access' => "CREATE TABLE IF NOT EXISTS `jy_auth_group_access` (
        `uid` int(10) unsigned NOT NULL COMMENT '用户 ID',
        `group_id` int(10) unsigned NOT NULL COMMENT '组 ID',
        PRIMARY KEY (`uid`, `group_id`),
        KEY `group_id` (`group_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员组访问表'",

    // 管理员规则表
    'jy_auth_rule' => "CREATE TABLE IF NOT EXISTS `jy_auth_rule` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则 ID',
        `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则标识',
        `title` varchar(50) NOT NULL DEFAULT '' COMMENT '规则标题',
        `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '类型：1-规则，2-菜单',
        `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-禁用，1-正常',
        `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '规则表达式',
        `create_time` datetime DEFAULT NULL COMMENT '创建时间',
        `update_time` datetime DEFAULT NULL COMMENT '更新时间',
        PRIMARY KEY (`id`),
        UNIQUE KEY `name` (`name`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员规则表'",

    // 系统配置表
    'jy_config' => "CREATE TABLE IF NOT EXISTS `jy_config` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置 ID',
        `name` varchar(50) NOT NULL DEFAULT '' COMMENT '配置名',
        `value` text COMMENT '配置值',
        `type` varchar(20) NOT NULL DEFAULT 'string' COMMENT '配置类型',
        `group` varchar(20) NOT NULL DEFAULT 'basic' COMMENT '配置分组',
        `description` varchar(255) NOT NULL DEFAULT '' COMMENT '配置说明',
        `sort` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '排序',
        `create_time` datetime DEFAULT NULL COMMENT '创建时间',
        `update_time` datetime DEFAULT NULL COMMENT '更新时间',
        PRIMARY KEY (`id`),
        UNIQUE KEY `name` (`name`),
        KEY `group` (`group`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表'",

    // 操作日志表
    'jy_action_log' => "CREATE TABLE IF NOT EXISTS `jy_action_log` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志 ID',
        `action_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '行为 ID',
        `user_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '用户 ID',
        `action_ip` bigint(20) NOT NULL DEFAULT 0 COMMENT '操作 IP',
        `model` varchar(50) NOT NULL DEFAULT '' COMMENT '模型',
        `record_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '记录 ID',
        `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
        `create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `action_id` (`action_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志表'",

    // 初始化管理员账号 (密码：admin123)
    'init_data' => "INSERT INTO `jy_member` (`username`, `password`, `salt`, `email`, `nickname`, `status`) VALUES 
    ('admin', 'e10adc3949ba59abbe56e057f20f883e', 'jymusic', 'admin@jymusic.com', '超级管理员', 1);
    
    INSERT INTO `jy_auth_group` (`id`, `title`, `status`, `rules`) VALUES 
    (1, '超级管理员组', 1, '*');
    
    INSERT INTO `jy_auth_group_access` (`uid`, `group_id`) VALUES 
    (1, 1);
    
    INSERT INTO `jy_config` (`name`, `value`, `type`, `group`, `description`) VALUES 
    ('web_site_title', 'JYMusic 音乐分享平台', 'string', 'basic', '网站标题'),
    ('web_site_close', '0', 'bool', 'basic', '站点开关'),
    ('web_off_msg', '网站维护中，请稍后访问', 'text', 'basic', '关闭提示'),
    ('url_model', '1', 'num', 'basic', 'URL 模式');",
];
