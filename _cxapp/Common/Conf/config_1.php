<?php

/**
 * 系统配置文件
 */
return array(
	'DB_TYPE'			 => 'mysql',
	'DB_HOST'			 => 'localhost',
	'DB_NAME'			 => 'cxphp_db',
	'DB_USER'			 => 'root',
	'DB_PWD'			 => '',
	'DB_PORT'			 => '3306',
	'DB_PREFIX'			 => 'cx_',
	/* Default Module */
	'DEFAULT_MODULE'	 => 'Home',
	/* Data Auth Key */
	"DATA_AUTH_KEY"		 => 'LuVBbSzkFMrAj0IKPN',
	/* cookies Prefix */
	"COOKIE_PREFIX"		 => '5rcclT_',
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
