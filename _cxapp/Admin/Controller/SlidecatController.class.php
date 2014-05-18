<?php

namespace Admin\Controller;

/**
 * 幻灯片分类管理
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class SlidecatController extends \Admin\Controller\AdminController {

	function index() {
		$cats = D("SlideCat")->where("cat_status!=0")->select();
		$this->assign("slidecats", $cats);
		$this->display($this->getLowerTplName());
	}

	/**
	 *  添加
	 */
	public function add() {
		if (IS_POST) {
			if (D("SlideCat")->create()) {
				if (D("SlideCat")->add()) {
					$this->success("添加成功！", U("slidecat/index"));
				} else {
					$this->error("添加失败！");
				}
			} else {
				$this->error(D("SlideCat")->getError());
			}
		} else {
			$this->display($this->getLowerTplName('info'));
		}
	}

	function edit() {
		if (IS_POST) {
			if (D("SlideCat")->create()) {
				if (FALSE !== D("SlideCat")->save()) {
					$this->success("保存成功！", U("slidecat/index"));
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error(D("SlideCat")->getError());
			}
		} else {
			$id = I("get.id");
			$slidecat = D("SlideCat")->where("cid=$id")->find();
			$this->assign($slidecat);
			$this->display($this->getLowerTplName('info'));
		}
	}

}
