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
 * Class Manager
 * @package cxphp\core
 */
abstract class Manager
{
    /** @var App */
    protected $app;

    /** @var string */
    protected $ctype;

    /** @var array */
    protected $drivers = [];

    /** @var string */
    protected $namespace = null;

    /**
     * Manager constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 获取驱动实例对象
     * @param null|string $name
     * @return static
     * @throws Exception
     */
    public function getDriver(string $name = null)
    {
        $name = $name ?: $this->getDefaultDriver();
        return $this->drivers[$name] = $this->drivers[$name] ?? $this->createDriver($name);
    }

    /**
     * 移除一个驱动实例对象
     * @param string|null $name
     * @return $this
     */
    public function forgetDriver(string $name = null)
    {
        $name = $name ?? $this->getDefaultDriver();
        if (isset($this->drivers[$name])) {
            unset($this->drivers[$name]);
        }
        return $this;
    }

    /**
     * 创建驱动
     * @param string $channel
     * @return mixed
     * @throws Exception
     */
    protected function createDriver(string $channel)
    {
        if ($this->namespace || false !== strpos($channel, '\\')) {
            $class = false !== strpos($channel, '\\') ? $channel : $this->namespace . str_studly($channel);
            if (class_exists($class)) {
                return $this->app->invokeClass($class, [$this->getChannelConfig($channel)]);
            }
        }
        throw new Exception("Driver [{$this->ctype}\\driver\\{$channel}] not supported.");
    }

    /**
     * 获取缓存配置
     * @param null|string $name 名称
     * @param null|mixed $default 默认值
     * @return mixed
     */
    protected function getConfig(string $name = null, $default = null)
    {
        $subtype = is_null($name) ? '' : '.' . $name;
        return $this->app->config->get($this->ctype . $subtype, $default);
    }

    /**
     * 默认驱动
     * @return string
     */
    protected function getDefaultDriver()
    {
        return $this->getConfig('default', 'default');
    }

    /**
     * 获取磁盘配置
     * @param string $channel 通道名称
     * @param null|mixed $name 配置名称
     * @param null|mixed $default
     * @return array
     * @throws Exception
     */
    protected function getChannelConfig($channel = null, $name = null, $default = null)
    {
        if (is_null($channel)) $channel = $this->getDefaultDriver();
        if ($config = $this->getConfig('channel.' . $channel)) {
            return is_null($name) ? $config : ($config[$name] ?? $default);
        } else {
            throw new Exception("Channel [{$this->ctype}\\driver\\{$channel}] not found.");
        }
    }

    /**
     * 动态调用驱动方法
     * @param string $method 调用方法
     * @param array $parameters 调用参数
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $parameters)
    {
        return $this->getDriver()->$method(...$parameters);
    }

}