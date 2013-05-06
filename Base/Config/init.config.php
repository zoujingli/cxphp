<?php

/**
 * CXPHP 框架的初始化目录配置文件(在会在初始化时调用一次)
 */
return array(
    'INIT_PATH' => array(
        /* 项目的公共目录 */
        C('PRO_PATH'),
        C('PRO_PATH') . '/Public',
        C('PRO_PATH') . '/Public/Resource',
        C('PRO_PATH') . '/Public/Resource/css',
        C('PRO_PATH') . '/Public/Resource/js',
        C('PRO_PATH') . '/Public/Resource/image',
        C('PRO_PATH') . '/Public/Upload',
        /* 项目应用目录 */
        C('APP_PATH') . '/Common/',
        C('APP_PATH') . '/Action/',
        C('APP_PATH') . '/Model/',
        C('APP_PATH') . '/Extend/',
        C('APP_PATH') . '/View/',
        C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/',
        C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/Public/',
        C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/Public/',
        C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/Public/Resource/',
        C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/Public/Resource/css/',
        C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/Public/Resource/js/',
        C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/Public/Resource/image/',
        C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/',
        C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/Resource/',
        C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/Resource/css/',
        C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/Resource/js/',
        C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/Resource/image/',
        /* 缓存运行目录 */
        C('RUN_PATH'),
        C('RUN_PATH') . '/Action/',
        C('RUN_PATH') . '/Model/',
        C('RUN_PATH') . '/Cache/',
        C('RUN_PATH') . '/Cache/' . C('TPL_STYLE') . '/',
        C('RUN_PATH') . '/Compile/',
        C('RUN_PATH') . '/Compile/' . C('TPL_STYLE') . '/',
        C('RUN_PATH') . '/Data/',
    ),
);


