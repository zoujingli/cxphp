<?php

// +----------------------------------------------------------------------
// | CxPHP 极速常驻内存框架 ~ 基于 WorkerMan 实现，极速及兼容并存
// +----------------------------------------------------------------------
// | 版权所有 2014~2020 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://www.cxphp.cn
// | 项目文档: http://doc.cxphp.cn
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/cxphp
// | github 代码仓库：https://github.com/zoujingli/cxphp
// +----------------------------------------------------------------------

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
