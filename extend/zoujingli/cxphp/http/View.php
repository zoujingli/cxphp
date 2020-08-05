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

namespace cxphp\http;

use cxphp\core\Manager;

/**
 * 视图基类
 * Class View
 * @package cxphp
 */
class View extends Manager
{
    /** @var array */
    protected $data = [];

    /** @var string */
    protected $ctype = 'view';

    /** @var string */
    protected $namespace = '\\cxphp\\http\\view\\driver\\';

    /**
     * 模板变量赋值
     * @param string|array $name 模板变量
     * @param mixed $value 变量值
     * @return $this
     */
    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }

    /**
     * 解析和获取模板内容 用于输出
     * @param string $tpl 模板文件名或者内容
     * @param array $vars 模板变量
     * @param Request $request
     * @return string
     * @throws \cxphp\core\Exception
     */
    public function fetch(string $tpl = '', array $vars = [], Request $request = null): string
    {
        $coutent = $this->getDriver()->fetch($tpl, array_merge($this->data, $vars), $request);
        $this->data = [];
        return $coutent;
    }

    /**
     * 模板变量赋值
     * @access public
     * @param string $name 变量名
     * @param mixed $value 变量值
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * 取得模板显示变量的值
     * @param string $name 模板变量
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * 检测模板变量是否设置
     * @param string $name 模板变量名
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
}