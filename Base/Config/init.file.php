<?php

/**
 * CXPHP 框架的初始化文件配置文件(只会在初始化时调用一次)
 */
$config_null = <<<confignull
<?php
    //项目配置文件（自动生成）
    return array(
        //配置项　＝> 值,
    );
confignull;
$config_str = <<<config
<?php

//项目配置文件（自动生成）
return array(
    /* 系统配置 */
    //'DEBUG' => true,                    //开启Debug测试
    //'TPL_STYLE'=>'Default',              //模板样式

    /* 数据库配置 */
    'DB_TYPE' => 'pdo',                 // 数据库类型（mysqli或pdo）
    'DB_HOST' => 'localhost',           // 服务器地址
    'DB_NAME' => '',                    // 数据库名
    'DB_USER' => 'root',                // 用户名
    'DB_PWD' => '',                     // 密码
    'DB_PORT' => '3306',                // 端口
    'DB_PREFIX' => 'cx_',               // 数据库表前缀
    'DB_CHARSET' => 'utf8',             // 数据库编码默认采用utf8

    /* 模板引擎配置 */
    'TPL_L_TAG' => '<{',                //Smarty模板的左定界符
    'TPL_R_TAG' => '}>',                //Smarty模板的右定界符
    'TPL_CACHEING' => false,            //是否开启模板缓存
    'TPL_CACHE_TIME' => 3600,           //缓存时间
    'TPL_SUFFIX' => '.tpl',               //模板文件后缀
    'TPL_SUCCESS' => 'Public/success',	//操作成功的提示页
    'TPL_ERROR' => 'Public/error',		//操作失败的提示页
);
config;
$func = <<<func
<?php
     //应用下的公共函数库文件（自动生成)
func;
$index_str = <<<index
<?php

//Index模块（自动生成）
class IndexAction extends Action {
    function init(){
        //默认执行的方法
    }
    function index() {
        echo '<h2>欢迎使用 CXPHP 框架（晨星PHP框架）测试版 1.1 2012-11-15</h2>';
        echo '<hr />';
        echo '<p>本程序属于自由开源软件，任何人可对此程序做修改，无需承担任何责任！</p>';
        echo '<hr />';
        echo '<p>【作者】LAMP兄弟连-51期 邹景立</p>';
        echo '<p>【Email】anyon@139.com  【Weibo】http://weibo.com/anyons</p>';
        echo '<hr />';
    }

}
index;

return array(
    C('PRO_PATH') . '/config.php' => $config_str,
    C('APP_PATH') . '/Action/IndexAction.class.php' => $index_str,
    C('RUN_PATH') . '/CXPHP_LOCK_FILE' => '已经缓存PHP文件',
    C('APP_PATH') . '/config.php' => $config_null,
    C('APP_PATH') . '/Common/function.inc.php' => $func,
    C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/Public/success' . C('TPL_SUFFIX') => file_get_contents(C('CXPHP') . '/Common/file_success'),
    C('APP_PATH') . '/View/' . C('TPL_STYLE') . '/Public/error' . C('TPL_SUFFIX') => file_get_contents(C('CXPHP') . '/Common/file_error'),
);


