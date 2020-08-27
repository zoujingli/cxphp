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

use cxphp\core\httpd\Request;

/**
 * 基础路由管理器
 * Class Route
 * @package cxphp\http
 */
class Route
{
    /** @var App */
    protected $app;

    /** @var array */
    protected $rule;

    /**
     * Route constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 添加 GET 路由规则
     * @param string $path
     * @param callable $callback
     */
    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * 添加 Post 路由规则
     * @param string $path
     * @param callable $callback
     */
    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * 添加 Put 路由规则
     * @param string $path
     * @param callable $callback
     */
    public function put($path, $callback)
    {
        $this->addRoute('PUT', $path, $callback);
    }

    /**
     * 添加 Patch 路由规则
     * @param string $path
     * @param callable $callback
     */
    public function patch($path, $callback)
    {
        $this->addRoute('PATCH', $path, $callback);
    }

    /**
     * 添加 Delete 路由规则
     * @param string $path
     * @param callable $callback
     */
    public function delete($path, $callback)
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    /**
     * 添加 Head 路由规则
     * @param string $path
     * @param callable $callback
     */
    public function head($path, $callback)
    {
        $this->addRoute('HEAD', $path, $callback);
    }

    /**
     * 添加任意路由规则
     * @param string $path
     * @param string $callback
     */
    public function any($path, $callback)
    {
        $this->addRoute(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD'], $path, $callback);
    }

    /**
     * 添加路由规则
     * @param string|array $method
     * @param string $pathinfo
     * @param callable $callback
     */
    protected function addRoute($method, $pathinfo, $callback)
    {
        if (is_array($method)) foreach ($method as $m) {
            $this->rule[strtoupper($m)][$pathinfo] = $callback;
        } elseif (is_string($method)) {
            $this->rule[strtoupper($method)][$pathinfo] = $callback;
        }
    }

    /**
     * 解析路由路径
     * @param string $pathinfo
     * @param Request $request
     * @return array
     */
    public function parseRoute(string $pathinfo, Request $request)
    {
        $defaultModule = $this->app->config->get('app.default_module', 'index');
        $defaultAction = $this->app->config->get('app.default_action', 'index');
        $defaultController = $this->app->config->get('app.default_controller', 'Index');
        foreach ($this->rule[strtoupper($request->method())] ?? [] as $rule => $callable) {
            if (preg_match("|{$rule}|", $pathinfo, $matches)) if (is_callable($callable)) {
                $request->action = $defaultAction;
                $request->module = $defaultModule;
                $request->controller = $defaultController;
                return [$callable, $defaultModule, $defaultController, $defaultAction, []];
            } elseif (is_string($callable)) {
                $pathinfo = preg_replace("|{$rule}|", $callable, $pathinfo);
                break;
            }
        }
        $args = $pathinfo ? explode('/', $pathinfo) : [];
        $request->module = array_shift($args) ?: $defaultModule;
        $request->controller = count($args) > 0 ? array_shift($args) : $defaultController;
        $request->action = count($args) > 0 ? array_shift($args) : $defaultAction;
        return ['', $request->module, $request->controller, $request->action, $args ?: []];
    }

    /**
     * 加载路由配置文件
     * @param string $routeConfigFile
     * @return Route
     */
    public function load(string $routeConfigFile)
    {
        if (file_exists($routeConfigFile)) {
            $route = require_once $routeConfigFile;
            if (is_array($route)) foreach ($route as $path => $callback) {
                $this->any($path, $callback);
            }
        }
        return $this;
    }
}