<?php

namespace Admin\Controller;

/**
 * 网站配置管理
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class SettingController extends \Admin\Controller\AdminController {

	/**
	 * 可用的URL模式
	 * @var type 
	 */
	protected $urlmodes = array(
		"0"	 => "普通模式",
		"1"	 => "PATHINFO模式",
		"2"	 => "REWRITE模式"
	);

	function index() {
		
	}

	/**
	 * 前台网站信息配置
	 */
	function site() {
		if (IS_POST) {
			$home_configs = (array) F("site_options", "", C('APP_CONF_PATH'));
			$home_configs["DEFAULT_THEME"] = $_POST['options']['site_tpl'];
			$home_configs["URL_MODEL"] = $_POST['options']['urlmode'];
			$home_configs["URL_HTML_SUFFIX"] = $_POST['options']['html_suffix'];
//			$home_configs['TMPL_ACTION_ERROR'] = C("APP_TPL_PATH") . $_POST['options']['site_tpl'] . '/error.html'; // 默认错误跳转对应的模板文件
//			$home_configs['TMPL_ACTION_SUCCESS'] = C("APP_TPL_PATH") . $_POST['options']['site_tpl'] . '/success.html'; // 默认成功跳转对应的模板文件
//			$home_configs['TMPL_EXCEPTION_FILE'] = C("APP_TPL_PATH") . $_POST['options']['site_tpl'] . '/error.html'; // 异常页面的模板文件
			$home_configs['TMPL_PARSE_STRING'] = array(
				'__TMPL__'	 => __ROOT__ . '/Home/' . $_POST['options']['site_tpl'],
				'__STATIC__' => __ROOT__ . '/static',
			);
			$data['option_name'] = "site_options";
			$data['option_value'] = json_encode(I('post.options'));

			$where = array();
			isset($_POST['option_id']) && $where['option_id'] = I('post.option_id');
			$result = M('Options')->where($where)->add($data, array(), true);
			if ($result) {
				F("site_options", array_merge($home_configs, get_site_options()), C('APP_CONF_PATH'));
				$this->success("保存成功！");
			} else {
				$this->error("保存失败！");
			}
		} else {
			$option = M('Options')->where("option_name='site_options'")->find();
			$this->assign('urlmodes', $this->urlmodes);
			$this->assign("templates", get_dir_list(C("APP_TPL_PATH")));
			if ($option) {
				$this->assign((array) json_decode($option['option_value']));
				$this->assign("option_id", $option['option_id']);
			}
			$this->display($this->getLowerTplName());
		}
	}

	/**
	 * 用户密码修改
	 */
	function password() {
		if (IS_POST) {
			$user_obj = new \Admin\Model\UsersModel();
			$admin = session('user');
			$old_password = md5($_POST['old_password']);
			$password = md5($_POST['password']);
			if ($old_password == $admin['user_pass']) {
				if ($admin['user_pass'] == $password) {
					$this->error("新密码不能和原始密码相同！");
				} else {
					$data = array();
					$data['user_pass'] = $password;
					$data['ID'] = $admin['ID'];
					$r = $user_obj->save($data);
					if ($r) {
						$admin['user_pass'] = $password;
						session('user', $admin);
						$this->success("修改成功！");
					} else {
						$this->error("修改失败！");
					}
				}
			} else {
				$this->error("原始密码不正确！");
			}
		} else {
			$this->display($this->getLowerTplName());
		}
	}

	//清除缓存
	function clearcache() {
		clear_cache();
		$this->display();
	}

}
