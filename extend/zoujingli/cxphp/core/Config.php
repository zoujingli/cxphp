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

namespace cxphp\core;

/**
 * 核心配置加载类型
 * Class Config
 * @package cxphp
 */
class Config
{
    /**
     * 当前应用
     * @var App
     */
    protected $app;

    /**
     * 当前配置数据
     * @var array
     */
    protected $data = [];

    /**
     * Config constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        foreach (\glob($this->app->getConfigPath("*.php")) as $file) {
            $this->load($file);
        }
    }

    /**
     * 加载指定配置文件
     * @param string $file 文件路径
     * @return $this
     */
    public function load($file)
    {
        $basename = \strtolower(\basename($file, '.php'));
        $this->data[$basename] = include $file;
        return $this;
    }

    /**
     * 读取指定配置信息
     * @param null|string $name
     * @param null|string $default
     * @return array|mixed|null
     */
    public function get($name = null, $default = null)
    {
        if ($name === null) {
            return $this->data;
        }
        $temp = $this->data;
        $array = \explode('.', $name);
        foreach ($array as $index) {
            if (!isset($temp[$index])) {
                return $default;
            }
            $temp = $temp[$index];
        }
        return $temp;
    }

    /**
     * 重新加载配置
     */
    public function reload()
    {
        $this->data = [];
        $this->__construct($this->app);
    }
}