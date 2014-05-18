<?php

namespace Admin\Controller;

/**
 * 后台管理显示
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class IndexController extends \Admin\Controller\AdminController {

	/**
	 * 后台框架首页
	 */
	public function index() {
		$this->assign("SUBMENU_CONFIG", json_encode(D("Menu")->get_tree(0)));
		$this->display('public:index');
	}

}
