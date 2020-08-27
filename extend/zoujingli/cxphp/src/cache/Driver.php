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

namespace cxphp\cache;

use cxphp\App;
use cxphp\Exception;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 缓存驱动接口
 * Class Driver
 * @package cxphp\cache
 */
abstract class Driver implements CacheInterface
{
    /**
     * @var App
     */
    protected $app;

    /**
     * 驱动句柄
     * @var object
     */
    protected $handler = null;

    /**
     * 缓存参数
     * @var array
     */
    protected $options = [];

    /**
     * Driver constructor.
     * @param App $app
     * @param array $options
     */
    public function __construct(App $app, array $options = [])
    {
        $this->app = $app;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * 获取有效期
     * @param integer|\DateTimeInterface|\DateInterval $expire 有效期
     * @return int
     */
    protected function getExpireTime($expire): int
    {
        if ($expire instanceof \DateTimeInterface) {
            $expire = $expire->getTimestamp() - time();
        } elseif ($expire instanceof \DateInterval) {
            $expire = \DateTime::createFromFormat('U', (string)time())->add($expire)->format('U') - time();
        }
        return (int)$expire;
    }

    /**
     * 获取实际的缓存标识
     * @access public
     * @param string $name 缓存名
     * @return string
     */
    public function getCacheKey(string $name): string
    {
        return $this->options['prefix'] . $name;
    }

    /**
     * 追加（数组）缓存
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @return bool
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function push(string $name, $value): bool
    {
        $item = $this->get($name, []);
        if (!is_array($item)) {
            throw new Exception('only array cache can be push');
        }
        $item[] = $value;
        if (count($item) > 1000) array_shift($item);
        return $this->set($name, array_unique($item));
    }

    /**
     * 序列化数据
     * @param mixed $data 缓存数据
     * @return string
     */
    protected function serialize($data): string
    {
        return serialize($data);
    }

    /**
     * 反序列化数据
     * @param string $data 缓存数据
     * @return mixed
     */
    protected function unserialize(string $data)
    {
        return unserialize($data);
    }

    /**
     * 返回句柄对象，可执行其它高级方法
     * @return object
     */
    public function handler()
    {
        return $this->handler;
    }

    /**
     * 通过魔术方法调用
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->handler, $method], $args);
    }

    /**
     * 读取缓存
     * @access public
     * @param iterable $keys 缓存变量名
     * @param mixed $default 默认值
     * @return iterable
     * @throws InvalidArgumentException
     */
    public function getMultiple($keys, $default = null): iterable
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * 写入缓存
     * @access public
     * @param iterable $values 缓存数据
     * @param null|int|\DateInterval $ttl 有效时间 0为永久
     * @return bool
     * @throws InvalidArgumentException
     */
    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $val) {
            $result = $this->set($key, $val, $ttl);
            if (false === $result) {
                return false;
            }
        }
        return true;
    }

    /**
     * 删除缓存
     * @access public
     * @param iterable $keys 缓存变量名
     * @return bool
     * @throws InvalidArgumentException
     */
    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            $result = $this->delete($key);
            if (false === $result) {
                return false;
            }
        }
        return true;
    }

    abstract public function has($name): bool;

    abstract public function clear(): bool;

    abstract public function delete($name): bool;

    abstract public function get($name, $default = null);

    abstract public function set($name, $value, $expire = null): bool;

}