<?php

return [
    // 默认路由规则
    '__pattern__' => [
        'id' => '\d+',
        'page' => '\d+',
    ],

    // 首页路由
    '/' => 'index/Index/index',

    // 用户相关路由
    '/user/register' => 'user/Register/index',
    '/user/login' => 'user/Login/index',
    '/user/logout' => 'user/Login/logout',
    '/user/profile' => 'user/Profile/index',

    // 音乐相关路由
    '/music/list' => 'music/List/index',
    '/music/detail/<id>' => 'music/Detail/index',
    '/music/search' => 'music/Search/index',

    // 专辑相关路由
    '/album/list' => 'album/List/index',
    '/album/detail/<id>' => 'album/Detail/index',

    // 歌手相关路由
    '/artist/list' => 'artist/List/index',
    '/artist/detail/<id>' => 'artist/Detail/index',

    // API 路由组
    '[api]' => [
        '/v1/music' => 'api/v1.Music/index',
        '/v1/user' => 'api/v1.User/index',
    ],
];
