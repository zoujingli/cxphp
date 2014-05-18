<?php

/**
 * 系统Admin应用配置文件
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
/* 加载系统SDK配置文件 */
$sdk_config = (Array) F('sdk_options', '', C('APP_CONF_PATH'));
/* 加载网站常规配置文件 */
$site_config = (Array) F('site_options', '', C('APP_CONF_PATH'));
/* 后台应用配置文件 */
$config = array(
	'DEFAULT_THEME'		 => '',
	'URL_MODEL'			 => 1,
	'TMPL_FILE_DEPR'	 => '.',
	'TMPL_PARSE_STRING'	 => array(
		'__STATIC__' => __ROOT__ . '/static',
	)
);
/* 合并各项配置项目 */
return array_merge($site_config, $sdk_config, $config);
