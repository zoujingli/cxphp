<?php

use cxphp\core\App;

return [
    'default' => 'file',
    'channel' => [
        'file'  => [
            'session_name' => 'PHPSID',
            'session_path' => App::$instance->getRuntimePath() . 'session',
        ],
        'redis' => [
            'host'         => '127.0.0.1',
            'port'         => 6379,
            'auth'         => '',
            'timeout'      => 2,
            'database'     => '',
            'prefix'       => 'redis_session_',
            'session_name' => 'PHPSID',
        ],
    ],
];
