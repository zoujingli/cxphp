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

namespace cxphp\http\view\driver;

use cxphp\core\Exception;
use cxphp\http\view\Driver;
use think\Template;

/**
 * Class Think
 * @package cxphp\http\view\driver
 */
class Think extends Driver
{
    /**
     * @var Template
     */
    protected $view;

    protected function initialize()
    {
        $this->config['view_path'] = $this->config['view_path'] ?? $this->app->getAppPath();
        $this->config['view_suffix'] = $this->config['view_suffix'] ?? 'html';
        $this->config['cache_path'] = $this->config['cache_path'] ?? $this->app->getRuntimePath('temp') . DIRECTORY_SEPARATOR;
        $this->config['cache_suffix'] = $this->config['cache_suffix'] ?? 'php';
        $this->view = $this->view ?: new Template($this->config);
    }

    /**
     * 解析模板文件
     * @param string $name 模板名称
     * @return string
     * @throws Exception
     */
    protected function parseTemplate($name)
    {
        if (stripos($name, '.' . $this->config['view_suffix']) == false) {
            $name .= '.' . $this->config['view_suffix'];
        }
        if (file_exists($name) && is_file($name)) {
            return $name;
        }
        if (stripos($name, '@') !== false) {
            [$module, $file] = explode('@', $name);
            $temp = "{$module}/view/{$file}";
        } elseif ($name === '.' . $this->config['view_suffix']) {
            $temp = $this->app->request->module . '/view/' . strtr($this->app->request->realpath, '.', '/') . '/' . strtolower($this->app->request->action) . $name;
        } elseif (substr_count($name, '/') === 0) {
            $temp = $this->app->request->module . '/view/' . strtr($this->app->request->realpath, '.', '/') . '/' . $name;
        } elseif (substr_count($name, '/') === 1) {
            $temp = $this->app->request->module . '/view/' . $name;
        } else {
            $temp = $name;
        }
        $realname = $this->app->getAppPath($temp);
        if (file_exists($realname) && is_file($realname)) {
            return $realname;
        } else {
            throw new Exception("Template {$temp} not found.");
        }
    }

    /**
     * 模板文件渲染
     * @param string $name 模板文件
     * @param array $data 模板变量
     * @return string
     * @throws Exception
     */
    public function fetch(string $name, array $data = []): string
    {
        \ob_start();
        $this->view->fetch($this->parseTemplate($name), $data);
        return \ob_get_clean();
    }

}