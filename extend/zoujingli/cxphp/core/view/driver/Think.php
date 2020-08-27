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

namespace cxphp\core\view\driver;

use cxphp\core\view\Driver;
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
        $this->config['cache_path'] = $this->config['cache_path'] ?? $this->app->getRuntimePath() . 'temp' . DIRECTORY_SEPARATOR;
        $this->config['cache_suffix'] = $this->config['cache_suffix'] ?? 'php';
        $this->view = $this->view ?: new Template($this->config);
    }

    /**
     * 自动定位模板文件
     * @access private
     * @param string $template 模板文件规则
     * @return string
     */
    private function parseTemplate(string $template): string
    {
        if (strpos($template, '@') !== false) {
            [$app, $template] = explode('@', $template);
        } else {
            $app = $this->app->request->module;
        }
        $depr = $this->config['view_depr'] ?? DIRECTORY_SEPARATOR;
        $viewpath = $this->config['view_path'] . $app . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
        if (0 !== strpos($template, '/')) {
            $realpath = $this->app->request->realpath;
            $template = str_replace(['/', ':'], $depr, $template);
            if ($template === '') {
                $template = str_replace('.', DIRECTORY_SEPARATOR, $realpath) . $depr . str_snake($this->app->request->action);
            } elseif (strpos($template, $depr) === false) {
                $template = str_replace('.', DIRECTORY_SEPARATOR, $realpath) . $depr . $template;
            }
        } else {
            $template = str_replace(['/', ':'], $depr, trim($template, '/\\'));
        }
        $extension = '.' . trim($this->config['view_suffix'], '.');
        if (substr($template, -strlen($extension)) !== $extension) {
            return $viewpath . $template . $extension;
        } else {
            return $viewpath . $template;
        }
    }


    /**
     * 模板文件渲染
     * @param string $name 模板文件
     * @param array $data 模板变量
     * @return string
     */
    public function fetch(string $name, array $data = []): string
    {
        \ob_start();
        $this->view->fetch($this->parseTemplate($name), $data);
        return \ob_get_clean();
    }

}