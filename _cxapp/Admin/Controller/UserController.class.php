<?php

namespace Admin\Controller;

/**
 * 后台用户管理
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class UserController extends \Admin\Controller\AdminController {

	function index() {
		$users = D('Users')->where("user_status=1")->select();
		$roles_src = D('Role')->select();
		$roles = array();
		foreach ($roles_src as $r) {
			$roleid = $r['id'];
			$roles["$roleid"] = $r;
		}
		$this->assign("roles", $roles);
		$this->assign("users", $users);
		$this->display($this->getLowerTplName());
	}

	function add() {
		if (IS_POST) {
			if (D('Users')->create()) {
				if (D('Users')->add()) {
					$this->success("添加成功！", U("user/index"));
				} else {
					$this->error("添加失败！");
				}
			} else {
				$this->error(D('Users')->getError());
			}
		} else {
			$roles = D('Role')->where("status=1")->select();
			$this->assign("roles", $roles);
			$this->display($this->getLowerTplName('info'));
		}
	}

	function edit() {
		if (IS_POST) {
			if ($this->ad_obj->create()) {
				if ($this->ad_obj->save()) {
					$this->success("保存成功！", U("ad/index"));
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($this->ad_obj->getError());
			}
		} else {
			$id = I("get.id");
			$ad = $this->ad_obj->where("ad_id=$id")->find();
			$this->assign($ad);
			$this->display($this->getLowerTplName('info'));
		}
	}

	/**
	 *  删除
	 */
	function delete() {
		$id = (int) I("get.id");
		$data['user_status'] = 0;
		$data['ID'] = $id;
		if (D('Users')->save($data)) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}

	function userinfo() {
		if (IS_POST) {
			if (D('Users')->create()) {
				if (false !== D('Users')->save()) {
					// 更新SESSION信息
					session('user', get_user_by_map(array('ID' => I('post.ID'))));
					$this->success("保存成功！");
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error(D('Users')->getError());
			}
		} else {
			$this->assign(session('user'));
			$this->display($this->getLowerTplName('info'));
		}
	}

}
