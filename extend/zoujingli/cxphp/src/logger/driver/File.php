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

namespace cxphp\logger\driver;

use cxphp\App;
use cxphp\logger\Driver;

/**
 * 本地化调试输出到文件
 * Class File
 * @package cxphp\logger\driver
 */
class File extends Driver
{
    /** @var array */
    protected $config = [
        'max_files'   => 0,
        'log_level'   => [],
        'log_format'  => '[%s][%s] %s',
        'file_path'   => '',
        'time_format' => 'Y-m-d H:i:s',
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
        if (empty($this->config['log_format'])) {
            $this->config['log_format'] = '[%s][%s] %s';
        }
        if (empty($this->config['time_format'])) {
            $this->config['time_format'] = 'Y-m-d H:i:s';
        }
        if (empty($this->config['file_path'])) {
            $this->config['file_path'] = $app->getRuntimePath('logger');
        }
        $this->config['file_path'] = rtrim($this->config['file_path'], '\\/') . DIRECTORY_SEPARATOR;
    }

    /**
     * 日志写入接口
     * @param array $record 日志信息
     * @return bool
     */
    public function save(array $record): bool
    {
        $realpath = dirname($destination = $this->getMasterLogFile());
        file_exists($realpath) && is_dir($realpath) || mkdir($realpath, 0755, true);
        [$records, $datetime] = [[], date($this->config['time_format'], time())];
        foreach ($record as $type => $val) {
            $message = [];
            foreach ($val as $msg) {
                $message[] = sprintf($this->config['log_format'], $datetime, $type, is_string($msg) ? $msg : var_export($msg, true));
            }
            if (true === $this->config['log_level'] || in_array($type, $this->config['log_level'])) {
                $this->write($message, $this->getApartLevelFile($realpath, $type));
                continue;
            }
            $records[$type] = $message;
        }
        return $records ? $this->write($records, $destination) : true;
    }

    /**
     * 日志写入
     * @param array $records 日志信息
     * @param string $destination 日志文件
     * @return bool
     */
    private function write(array $records, string $destination): bool
    {
        foreach ($records as &$vo) $vo = is_array($vo) ? implode(PHP_EOL, $vo) : $vo;
        return error_log(implode(PHP_EOL, $records) . PHP_EOL, 3, $destination);
    }

    /**
     * 获取主日志文件名
     * @return string
     */
    private function getMasterLogFile(): string
    {
        if ($this->config['max_files'] > 0) {
            $files = glob($this->config['file_path'] . '*.log');
            if (count($files) > $this->config['max_files']) @unlink($files[0]);
            return $this->config['file_path'] . date('Ymd') . '.log';
        } else {
            return $this->config['file_path'] . date('Ym') . DIRECTORY_SEPARATOR . date('d') . '.log';
        }
    }

    /**
     * 获取独立日志文件名
     * @param string $path 日志目录
     * @param string $type 日志类型
     * @return string
     */
    private function getApartLevelFile(string $path, string $type): string
    {
        if ($this->config['max_files'] > 0) {
            return $path . DIRECTORY_SEPARATOR . date('Ymd') . '_' . $type . '.log';
        } else {
            return $path . DIRECTORY_SEPARATOR . date('d') . '_' . $type . '.log';
        }
    }
}