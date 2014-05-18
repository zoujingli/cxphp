<?php

namespace Admin\Controller;

/**
 * 系统信息显示
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class MainController extends \Admin\Controller\AdminController {

	public function index() {
		/* 服务器信息 */
		$info = array(
			'操作系统'		 => PHP_OS,
			'运行环境'		 => $_SERVER["SERVER_SOFTWARE"],
			'PHP运行方式'	 => php_sapi_name(),
			'MYSQL版本'	 => mysql_get_server_info(),
			'程序版本'		 => APP_VERSION,
			'上传附件限制'	 => ini_get('upload_max_filesize'),
			'执行时间限制'	 => ini_get('max_execution_time') . "秒",
			'剩余空间'		 => round((disk_free_space(".") / (1024 * 1024)), 2) . 'M',
		);
		$sms = array(
			1	 => array('id' => '1', 'title' => '系统发布', 'content' => 'CXPHP ' . APP_VERSION . ' 发布，欢迎体验新版',),
			2	 => array('id' => '2', 'title' => '构架编码', 'content' => '<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=94620616&site=qq&menu=yes">Anyon</a>',),
			3	 => array('id' => '3', 'title' => 'QQ讨论', 'content' => '官方QQ群 230436388'),
		);
		$this->assign('server_info', $info);
		$this->assign('sms', $sms);
		$this->display($this->getLowerTplName());
	}

}
