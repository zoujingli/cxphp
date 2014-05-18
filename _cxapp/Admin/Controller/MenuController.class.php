<?php

namespace Admin\Controller;

/**
 * 菜单管理
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class MenuController extends \Admin\Controller\AdminController {

	protected $Menu;

	function _initialize() {
		parent::_initialize();
		$this->Menu = D("Menu");
	}

	/**
	 *  显示菜单
	 */
	public function index() {
		$result = $this->Menu->order(array("listorder" => "ASC"))->select();
		$tree = new \Common\Lib\Util\Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		foreach ($result as $m) {
			$m['str_manage'] = '<a href="' . U("menu:add", array("parentid" => $m['id'], "menuid" => $_GET['menuid'])) . '">添加子菜单</a> | <a href="' . U("Menu/edit", array("id" => $m['id'], "menuid" => $_GET['menuid'])) . '">修改</a> | <a class="J_ajax_del" href="' . U("Menu/delete", array("id" => $m['id'], "menuid" => I("get.menuid"))) . '">删除</a> ';
			$m['status'] = $m['status'] ? "显示" : "不显示";
			$array[] = $m;
		}
		$tree->init($array);
		$str = "<tr>
					<td align='center'><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input'></td>
					<td align='center'>\$id</td>
					<td >\$spacer\$name</td>
                    <td align='center'>\$status</td>
					<td align='center'>\$str_manage</td>
				</tr>";
		$categorys = $tree->get_tree(0, $str);
		$this->assign("categorys", $categorys);
		$this->display($this->getLowerTplName());
	}

	/**
	 *  添加
	 */
	public function add() {
		if (IS_POST) {
			if ($this->Menu->create()) {
				if ($this->Menu->add()) {
					$this->success("新增成功！", U("menu:index"));
				} else {
					$this->error("新增失败！");
				}
			} else {
				$this->error($this->Menu->getError());
			}
		} else {
			$tree = new \Common\Lib\Util\Tree();
			$parentid = (int) I("get.parentid");
			$result = $this->Menu->select();
			foreach ($result as $r) {
				$r['selected'] = $r['id'] == $parentid ? 'selected' : '';
				$array[] = $r;
			}
			$str = "<option value='\$id' \$selected>\$spacer \$name</option>";
			$tree->init($array);
			$select_categorys = $tree->get_tree(0, $str);
			$this->assign("select_categorys", $select_categorys);
			$this->display($this->getLowerTplName('info'));
		}
	}

	/**
	 *  删除
	 */
	public function delete() {
		$id = (int) I("get.id");
		$count = $this->Menu->where(array("parentid" => $id))->count();
		if ($count > 0) {
			$this->error("该菜单下还有子菜单，无法删除！");
		}
		if ($this->Menu->delete($id)) {
			$this->success("删除菜单成功！");
		} else {
			$this->error("删除失败！");
		}
	}

	/**
	 *  编辑
	 */
	public function edit() {
		if (IS_POST) {
			if ($this->Menu->create()) {
				if ($this->Menu->save() !== false) {
					$this->success("更新成功！", U("Menu/index"));
				} else {
					$this->error("更新失败！");
				}
			} else {
				$this->error($this->Menu->getError());
			}
		} else {
			$tree = new \Common\Lib\Util\Tree();
			$id = (int) I("get.id");
			$rs = $this->Menu->where(array("id" => $id))->find();
			$result = $this->Menu->select();
			foreach ($result as $r) {
				$r['selected'] = $r['id'] == $rs['parentid'] ? 'selected' : '';
				$array[] = $r;
			}
			$str = "<option value='\$id' \$selected>\$spacer \$name</option>";
			$tree->init($array);
			$select_categorys = $tree->get_tree(0, $str);
			$this->assign("data", $rs);
			$this->assign("select_categorys", $select_categorys);
			$this->display($this->getLowerTplName('info'));
		}
	}

	//排序
	public function listorders() {
		$status = parent::listorders($this->Menu);
		if ($status) {
			$this->success("排序更新成功！");
		} else {
			$this->error("排序更新失败！");
		}
	}

}
