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

namespace cxphp\logger;

use Psr\Log\LoggerInterface;

/**
 * 日志驱动接口
 * Class Driver
 * @package cxphp\logger
 */
abstract class Driver implements LoggerInterface
{
    /**
     * 记录 emergency 信息
     * @param mixed $message 日志信息
     * @param array $context 日志内容
     */
    public function emergency($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录警报信息
     * @param mixed $message 日志信息
     * @param array $context 日志内容
     */
    public function alert($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录紧急情况
     * @param mixed $message 日志信息
     * @param array $context 日志内容
     */
    public function critical($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录错误信息
     * @param mixed $message 日志信息
     * @param array $context 日志内容
     */
    public function error($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录warning信息
     * @param mixed $message 日志信息
     * @param array $context 日志内容
     */
    public function warning($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录notice信息
     * @param mixed $message 日志信息
     * @param array $context 日志内容
     */
    public function notice($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录一般信息
     * @param mixed $message 日志信息
     * @param array $context 日志内容
     */
    public function info($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录调试信息
     * @param mixed $message 日志信息
     * @param array $context 日志内容
     */
    public function debug($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录 sql 信息
     * @param mixed $message 日志信息
     * @param array $context 日志内容
     */
    public function sql($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录日志信息
     * @param mixed $level 日志级别
     * @param mixed $message 日志信息
     * @param array $context 日志内容
     */
    public function log($level, $message, array $context = [])
    {
        $this->record($message, $level, $context);
    }

    /**
     * 记录日志信息
     * @param mixed $message 日志信息
     * @param string $type 日志级别
     * @param array $context 日志内容
     * @return $this
     */
    public function record($message, string $type = 'info', array $context = [])
    {
        $record = [];
        if (is_string($message) && !empty($context)) {
            $replace = [];
            foreach ($context as $key => $val) {
                $replace['{' . $key . '}'] = $val;
            }
            $message = strtr($message, $replace);
        }
        if (!empty($message) || 0 === $message) {
            $record[$type][] = $message;
        }
        $this->save($record);
        return $this;
    }

    abstract public function save(array $record): bool;
}