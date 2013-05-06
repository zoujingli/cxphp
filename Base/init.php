<?php

/* ==============================================================
 *        版本：D.20121120        字符编码:UTF-8
 * ==============================================================
 * CXPHP是由LAMP兄弟连51期学员在培训学习时开发创建，以兄弟连BroPHP框
 * 架及国内著名开源框架ThinkPHP作为参考，遵循面向对象及MVC开发模式设计
 * 目前此框架仅作为学习研发使用。CXPHP框架以实用性为目的，不断更新。
 * =============================================================
 * 作者：邹景立  邮箱:anyon@139.com  微博：http://weibo.com/anyons
 * ============================================================== */

header("Content-Type:text/html;charset=utf-8");//统一页面编码
date_default_timezone_set('PRC');//调整系统时区
define('REQUEST_TIME_START', microtime(true));//记录页面开始执行的时间
session_start();//默认开启SESSION会话
//声明框架源文件所在目录
define('CXPHP', str_replace('\\', '/', dirname(__FILE__)));
//定义项目根目录
define('PRO_PATH', substr(CXPHP, 0, strspn(CXPHP, str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']))) - 1));
//定义应用名称
defined('APP_NAME') or define('APP_NAME', 'Home');
//定义应用目录
defined('APP_PATH') or define('APP_PATH', './' . APP_NAME);
//定义样式主题(默认Default)
defined('TPL_STYLE') or define('TPL_STYLE', 'Default');
//开启DUBGE调试(默认开启)
defined('DEBUG') or define('DEBUG', true);
//载入系统函数库
require CXPHP . '/Common/function.inc.php';
//载入配置文件并应用配置
C(require CXPHP . '/Config/base.config.php');
C(@include C('PRO_PATH') . '/config.php');
C(@include C('APP_PATH') . '/config.php');
//导入用户自定义函数库
@include C('APP_PATH') . '/Common/function.inc.php';
//设置系统自动加载的目录
set_include_path(get_include_path() . PATH_SEPARATOR . join(PATH_SEPARATOR, C('AUTO_PATH')));
//执行相关的操作
Action::run();