<?php

return [
    // 应用名称
    'app_name' => 'JYMusic',
    
    // 应用命名空间
    'app_namespace' => 'app',
    
    // 应用调试模式
    'app_debug' => true,
    
    // 应用 Trace
    'app_trace' => false,
    
    // 应用日志开关
    'log' => [
        'enable' => true,
        'type' => 'File',
        'level' => ['error', 'sql'],
    ],
    
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',
    
    // 默认语言
    'default_lang' => 'zh-cn',
    
    // 允许的语言列表
    'allow_lang_list' => ['zh-cn', 'en-us'],
    
    // 全局请求过滤
    'filter' => ['htmlspecialchars', 'stripslashes'],
    
    // 默认 JSONP 返回变量
    'var_jsonp_handler' => 'callback',
    
    // JSONP 输出返回类型
    'jsonp_return_type' => 'json',
    
    // 默认跳转页等待时间
    'dispatch_success_wait' => 3,
    'dispatch_error_wait' => 3,
    
    // 模板引擎配置
    'view' => [
        'type' => '\\think\\view\\driver\\Php',
        'view_path' => '../resources/',
        'view_suffix' => 'html',
        'view_depr' => DIRECTORY_SEPARATOR,
    ],
    
    // 安全配置
    'security' => [
        // XSS 防护
        'xss_filter' => true,
        // SQL 注入防护
        'sql_injection_check' => true,
        // CSRF 令牌验证
        'csrf_token_check' => false,
        // 文件上传安全检查
        'upload_check' => true,
    ],
];
