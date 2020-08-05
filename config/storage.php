<?php

use cxphp\core\App;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;

return [
    // 默认磁盘
    'default' => 'local',
    // 磁盘列表
    'channel' => [
        'local' => [
            'adapter' => function (App $app): AdapterInterface {
                return new Local($app->getPublicPath('storage'), LOCK_EX, Local::SKIP_LINKS);
            },
        ],
    ],
];