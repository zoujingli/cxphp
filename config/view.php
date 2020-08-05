<?php

return [
    'default' => 'think',
    'channel' => [
        'think' => [
            // 模板目录名
            'view_dir_name'      => 'view',
            // 模板后缀
            'view_suffix'        => 'html',
            // 去除HTML空格换行
            'strip_space'        => true,
            // 模板文件名分隔符
            'view_depr'          => DIRECTORY_SEPARATOR,
            // 模板缓存配置
            'tpl_cache'          => false,
            // 模板引擎普通标签开始标记
            'tpl_begin'          => '{',
            // 模板引擎普通标签结束标记
            'tpl_end'            => '}',
            // 标签库标签开始标记
            'taglib_begin'       => '{',
            // 标签库标签结束标记
            'taglib_end'         => '}',
            // 定义模板替换字符串
            'tpl_replace_string' => [

            ],
        ],
    ],
];