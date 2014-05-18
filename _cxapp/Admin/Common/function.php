<?php

/**
 * 应用Admin应用函数
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */

/**
 * 取得指定目录文件列表
 * @param type $path 需要取得的目录位置
 * @return type
 */
function get_dir_list($path) {
	return array_diff(scandir($path), array(".", "..", ".svn"));
}

/**
 * 获取指定目录下模板名称
 * @param type $path 目录位置
 * @return type
 */
function get_file_list($path, $split = '.') {
	$list = get_dir_list($path);
	foreach ($list as &$tpl) {
		$tpl = strstr($tpl, $split, true);
	}
	return $list;
}

/**
 * 通过条件取用户信息
 * @param type $map
 * @param type $findType 查询方式
 */
function get_user_by_map($map = array(), $findType = 'find') {
	return D("Users")->where($map)->$findType();
}

/**
 * 用户权限检测
 * @param type $roleid 用户ID
 * @param type $g 模块名称
 * @param type $m 控制器名称
 * @param type $a 方法名称
 * @return boolean
 */
function checkAuth($roleid, $g = MODULE_NAME, $m = CONTROLLER_NAME, $a = ACTION_NAME) {
	/* 如果用户角色是1，则无需判断 */
	if ($roleid == 1) {
		return true;
	}
	$map = array(
		'role_id'	 => $roleid,
		'g'			 => $g,
		'm'			 => $m,
		'a'			 => $a,
	);
	return M("Access")->where($map)->count();
}
