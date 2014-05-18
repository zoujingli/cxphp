<?php

namespace Home\Controller;

/**
 * 首页
 */
class IndexController extends \Home\Controller\HomeController {

	/**
	 * 首页
	 */
	public function index() {
		$this->display("index:index");
	}

}
