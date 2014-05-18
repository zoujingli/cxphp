<?php

namespace Admin\Controller;

/**
 * 幻灯片管理
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class SlideController extends \Admin\Controller\AdminController {

	function index() {
		$cates = array(
			array("cid" => "0", "cat_name" => "默认分类"),
		);
		$categorys = D("SlideCat")->field("cid,cat_name")->where("cat_status!=0")->select();
		$categorys = array_merge($cates, $categorys);
		$this->assign("categorys", $categorys);
		$where = "slide_status!=0";
		if (isset($_POST['cid']) && $_POST['cid'] != "") {
			$cid = $_POST['cid'];
			$this->assign("slide_cid", $cid);
			$where = "slide_status!=0 and slide_cid=$cid";
		}
		$slides = D("Slide")->where($where)->order("listorder ASC")->select();
		$this->assign('slides', $slides);
		$this->display($this->getLowerTplName());
	}

	function add() {
		if (IS_POST) {
			if (D("Slide")->create()) {
				if (D("Slide")->add()) {
					$this->success("添加成功！", U("slide/index"));
				} else {
					$this->error("添加失败！");
				}
			} else {
				$this->error(D("Slide")->getError());
			}
		} else {
			$categorys = D("SlideCat")->field("cid,cat_name")->where("cat_status!=0")->select();
			$this->assign("categorys", $categorys);
			$this->display($this->getLowerTplName('info'));
		}
	}

	function edit() {
		if (IS_POST) {
			if (D("Slide")->create()) {
				if (D("Slide")->save()) {
					$this->success("保存成功！", U("slide/index"));
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error(D("Slide")->getError());
			}
		} else {
			$categorys = D("SlideCat")->field("cid,cat_name")->where("cat_status!=0")->select();
			$id = I("get.id");
			$slide = D("Slide")->where("slide_id=$id")->find();
			$this->assign($slide);
			$this->assign("categorys", $categorys);
			$this->display($this->getLowerTplName('info'));
		}
	}

	function delete() {
		if (isset($_POST['ids'])) {
			$ids = implode(",", $_POST['ids']);
			$data['slide_status'] = 0;
			if (D("Slide")->where("slide_id in ($ids)")->save($data)) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		} else {
			$id = (int) I("get.id");
			$data['slide_status'] = 0;
			$data['slide_id'] = $id;
			if (D("Slide")->save($data)) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}

	//排序
	public function listorders() {
		$status = parent::listorders(D("Slide"));
		if ($status) {
			$this->success("排序更新成功！");
		} else {
			$this->error("排序更新失败！");
		}
	}

}
