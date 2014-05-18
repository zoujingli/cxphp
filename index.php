<?php

/* 开启调试模式 */
define("APP_DEBUG", TRUE);

/* 项目名称，不可更改 */
define('APP_NAME', 'CXPHP');

/* 定义应该根目录 */
define('APP_ROOT', str_replace('\\', '/', getcwd()) . '/');

/* 项目路径，不可更改 */
define('APP_PATH', APP_ROOT . '_cxapp/');

/* 数据写入目录 */
define('APP_DATA_PATH', APP_ROOT . 'static/data/');

/* 定义缓存路径 */
define("RUNTIME_PATH", APP_DATA_PATH . 'runtime/');

/* 系统安装检测 */
if (!file_exists(APP_DATA_PATH . "install.lock")) {
	$_GET['m'] = 'install';
}

/* 系统版本号 */
define("APP_VERSION", '1.0');

/* 载入框架核心文件 */
require APP_ROOT . './_think/ThinkPHP.php';
