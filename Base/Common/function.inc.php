<?php

/**
 * 系统配置函数 C()
 * @param null 不传入参数则会返回所有的配置信息(array)
 * @param string $name 一个参数为取值操作
 * @param array|string null|value 两个参数为赋值操作
 * @return array|null
 */
function C(){
	static $_config = array();
	$args = func_get_args();
	switch(count($args)){
		case 0:  //返回所有的配置系统
			return $_config;
		case 1:  //返回指定的值或批量设置值
			if(is_array($args[0])){
				return $_config = array_merge($_config,$args[0]);
			}elseif(is_string($args[0])){
				return array_key_exists($args[0],$_config) ? $_config[$args[0]] : NULL;
			}
		case 2:   //设置值(支持数组)
			if(!is_array($args[1])){
				return $_config[$args[0]] = $args[1];
			}else{
				$old_name = array_key_exists($args[0],$_config) ? $_config[$args[0]] : array();
				return $_config[$args[0]] = array_merge($old_name,$args[1]);
			}
	}
	return null;
}

/**
 * 系统输出调试函数 P()
 * @param stirng|object|number|array 需要输出的对象 
 */
function P(){
	$args = func_get_args();
	echo '<div style="width:100%;text-align:left"><pre>';
	foreach($args as $arg){
		if(is_array($arg)){
			print_r($arg);
			echo '<br>';
		}else if(is_string($arg)){
			echo $arg . '<br>';
		}else{
			var_dump($arg);
			echo '<br>';
		}
	}
	echo '</pre></div>';
}

//PHP5魔术方法 自动加载需要用到的类
function __autoload($className){
	if($className === 'Smarty'){
		include C('CXPHP') . '/Smarty/' . $className . '.class.php';
	}else{
		include $className . '.class.php';
	}
	Debug::addmsg('自动加载类：' . $className . '.class.php',1);
}

/**
 * 去除代码中的注释及多余空白符 strip_whitespace()
 * @param string $content  需要处理的字串
 * @return string
 */
function strip_whitespace($content){
	$stripStr = '';
	$tokens = token_get_all($content);
	$last_space = false;
	for($i = 0,$j = count($tokens); $i < $j; $i++){
		if(is_string($tokens[$i])){
			$last_space = false;
			$stripStr .= $tokens[$i];
		}else{
			switch($tokens[$i][0]){
				case T_COMMENT:
				case T_DOC_COMMENT:
					break;
				case T_WHITESPACE:
					if(!$last_space){
						$stripStr .= ' ';
						$last_space = true;
					}
					break;
				default:
					$last_space = false;
					$stripStr .= $tokens[$i][1];
			}
		}
	}
	return $stripStr;
}

/**
 * 创建Model中的数据库操作对象
 *  @param	string	$className	类名或表名
 *  @param	string	$app	 应用名,访问其他应用的Model
 *  @return	object	数据库连接对象
 */
function D($name = null,$app = ''){
	$db = null;
	if(is_null($name)){
		$class = "Db" . ucfirst(strtolower(C('DB_TYPE')));
		$db = new $class;
	}else{
		$name = strtolower($name);
		$model = M($name,$app);
		if(!empty($model)){
			$model = new $model();
			$model->setTable($name);
			$db = $model;
		}
	}
	$db->path = C('RUN_PATH') . '/Data';
	return $db;
}

/**
 * 实例化一个没有模型文件的Model
 * @param string $name Model名称 支持指定基础模型 例如 MongoModel:User
 * @param string $tablePrefix 表前缀
 * @param mixed $connection 数据库连接信息
 * @return Model
 */
function M($name = '',$app = ''){
	$db_class = "Db" . ucfirst(strtolower(C('DB_TYPE')));
	$name = ucfirst(strtolower($name));
	$controler_src = C('APP_PATH') . '/Model/' . $name . 'Model.class.php';
	$runfile = C('RUN_PATH') . '/Model/' . $name . 'Model.class.php';
	if(file_exists($controler_src)){
		if(!file_exists($runfile) || C('DEBUG')){
			file_put_contents($runfile,strip_whitespace(file_get_contents($controler_src)));
		}
		Debug::addmsg("当前操作的Model类: $controler_src");
		return $name . 'Model';
	}else{
		return $db_class;
	}
}