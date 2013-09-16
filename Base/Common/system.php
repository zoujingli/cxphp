<?php

/**
 * 系统函数库文件
 *
 * @author Anyon
 * @datetime 2013/9/16
 */
function cache($name, $value = null, $type = 'static') {
	static $_cache = array();
	$index = md5($type . $name, true);
	switch ($type) {
		case 'static':
			if (isset($_cache[$index]) && !is_null($value)) {
				return $_cache[$index];
			} else {
				return $_cache[$index] = $value;
			}
		case 'file':
			
	}
}
