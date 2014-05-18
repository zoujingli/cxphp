<?php

namespace Admin\Controller;

/**
 * Api管理控制器
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class ApiController extends \Admin\Controller\AdminController {

	/**
	 * 显示第三方用户列表
	 */
	public function index() {
		parent::index(M('OauthMember'), $this->getLowerTplName(), array('status' => 1));
	}

	/**
	 * 删除第三方用户
	 */
	public function delete() {
		parent::delete(M("OauthMember"), I('get.id'), 'status');
	}

	/**
	 * 设置SKD配置
	 */
	function setting() {
		if ($_POST) {
			$host = !C('site_host') ? '' : '@' . C('site_host');
			$config = array(
				'THINK_SDK_QQ'			 => array(
					'APP_KEY'	 => I('post.qq_key'),
					'APP_SECRET' => I('post.qq_sec'),
					'CALLBACK'	 => U('api/oauth/callback' . $host, array('type' => 'qq'), true, true),
				),
				'THINK_SDK_SINA'		 => array(
					'APP_KEY'	 => I('post.sina_key'),
					'APP_SECRET' => I('post.sina_sec'),
					'CALLBACK'	 => U('api/oauth/callback' . $host, array('type' => 'sina'), true, true),
				),
				'WECHAT_TOKEN'			 => I('post.wx_tok'),
				'WECHAT_APPID'			 => I('post.wx_id'),
				'WECHAT_APPSECRET'		 => I('post.wx_sec'),
				'WECHAT_AUTO_REPLY'		 => I('post.wx_auto_reply'),
				'WECHAT_AUTO_DEFAULT'	 => I('post.wx_auto_default'),
			);
			if (false !== F('sdk_options', $config, C('APP_CONF_PATH'))) {
				$this->success("更新成功！");
			} else {
				$this->error("更新失败！");
			}
			exit;
		}
		$this->display($this->getLowerTplName());
	}

}
