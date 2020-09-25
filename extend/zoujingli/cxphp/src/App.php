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

/**
 * App 基础类
 * @property Db $db
 * @property Lang $lang
 * @property View $view
 * @property Cache $cache
 * @property Event $event
 * @property Httpd $httpd
 * @property Route $route
 * @property Config $config
 * @property Logger $logger
 * @property Request $request
 * @property Storage $storage
 * @property Response $response
 * @property Middleware $middleware
 */
class App
{
    const VERSION = '1.0.0';

    /**
     * 容器绑定标识
     * @var array
     */
    protected $bind = [
        'db'         => Db::class,
        'app'        => App::class,
        'lang'       => Lang::class,
        'view'       => View::class,
        'cache'      => Cache::class,
        'event'      => Event::class,
        'httpd'      => Httpd::class,
        'route'      => Route::class,
        'config'     => Config::class,
        'logger'     => Logger::class,
        'request'    => Request::class,
        'storage'    => Storage::class,
        'response'   => Response::class,
        'middleware' => Middleware::class,
    ];

    /**
     * 框架核心目录
     * @var string
     */
    protected $corePath;

    /**
     * 项目根目录
     * @var string
     */
    protected $rootPath;

    /**
     * 容器中的对象实例
     * @var array
     */
    protected $instances = [];

    /**
     * 当前应用实例对象
     * @var static
     */
    public static $object;

    /**
     * App constructor.
     * @param string $rootPath
     */
    public function __construct(string $rootPath = '')
    {
        $this->corePath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
        if (empty($rootPath)) {
            $this->rootPath = dirname($this->corePath, 3) . DIRECTORY_SEPARATOR;
        } else {
            $this->rootPath = rtrim($rootPath, '\\/') . DIRECTORY_SEPARATOR;
        }
        $this->setInstance('app', $this);
    }

    /**
     * 获取应用属性对象
     * @param string $name 属性名称
     * @return mixed
     * @throws Exception
     */
    public function __get(string $name)
    {
        return $this->make($name);
    }

    /**
     * 创建获取当前应用
     * @param bool $listen
     * @return static
     */
    public static function run(bool $listen = false): App
    {
        if (is_null(self::$object)) {
            static::$object = new static;
            // 设置系统默认时区
            if ($timezone = static::$object->config->get('app.default_timezone')) {
                date_default_timezone_set($timezone);
            }
            // 初始化 Worker 服务
            if ($listen) {
                static::$object->httpd->start();
            }
        }
        return static::$object;
    }

    /**
     * 绑定一个类、闭包、实例、接口实现到容器
     * @param string|array $abstract 类标识、接口
     * @param mixed $concrete 要绑定的类、闭包或者实例
     * @return $this
     */
    public function bind($abstract, $concrete = null)
    {
        if (is_array($abstract)) {
            foreach ($abstract as $key => $val) {
                $this->bind($key, $val);
            }
        } elseif ($concrete instanceof \Closure) {
            $this->bind[$abstract] = $concrete;
        } elseif (is_object($concrete)) {
            $this->setInstance($abstract, $concrete);
        } else {
            $abstract = $this->getAlias($abstract);
            $this->bind[$abstract] = $concrete;
        }
        return $this;
    }

    /**
     * 创建类的实例 已经存在则直接获取
     * @param string $abstract 类名或者标识
     * @param array $vars 实例参数数据
     * @param boolean $newInstance 是否创建新的实例
     * @return mixed
     * @throws Exception
     */
    public function make(string $abstract, array $vars = [], bool $newInstance = false)
    {
        $abstract = $this->getAlias($abstract);
        if (isset($this->instances[$abstract]) && !$newInstance) {
            return $this->instances[$abstract];
        }
        if (isset($this->bind[$abstract]) && $this->bind[$abstract] instanceof \Closure) {
            $object = $this->invokeFunction($this->bind[$abstract], $vars);
        } else {
            $object = $this->invokeClass($abstract, $vars);
        }
        if (!$newInstance) {
            $this->instances[$abstract] = $object;
        }
        return $object;
    }

    /**
     * 绑定一个类实例
     * @param string $abstract 类名或者标识
     * @param object $instance 类的实例
     * @return $this
     */
    public function setInstance(string $abstract, $instance): App
    {
        $abstract = $this->getAlias($abstract);
        $this->instances[$abstract] = $instance;
        return $this;
    }

    /**
     * 移除一个类实例
     * @param string $abstract 类名或者标识
     * @return $this
     */
    public function delInstance(string $abstract): App
    {
        $name = $this->getAlias($abstract);
        if (isset($this->instances[$name])) {
            unset($this->instances[$name]);
        }
        return $this;
    }

    /**
     * 调用反射执行类的实例化 支持依赖注入
     * @param string $class
     * @param array $vars
     * @param array $statics
     * @return object
     * @throws Exception
     */
    public function invokeClass(string $class, array $vars = [], array $statics = [])
    {
        try {
            $reflect = new \ReflectionClass($class);
        } catch (\ReflectionException $exception) {
            throw new Exception("class not exists: {$class}", $exception->getCode(), $exception);
        }
        try {
            if ($statics) foreach ($statics as $name => $value) {
                $reflect->setStaticPropertyValue($name, $value);
            }
            $constructor = $reflect->getConstructor();
            return $reflect->newInstanceArgs($constructor ? $this->__bindParams($constructor, $vars) : []);
        } catch (\ReflectionException $exception) {
            throw new Exception("class invoke faild: {$exception->getMessage()}", $class, $exception);
        }
    }

    /**
     * 调用反射执行类的方法 支持参数绑定
     * @param mixed $method 方法
     * @param array $vars 参数
     * @param bool $accessible 设置是否可访问
     * @return mixed
     * @throws Exception
     */
    public function invokeMethod($method, array $vars = [], bool $accessible = false)
    {
        if (is_array($method)) {
            [$class, $method] = $method;
            $class = is_object($class) ? $class : $this->invokeClass($class);
        } else {
            [$class, $method] = explode('::', $method);
        }
        try {
            $reflect = new \ReflectionMethod($class, $method);
        } catch (\ReflectionException $exception) {
            $class = is_object($class) ? get_class($class) : $class;
            throw new Exception("method not exists: {$class}::{$method}()", $exception->getCode(), $exception);
        }
        try {
            if ($accessible) $reflect->setAccessible($accessible);
            return $reflect->invokeArgs(is_object($class) ? $class : null, $this->__bindParams($reflect, $vars));
        } catch (\ReflectionException $exception) {
            throw new Exception("method exec faild: {$exception->getMessage()}", $exception->getCode(), $exception);
        }
    }

    /**
     * 执行函数或者闭包方法 支持参数调用
     * @param string|\Closure $function 函数或者闭包
     * @param array $vars 参数
     * @return mixed
     * @throws Exception
     */
    public function invokeFunction($function, array $vars = [])
    {
        try {
            $reflect = new \ReflectionFunction($function);
        } catch (\ReflectionException $exception) {
            throw new Exception("function not exists: {$function}()", $function, $exception);
        }
        try {
            return $function(...$this->__bindParams($reflect, $vars));
        } catch (\ReflectionException $exception) {
            throw new Exception('function exec faild', $function, $exception);
        }
    }

    /**
     * 绑定参数
     * @param \ReflectionFunctionAbstract $reflect 反射类
     * @param array $vars 参数
     * @return array
     * @throws Exception
     * @throws \ReflectionException
     */
    private function __bindParams(\ReflectionFunctionAbstract $reflect, array $vars = []): array
    {
        if ($reflect->getNumberOfParameters() == 0) {
            return [];
        }
        reset($vars);
        [$args, $type] = [[], key($vars) === 0 ? 1 : 0];
        foreach ($reflect->getParameters() as $parameter) {
            [$name, $class] = [$parameter->getName(), $parameter->getClass()];
            if ($class) {
                $args[] = (function (string $classname, array &$vars) {
                    $array = $vars;
                    $value = array_shift($array);
                    if ($value instanceof $classname) {
                        array_shift($vars);
                        return $value;
                    } else {
                        return $this->make($classname);
                    }
                })($class->getName(), $vars);
            } elseif (1 == $type && !empty($vars)) {
                $args[] = array_shift($vars);
            } elseif (0 == $type && isset($vars[$name])) {
                $args[] = $vars[$name];
            } elseif (0 == $type && isset($vars[str_snake($name)])) {
                $args[] = $vars[str_snake($name)];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
            } else {
                throw new Exception('method param miss:' . $name);
            }
        }
        return $args;
    }

    /**
     * 根据别名获取真实类名
     * @param string $abstract
     * @return string
     */
    public function getAlias(string $abstract): string
    {
        if (isset($this->bind[$abstract]) && is_string($this->bind[$abstract])) {
            return $this->getAlias($this->bind[$abstract]);
        }
        return $abstract;
    }

    /**
     * 获取应用根目录
     * @param string $suffix
     * @return string
     */
    public function getAppPath(string $suffix = ''): string
    {
        return $this->rootPath . 'app' . DIRECTORY_SEPARATOR . $suffix;
    }

    /**
     * 获取内核目录
     * @param string $suffix
     * @return string
     */
    public function getCorePath(string $suffix = ''): string
    {
        return $this->corePath . $suffix;
    }

    /**
     * 获取项目根目录
     * @param string $suffix
     * @return string
     */
    public function getRootPath(string $suffix = ''): string
    {
        return $this->rootPath . $suffix;
    }

    /**
     * 获取路由目录
     * @param string $suffix
     * @return string
     */
    public function getRoutePath(string $suffix = ''): string
    {
        return $this->rootPath . 'route' . DIRECTORY_SEPARATOR . $suffix;
    }

    /**
     * 获取应用配置目录
     * @param string $suffix
     * @return string
     */
    public function getConfigPath(string $suffix = ''): string
    {
        return $this->rootPath . 'config' . DIRECTORY_SEPARATOR . $suffix;
    }

    /**
     * 获取网站根目录
     * @param string $suffix
     * @return string
     */
    public function getPublicPath(string $suffix = ''): string
    {
        return $this->rootPath . 'public' . DIRECTORY_SEPARATOR . $suffix;
    }


    /**
     * 获取临时运行目录
     * @param string $suffix
     * @return string
     */
    public function getRuntimePath(string $suffix = ''): string
    {
        return $this->rootPath . 'runtime' . DIRECTORY_SEPARATOR . $suffix;
    }

}