<?php

return [
    // 全局中间件定义
    'global' => [
        // 安全中间件
        app\middleware\Security::class,
        // 请求过滤中间件
        app\middleware\RequestFilter::class,
    ],

    // 分组中间件定义
    'group' => [
        'web' => [
            // Session 初始化
            think\middleware\SessionInit::class,
        ],
        
        'api' => [
            // API 认证中间件
            app\middleware\ApiAuth::class,
        ],
    ],

    // 控制器中间件别名
    'alias' => [
        'auth' => app\middleware\AuthCheck::class,
        'admin' => app\middleware\AdminAuth::class,
        'csrf' => think\middleware\VerifyCsrfToken::class,
    ],
];
