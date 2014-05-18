<?php

namespace Admin\Controller;

/**
 * 友情链接管理
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class LinkController extends \Admin\Controller\AdminController {

	protected $targets = array(
		"_blank" => "新标签页打开",
		"_self"	 => "本窗口页打开",
	);

	public function _initialize() {
		parent::_initialize();
		$this->assign('targets', $this->targets);
	}

}
