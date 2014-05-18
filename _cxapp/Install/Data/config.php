<?php

/**
 * 系统配置文件
 */
return array(
	'DB_TYPE'			 => 'mysql',
	'DB_HOST'			 => '#DB_HOST#',
	'DB_NAME'			 => '#DB_NAME#',
	'DB_USER'			 => '#DB_USER#',
	'DB_PWD'			 => '#DB_PWD#',
	'DB_PORT'			 => '#DB_PORT#',
	'DB_PREFIX'			 => '#DB_PREFIX#',
	/* Default Module */
	'DEFAULT_MODULE'	 => 'Home',
	/* Data Auth Key */
	"DATA_AUTH_KEY"		 => '#AUTHCODE#',
	/* cookies Prefix */
	"COOKIE_PREFIX"		 => '#COOKIE_PREFIX#',
	/* Home Tpl Path */
	'APP_TPL_PATH'		 => APP_ROOT . 'Home/',
	/* CMF Config Path */
	'APP_CONF_PATH'		 => APP_DATA_PATH . 'config/',
	/* CMF Databack Path */
	'APP_DATA_PATH_PATH' => APP_DATA_PATH . 'backup/',
	/* TMPL Parse String  */
	'TMPL_PARSE_STRING'	 => array(
		'__TMPL__' => __ROOT__ . '/Home/default',
	),
);
