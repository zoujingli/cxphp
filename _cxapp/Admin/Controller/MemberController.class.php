<?php

namespace Admin\Controller;

/**
 * 会员列表管理
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class MemberController extends \Admin\Controller\AdminController {

	protected function _index_filter(&$map) {
		$map['user_status'] = 1;
	}

	/**
	 * 标志删除用户
	 */
	public function delete() {
		parent::delete(M('Members'), I('get.id'), 'user_status');
	}

}
