<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2024 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think;

require __DIR__ . '/vendor/autoload.php';

// 容器初始化
Container::setInstance(new Container());

// 获取基础实例
$app = App::make();

// 注册全局异常处理
$exceptionHandle = new exception\Handle($app);
$exceptionHandle->register();

// 执行 HTTP 请求
$http = $app->http;
$response = $http->run();

$response->send();

$http->end($response);
