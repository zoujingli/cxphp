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

use cxphp\http\request\UploadFile;

/**
 * 当前请求对象
 * Class Request
 * @package cxphp
 */
class Request extends \Workerman\Protocols\Http\Request
{
    /** @var string */
    public $realnode;

    /** @var string */
    public $realpath;

    /** @var string */
    public $action = null;

    /**  @var string */
    public $module = null;

    /** @var string */
    public $controller = null;

    /**
     * 获取所有输入数据
     * @return array
     */
    public function all()
    {
        return $this->post() + $this->get();
    }

    /**
     * 获取输入数据 支持默认值和过滤
     * @param string $name 变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function input($name, $default = null)
    {
        $all = $this->all();
        return $all[$name] ?? $default;
    }

    /**
     * 获取指定字段的输入数据
     * @param array $keys 指定字段
     * @return array
     */
    public function only(array $keys)
    {
        $result = [];
        $all = $this->all();
        foreach ($keys as $key) {
            if (isset($all[$key])) {
                $result[$key] = $all[$key];
            }
        }
        return $result;
    }

    /**
     * 获取排除字段的输入数据
     * @param array $keys 排除字段
     * @return array
     */
    public function except(array $keys)
    {
        $all = $this->all();
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        return $all;
    }

    /**
     * 获取上传的文件对象
     * @param null $name 指定字段名称
     * @return null|array|UploadFile
     */
    public function file($name = null)
    {
        $files = parent::file($name);
        if (null === $files) {
            return $name === null ? [] : null;
        }
        if ($name !== null) {
            return new UploadFile($files['tmp_name'], $files['name'], $files['type'], $files['error']);
        }
        $uploadFiles = [];
        foreach ($files as $name => $file) {
            $uploadFiles[$name] = new UploadFile($file['tmp_name'], $file['name'], $file['type'], $file['error']);
        }
        return $uploadFiles;
    }

    /**
     * 当前服务端的IP
     * @return string
     */
    public function getRemoteIp()
    {
        return $this->connection->getRemoteIp();
    }

    /**
     * 当前服务端的端口号
     * @return integer
     */
    public function getRemotePort()
    {
        return $this->connection->getRemotePort();
    }

    /**
     * 当前请求客户端的IP
     * @return string
     */
    public function getLocalIp()
    {
        return $this->connection->getLocalIp();
    }

    /**
     * 当前客户端的端口号
     * @return integer
     */
    public function getLocalPort()
    {
        return $this->connection->getLocalPort();
    }

    /**
     * 当前请求路径
     * @return string
     */
    public function url()
    {
        return '//' . $this->host() . $this->path();
    }

    /**
     * 当前请求地址
     * @return string
     */
    public function fullUrl()
    {
        return '//' . $this->host() . $this->uri();
    }

    /**
     * 是否为 Ajax 请求
     * @return bool
     */
    public function isAjax()
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * 是否为 Pjax 请求
     * @return bool
     */
    public function isPjax()
    {
        return (bool)$this->header('X-PJAX');
    }
}