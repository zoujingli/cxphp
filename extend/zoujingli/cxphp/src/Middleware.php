<?php

declare (strict_types=1);

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

namespace cxphp;

class Middleware
{
    /**
     * @var array
     */
    protected static $_instances = [];

    /**
     * @param $allMiddlewares
     */
    public static function load($allMiddlewares)
    {
        foreach ($allMiddlewares as $app_name => $middlewares) {
            foreach ($middlewares as $class_name) {
                if (\method_exists($class_name, 'process')) {
                    static::$_instances[$app_name][] = [\singleton($class_name), 'process'];
                } else {
                    echo "middleware {$class_name}::process not exsits\n";
                }
            }
        }
    }

    public static function getMiddleware($appName, $withGlobalMiddleware = true)
    {
        $globalMiddleware = $withGlobalMiddleware && isset(static::$_instances['']) ? static::$_instances[''] : [];
        if ($appName === '') {
            return \array_reverse($globalMiddleware);
        }
        $appMiddleware = static::$_instances[$appName] ?? [];
        return \array_reverse($globalMiddleware + $appMiddleware);
    }

    /**
     * @param $appname
     * @return bool
     */
    public static function hasMiddleware($appname)
    {
        return isset(static::$_instances[$appname]);
    }
}