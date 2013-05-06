<?php

/**
 * 框架的核心配置文件
 */
$run_path = APP_PATH . '/~Runtime';

//核心的配置文件
return array(
    'CXPHP' => CXPHP,
    'APP_PATH' => APP_PATH,
    'APP_NAME' => APP_NAME,
    'PRO_PATH' => PRO_PATH,
    'TPL_STYLE' => TPL_STYLE,
    'DEBUG' => DEBUG,
    'RUN_PATH' => $run_path,
    'AUTO_PATH' => array(/* 需要自动加载的目录 */
        CXPHP . '/Class',
        CXPHP . '/Model',
        CXPHP . '/Extend',
        APP_PATH . '/Extend',
        APP_PATH . '/~Runtime/Action',
        APP_PATH . '/~Runtime/Model',
    ),
    /* 数据库配置 */
    'DB_TYPE' => 'pdo', // 数据库类型（mysqli或pdo）
    'DB_HOST' => 'localhost', // 服务器地址
    'DB_NAME' => '', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => '', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'cx_', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8

    /* 模板引擎配置 */
    'TPL_L_TAG' => '<{',
    'TPL_R_TAG' => '}>',
    'TPL_DIR' => APP_PATH . '/View/' . TPL_STYLE,
    'TPL_COMPILE_DIR' => $run_path . '/Compile/' . TPL_STYLE,
    'TPL_CACHEING' => false,
    'TPL_CACHE_DIR' => $run_path . '/Cache/' . TPL_STYLE,
    'TPL_CACHE_TIME' => 3600,
    'TPL_SUFFIX' => '.tpl',
    'TPL_SUCCESS' => 'Public/success',
    'TPL_ERROR' => 'Public/error',
);
