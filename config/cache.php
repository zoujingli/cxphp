<?php

use cxphp\core\App;

return [
    // 默认缓存驱动
    'default' => 'file',
    // 缓存连接方式配置
    'channel' => [
        'file' => [
            // 驱动方式
            'type'      => 'File',
            // 缓存保存目录
            'path'      => App::$instance->getRuntimePath() . 'cache' . DIRECTORY_SEPARATOR,
            // 缓存名称前缀
            'prefix'    => '',
            // 缓存有效期 0 表示永久缓存
            'expire'    => 0,
            // 序列化机制
            'serialize' => [],
        ],
    ],
];