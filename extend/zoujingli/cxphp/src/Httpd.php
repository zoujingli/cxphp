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

use cxphp\httpd\Request;
use cxphp\httpd\Response;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http;
use Workerman\Timer;
use Workerman\Worker;

/**
 * Class Httpd
 * @package cxphp
 */
class Httpd
{
    /** @var App */
    protected $app;

    /** @var array */
    protected $config;

    /** @var Worker */
    protected $worker;

    /**  @var string */
    protected $publicPath = '';

    /** @var int */
    protected $maxRequestCount = 1000000;

    /**  @var int */
    protected $gracefulStopTimer = null;

    /**
     * Worker constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->config = $app->config->get('worker', []);
        $this->publicPath = $this->app->getPublicPath();
        $this->init();
    }

    /**
     * 初始化 Worker 环境
     * @return $this
     */
    public function init()
    {
        Worker::$onMasterReload = function () {
            if ($status = \opcache_get_status()) {
                foreach (array_keys($status['scripts']) as $file) {
                    \opcache_invalidate($file, true);
                }
            }
        };
        Worker::$pidFile = $this->config['pid_file'] ?? $this->app->getRuntimePath('httpd.pid');
        Worker::$stdoutFile = $this->config['stdout_file'] ?? $this->app->getRuntimePath('stdout.txt');
        TcpConnection::$defaultMaxPackageSize = $this->config['max_package_size'] ?? 10 * 1024 * 1024;
        return $this;
    }

    /**
     * 启动 Worker 进程
     */
    public function start()
    {
        $this->worker = new Worker($this->config['listen'] ?? 'http://0.0.0.0:8080', $this->config['context'] ?? []);
        $this->worker->name = 'WebService';
        $this->worker->count = 1;
        foreach (['name', 'count', 'user', 'group', 'reusePort', 'transport'] as $property) {
            if (isset($this->config[$property])) $this->worker->$property = $this->config[$property];
        }
        $this->worker->onWorkerStart = function ($worker) {
            $this->app->config->reload();
            Http::requestClass(Request::class);
            $worker->onMessage = function (TcpConnection $connection, Request $request) {
                $this->app->setInstance('request', $request);
                $this->app->setInstance('response', Response::make());
                static $requestCount = 0;
                if (++$requestCount > $this->maxRequestCount) {
                    $this->tryToGracefulExit();
                }
                try {
                    $filename = $this->app->getPublicPath(trim($request->path(), '\\/'));
                    if (file_exists($filename) && is_file($filename)) {
                        return $this->app->response->file($filename)->send();
                    } else {
                        $callable = $this->app->route->parseRoute(trim($request->path(), '\\/'), $request);
                        return $this->execCallback($callable, $request);
                    }
                } catch (\Throwable|\Exception $exception) {
                    echo PHP_EOL . ">>> Exception <<< " . date('Y-m-d H:i:s') . PHP_EOL;
                    echo "Info: {$exception->getMessage()}" . PHP_EOL;
                    echo "File: {$exception->getFile()}:{$exception->getLine()}" . PHP_EOL . PHP_EOL;
                    $this->app->response->exceptionPage($exception)->send();
                }
            };
        };
        Worker::runAll();
    }

    /**
     * 执行路由访问
     * @param array $callback
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function execCallback(array $callback, Request $request)
    {
        [$callable, $module, $controller, $action, $args] = $callback;
        $request->realpath = str_replace('._', '.', str_snake($controller));
        $request->realnode = "{$module}/{$request->realpath}/{$action}";
        if (is_callable($callable)) {
            return $this->app->invokeFunction($callable, $args);
        } elseif (strpos($request->realpath, '.')) {
            $attrs = explode('.', $request->realpath);
            [$class, $prefix] = [array_pop($attrs), join('\\', $attrs)];
            $controller = strtr($prefix . '.' . str_studly($class), '.', '\\');
        } else {
            $controller = ucfirst($controller);
        }
        \ob_start();
        $class = "app\\{$module}\\controller\\{$controller}";
        $result = $this->app->invokeMethod([$this->app->make($class), $action], $args);
        if ($result instanceof Response) {
            return $this->app->response->send();
        } elseif (is_string($result) || is_numeric($result)) {
            return $this->app->response->withBody($result)->send();
        } elseif (is_array($result) || is_bool($result)) {
            return $this->app->response->withBody(var_export($result, true))->send();
        }
        $content = \ob_get_clean() ?: '';
        return $this->app->response->withBody($content)->send();
    }

    /**
     * @return Worker
     */
    public function worker()
    {
        return $this->worker;
    }

    private function tryToGracefulExit()
    {
        if ($this->gracefulStopTimer === null) {
            $this->gracefulStopTimer = Timer::add(rand(1, 10), function () {
                if (\count($this->worker->connections) === 0) {
                    Worker::stopAll();
                }
            });
        }
    }
}