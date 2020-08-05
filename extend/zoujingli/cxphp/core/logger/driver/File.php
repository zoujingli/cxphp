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

namespace cxphp\core\logger\driver;

use cxphp\core\App;
use cxphp\core\logger\Driver;

/**
 * 本地化调试输出到文件
 * Class File
 * @package cxphp\core\logger\driver
 */
class File extends Driver
{
    /**
     * 配置参数
     * @var array
     */
    protected $config = [
        'time_format'  => 'c',
        'single'       => false,
        'file_size'    => 2097152,
        'path'         => '',
        'apart_level'  => [],
        'max_files'    => 0,
        'json'         => false,
        'json_options' => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        'format'       => '[%s][%s] %s',
    ];

    /**
     * File constructor.
     * @param App $app
     * @param array $config
     */
    public function __construct(App $app, $config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
        if (empty($this->config['format'])) {
            $this->config['format'] = '[%s][%s] %s';
        }
        if (empty($this->config['path'])) {
            $this->config['path'] = $app->getRuntimePath('logger');
        }
        if (substr($this->config['path'], -1) != DIRECTORY_SEPARATOR) {
            $this->config['path'] .= DIRECTORY_SEPARATOR;
        }
    }

    /**
     * 日志写入接口
     * @param array $record 日志信息
     * @return bool
     */
    public function save(array $record): bool
    {
        $destination = $this->getMasterLogFile();
        $path = dirname($destination);
        !is_dir($path) && mkdir($path, 0755, true);
        $info = [];
        // 日志信息封装
        $time = \DateTime::createFromFormat('0.u00 U', microtime())->setTimezone(new \DateTimeZone(date_default_timezone_get()))->format($this->config['time_format']);
        foreach ($record as $type => $val) {
            $message = [];
            foreach ($val as $msg) {
                if (!is_string($msg)) {
                    $msg = var_export($msg, true);
                }
                if ($this->config['json']) {
                    $message[] = json_encode(['time' => $time, 'type' => $type, 'msg' => $msg], $this->config['json_options']);
                } else {
                    $message[] = sprintf($this->config['format'], $time, $type, $msg);
                }
            }
            if (true === $this->config['apart_level'] || in_array($type, $this->config['apart_level'])) {
                // 独立记录的日志级别
                $filename = $this->getApartLevelFile($path, $type);
                $this->write($message, $filename);
                continue;
            }
            $info[$type] = $message;
        }
        return $info ? $this->write($info, $destination) : true;
    }

    /**
     * 日志写入
     * @param array $message 日志信息
     * @param string $destination 日志文件
     * @return bool
     */
    protected function write(array $message, string $destination): bool
    {
        $info = [];
        $this->checkLogSize($destination);
        foreach ($message as $type => $msg) {
            $info[$type] = is_array($msg) ? implode(PHP_EOL, $msg) : $msg;
        }
        return error_log(implode(PHP_EOL, $info) . PHP_EOL, 3, $destination);
    }

    /**
     * 获取主日志文件名
     * @return string
     */
    protected function getMasterLogFile(): string
    {
        if ($this->config['max_files']) {
            $files = glob($this->config['path'] . '*.log');
            try {
                if (count($files) > $this->config['max_files']) {
                    unlink($files[0]);
                }
            } catch (\Exception $exception) {
            }
        }
        if ($this->config['single']) {
            $name = is_string($this->config['single']) ? $this->config['single'] : 'single';
            $destination = $this->config['path'] . $name . '.log';
        } else {
            if ($this->config['max_files']) {
                $filename = date('Ymd') . '.log';
            } else {
                $filename = date('Ym') . DIRECTORY_SEPARATOR . date('d') . '.log';
            }
            $destination = $this->config['path'] . $filename;
        }
        return $destination;
    }

    /**
     * 获取独立日志文件名
     * @param string $path 日志目录
     * @param string $type 日志类型
     * @return string
     */
    protected function getApartLevelFile(string $path, string $type): string
    {
        if ($this->config['single']) {
            $name = is_string($this->config['single']) ? $this->config['single'] : 'single';
            $name .= '_' . $type;
        } elseif ($this->config['max_files']) {
            $name = date('Ymd') . '_' . $type;
        } else {
            $name = date('d') . '_' . $type;
        }
        return $path . DIRECTORY_SEPARATOR . $name . '.log';
    }

    /**
     * 检查日志文件大小并自动生成备份文件
     * @param string $destination 日志文件
     */
    protected function checkLogSize(string $destination): void
    {
        if (is_file($destination) && floor($this->config['file_size']) <= filesize($destination)) {
            try {
                rename($destination, dirname($destination) . DIRECTORY_SEPARATOR . time() . '-' . basename($destination));
            } catch (\Exception $exception) {
            }
        }
    }
}