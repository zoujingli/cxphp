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

use cxphp\core\App;

/**
 * 响应对象
 * Class Response
 * @package cxphp\http
 */
class Response extends \Workerman\Protocols\Http\Response
{
    /** @var App */
    public $app;

    /**
     * 创建响应对象
     * @return static
     */
    public static function make()
    {
        $static = new static;
        $static->app = App::$instance;
        return $static;
    }

    /**
     * XML 返回
     * @param string $xml
     * @return $this
     */
    public function xml($xml)
    {
        if ($xml instanceof \SimpleXMLElement) {
            $xml = $xml->asXML();
        }
        $this->header('Content-Type', 'text/xml');
        $this->withBody($xml);
        return $this;
    }

    /**
     * JSON 返回
     * @param array $data
     * @param int $option
     * @return $this
     */
    public function json(array $data, $option = JSON_UNESCAPED_UNICODE)
    {
        $this->header('Content-Type', 'application/json');
        $this->withBody(json_encode($data, $option));
        return $this;
    }

    /**
     * 下载文件
     * @param string $file
     * @param string $name
     * @return $this
     */
    public function download($file, $name)
    {
        $this->withFile($file);
        if ($name) $this->header('Content-Disposition', "attachment; filename=\"{$name}\"");
        return $this;
    }

    /**
     * 网页跳转
     * @param string $location
     * @param int $status
     * @param array $headers
     * @return $this
     */
    public function redirect($location, $status = 302, $headers = [])
    {
        $this->withStatus($status);
        $this->header('Location', $location);
        if (!empty($headers)) $this->withHeaders($headers);
        return $this;
    }

    /**
     * 视图文件输入
     * @param string $tpl
     * @param array $vars
     * @return $this
     * @throws \cxphp\core\Exception
     */
    public function view($tpl, $vars = [])
    {
        $this->_body = $this->app->make(View::class)->fetch($tpl, $vars);
        return $this;
    }

    /**
     * 回复文体内容
     * @param string $txt
     * @return $this
     */
    public function content($txt)
    {
        $this->_body = $txt;
        return $this;
    }

    /**
     * 显示异常页面
     * @param \Exception $exception
     * @return $this
     */
    public function exceptionPage(\Exception $exception)
    {
        error_reporting(0);
        $this->_status = 500;
        $template = $this->app->getCorePath('tpl' . DIRECTORY_SEPARATOR . 'exception.php');
        $this->_body = file_exists($template) ? $this->execPhpFile($template, ['e' => $exception]) : $exception->getMessage();
        return $this;
    }

    /**
     * 显示 404 页面
     * @return $this
     */
    public function notFoundPage()
    {
        $template = $this->app->getCorePath('tpl' . DIRECTORY_SEPARATOR . 'notfound.php');
        $this->_body = file_exists($template) ? $this->execPhpFile($template) : '404 Page not found.';
        $this->_status = 404;
        return $this;
    }

    /**
     * 执行PHP文件
     * @param string $file
     * @param array $vars
     * @return string
     */
    private function execPhpFile($file, array $vars = [])
    {
        \ob_start();
        try {
            extract($vars, EXTR_OVERWRITE);
            include $file;
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
        return \ob_get_clean();
    }

    /**
     * 获取响应内容
     * @return string|null
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * 获取响应状态
     * @return int|null
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * 绑定文件对象
     * @param string $file
     * @return $this
     */
    public function file($file)
    {
        if ($this->notModifiedSince($file)) {
            return $this->withStatus(304);
        } else {
            return $this->withFile($file);
        }
    }

    /**
     * 发送消息内容
     */
    public function send()
    {
        $this->header('Server', 'server');
        $keepAlive = $this->app->request->header('connection');
        if ((is_null($keepAlive) && $this->app->request->protocolVersion() === '1.1') || strtolower($keepAlive) === 'keep-alive') {
            $this->app->request->connection->send($this);
        } else {
            $this->app->request->connection->close($this);
        }
    }

    /**
     * 判断文件是否需要重新下载
     * @param string $filepath
     * @return bool
     */
    protected function notModifiedSince($filepath)
    {
        $ifModifiedSince = $this->app->request->header('if-modified-since');
        if ($ifModifiedSince === null || !($mtime = \filemtime($filepath))) return false;
        return $ifModifiedSince === \date('D, d M Y H:i:s', $mtime) . ' ' . \date_default_timezone_get();
    }
}