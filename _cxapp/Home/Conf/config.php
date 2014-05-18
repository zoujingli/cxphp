<?php

$config = array(
	'DEFAULT_THEME'			 => 'default', // 默认模板主题名称
	'TMPL_TEMPLATE_SUFFIX'	 => '.html', // 默认模板文件后缀
	'TMPL_PARSE_STRING'		 => array(
		'__STATIC__' => __ROOT__ . '/static',
		'__CSS__'	 => __ROOT__ . '/static/css',
		'__JS__'	 => __ROOT__ . '/static/js',
		'__IMG__'	 => __ROOT__ . '/static/img',
	)
);
$site_config = (Array) F('site_options', '', C('APP_CONF_PATH'));
$sdk_config = (Array) F('sdk_options', '', C('APP_CONF_PATH'));

return array_merge($config, $site_config, $sdk_config);
