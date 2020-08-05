<?php

return [
    // 默认日志记录通道
    'default' => 'file',
    // 日志通道列表
    'channel' => [
        'file' => [
            // 日志记录方式
            'type'           => 'File',
            // 日志保存目录
            'path'           => '',
            // 单文件日志写入
            'single'         => true,
            // 指定日志类型
            'level'          => ['error', 'alert', 'sql'],
            // 独立日志级别
            'apart_level'    => ['error', 'sql'],
            // 每个文件大小 ( 10兆 )
            'file_size'      => 1024 * 1024 * 10,
            // 日志日期格式
            'time_format'    => 'Y-m-d H:i:s',
            // 最大日志文件数量
            'max_files'      => 100,
            // 使用JSON格式记录
            'json'           => false,
            // 日志处理
            'processor'      => null,
            // 关闭通道日志写入
            'close'          => false,
            // 日志输出格式化
            'format'         => '[%s][%s] %s',
            // 是否实时写入
            'realtime_write' => false,
        ],
    ],
];