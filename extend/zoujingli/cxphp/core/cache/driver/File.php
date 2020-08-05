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

namespace cxphp\core\cache\driver;

use cxphp\core\App;
use cxphp\core\cache\Driver;
use Psr\SimpleCache\CacheInterface;

/**
 * 文件缓存类
 */
class File extends Driver
{
    /**
     * 配置参数
     * @var array
     */
    protected $options = [
        'expire'        => 0,
        'cache_subdir'  => true,
        'prefix'        => '',
        'path'          => '',
        'hash_type'     => 'md5',
        'data_compress' => false,
        'serialize'     => [],
    ];

    /**
     * 架构函数
     * @param App $app
     * @param array $options 参数
     */
    public function __construct(App $app, array $options = [])
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        if (empty($this->options['path'])) {
            $this->options['path'] = $app->getRuntimePath('cache');
        }
        $this->options['path'] = trim($this->options['path'], '\\/');
        $this->options['path'] .= DIRECTORY_SEPARATOR;
        parent::__construct($app, $options);
    }

    /**
     * 取得变量的存储文件名
     * @param string $name 缓存变量名
     * @return string
     */
    public function getCacheKey(string $name): string
    {
        $name = hash($this->options['hash_type'], $name);
        if ($this->options['cache_subdir']) {
            $name = substr($name, 0, 2) . DIRECTORY_SEPARATOR . substr($name, 2);
        }
        if ($this->options['prefix']) {
            $name = $this->options['prefix'] . DIRECTORY_SEPARATOR . $name;
        }
        return $this->options['path'] . $name . '.php';
    }

    /**
     * 判断缓存是否存在
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name): bool
    {
        return $this->getRaw($name) !== null;
    }

    /**
     * 读取缓存
     * @param string $name 缓存变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $raw = $this->getRaw($name);
        return is_null($raw) ? $default : $this->unserialize($raw['content']);
    }

    /**
     * 写入缓存
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param int|\DateTime $expire 有效时间 0 为永久
     * @return bool
     */
    public function set($name, $value, $expire = null): bool
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $expire = $this->getExpireTime($expire);
        $filename = $this->getCacheKey($name);
        $pathname = dirname($filename);
        if (!is_dir($pathname)) @mkdir($pathname, 0755, true);
        $data = "<?php\n//" . sprintf('%012d', $expire) . "\n exit();?>\n{$this->serialize($value)}";
        $result = file_put_contents($filename, $data);
        if ($result) {
            clearstatcache();
            return true;
        }
        return false;
    }

    /**
     * 删除缓存
     * @param string $name 缓存变量名
     * @return bool
     */
    public function delete($name): bool
    {
        return $this->unlink($this->getCacheKey($name));
    }

    /**
     * 清除缓存
     * @return bool
     */
    public function clear(): bool
    {
        $dirname = $this->options['path'] . $this->options['prefix'];
        $this->rmdir($dirname);
        return true;
    }

    /**
     * 获取缓存数据
     * @param string $name 缓存标识名
     * @return array|null|mixed
     */
    protected function getRaw(string $name)
    {
        $filename = $this->getCacheKey($name);
        if (!is_file($filename)) return;
        $content = @file_get_contents($filename);
        if (false !== $content) {
            $expire = (int)substr($content, 8, 12);
            if (0 != $expire && time() - $expire > filemtime($filename)) {
                //缓存过期删除缓存文件
                $this->unlink($filename);
                return;
            }
            return ['content' => substr($content, 32), 'expire' => $expire];
        }
    }

    /**
     * 判断文件是否存在后，删除
     * @param string $path
     * @return bool
     */
    private function unlink(string $path): bool
    {
        try {
            return is_file($path) && unlink($path);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * 删除文件夹
     * @param $dirname
     * @return bool
     */
    private function rmdir($dirname)
    {
        if (!is_dir($dirname)) return false;
        foreach (new \FilesystemIterator($dirname) as $item) {
            if ($item->isDir() && !$item->isLink()) {
                $this->rmdir($item->getPathname());
            } else {
                $this->unlink($item->getPathname());
            }
        }
        @rmdir($dirname);
        return true;
    }
}
