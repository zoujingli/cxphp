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

use cxphp\App;

if (!function_exists('app')) {
    /**
     * 快速获取容器中的实例 支持依赖注入
     * @param string $name 类名或标识 默认获取当前应用实例
     * @param array $args 参数
     * @param bool $newInstance 是否每次创建新的实例
     * @return object|App
     * @throws \cxphp\Exception
     */
    function app(string $name = '', array $args = [], bool $newInstance = false)
    {
        return App::$object->make($name ?: App::class, $args, $newInstance);
    }
}

if (!function_exists('dump')) {
    /**
     * 浏览器友好的变量输出
     * @param mixed $vars 要输出的变量
     * @return void
     */
    function dump(...$vars)
    {
        ob_start();
        var_dump(...$vars);
        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
        if (PHP_SAPI == 'cli') {
            $output = PHP_EOL . $output . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $output = htmlspecialchars($output, ENT_SUBSTITUTE);
            }
            $output = '<pre>' . $output . '</pre>';
        }
        echo $output;
    }
}

if (!function_exists('str_snake')) {
    /**
     * 驼峰转下划线
     * @param string $value
     * @param string $separ
     * @return string
     */
    function str_snake(string $value, string $separ = '_')
    {
        $value = preg_replace('/\s+/u', '', $value);
        return mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $separ, $value), 'UTF-8');
    }
}

if (!function_exists('str_studly')) {
    /**
     * 下划线转驼峰(首字母大写)
     * @param string $value
     * @return string
     */
    function str_studly(string $value)
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }
}

