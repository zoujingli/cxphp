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

use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;

/**
 * 文件存储引擎管理
 * Class Storage
 * @package cxphp
 * @mixin Filesystem
 */
class Storage extends Manager
{

    /** @var string */
    protected $ctype = 'storage';

    /**
     * 创建驱动
     * @param string $channel
     * @return Filesystem
     * @throws Exception
     */
    protected function createDriver(string $channel): Filesystem
    {
        $config = $this->getChannelConfig($channel);
        if (isset($config['adapter']) && is_callable($config['adapter'])) {
            return new Filesystem($this->createAdapter($config), $config);
        } else {
            throw new Exception("Storage Driver {$channel} not supported.");
        }
    }

    /**
     * 创建文件存储适配器
     * @param array $config
     * @return AdapterInterface
     * @throws Exception
     */
    protected function createAdapter(array $config): AdapterInterface
    {
        if ($config['adapter'] instanceof AdapterInterface) {
            return $config['adapter'];
        } elseif (is_callable($config['adapter'])) {
            return $this->app->invokeFunction($config['adapter']);
        } else {
            throw new Exception("Storage Adapter Not Found.");
        }
    }
}