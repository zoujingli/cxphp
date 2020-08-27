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

namespace cxphp\core\view;

use cxphp\core\App;

/**
 * 视图驱动接口
 * Class Driver
 * @package cxphp\http\view
 */
abstract class Driver
{
    /** @var App */
    protected $app;

    /** @var array */
    protected $config;

    /**
     * Driver constructor.
     * @param App $app
     * @param array $config
     */
    public function __construct(App $app, array $config = [])
    {
        $this->app = $app;
        $this->config = $config;
        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }
    }

    /**
     * 渲染模板文件
     * @param string $name 模板文件
     * @param array $data 模板变量
     */
    abstract public function fetch(string $name, array $data = []);

}
