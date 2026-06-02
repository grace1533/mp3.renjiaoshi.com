<?php

return [
    // 数据库类型
    'type' => 'mysql',
    
    // 服务器地址
    'hostname' => '127.0.0.1',
    
    // 数据库名
    'database' => 'jymusic',
    
    // 用户名
    'username' => 'root',
    
    // 密码
    'password' => '',
    
    // 端口
    'hostport' => '3306',
    
    // 连接 DSN
    'dsn' => '',
    
    // 数据库连接参数
    'params' => [],
    
    // 数据库编码默认采用 utf8mb4
    'charset' => 'utf8mb4',
    
    // 数据库表前缀
    'prefix' => 'jy_',
    
    // 数据库部署方式:0 集中式 (单一服务器),1 分布式 (主从服务器)
    'deploy' => 0,
    
    // 数据库读写是否分离 主从式有效
    'rw_separate' => false,
    
    // 读写分离后 主服务器数量
    'master_num' => 1,
    
    // 指定从服务器序号
    'slave_no' => '',
    
    // 模型写入后自动读取主服务器
    'read_master' => false,
    
    // 是否严格检查字段是否存在
    'fields_strict' => true,
    
    // 数据集返回类型
    'resultset_type' => 'array',
    
    // 自动写入时间戳字段
    'auto_timestamp' => true,
    
    // 时间字段取出后的默认类型
    'datetime_format' => 'Y-m-d H:i:s',
    
    // 时间字段更新类型
    'update_time_format' => 'U',
    
    // SQL 监听
    'trigger_sql' => true,
    
    // 断线重连
    'break_reconnect' => true,
    
    // 关闭 SQL 日志，用于记录 SQL 信息
    'log' => false,
];
