<?php

namespace Admin\Controller;

/**
 * 前台导航管理
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class NavController extends \Admin\Controller\AdminController {

	/**
	 *  显示菜单
	 */
	public function _before_index() {
		if (empty($_REQUEST['cid'])) {
			$cid = M("NavCat")->getField('navcid');
		} else {
			$cid = $_REQUEST['cid'];
		}
		$map = array();
		$map['cid'] = $cid;
		$str = "<tr>
				<td><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input'></td>
				<td>\$id</td>
				<td >\$spacer\$label</td>
			    <td>\$status</td>
				<td>\$str_manage</td>
			</tr>";
		$this->_data_assign($map, $str);
	}

	/**
	 *  添加数据前置处理
	 */
	public function _before_add() {
		if (!IS_POST) {
			$map = array();
			$map['cid'] = I('get.cid');
			$map['id'] = I('get.parentid');
			$this->_data_assign($map);
		}
	}

	/**
	 * 添加数据成功后置处理
	 * @param type $id
	 */
	protected function _add_success($id) {
		if (!!I('post.parentid')) {
			$data['path'] = "0-{$id}";
		} else {
			$parent = M('Nav')->where(array('id' => I('post.parentid')))->find();
			$data['path'] = "{$parent['path']}-{$id}";
		}
		$data['id'] = $id;
		M('Nav')->save($data);
	}

	/**
	 * 编辑数据前置处理
	 */
	public function _before_edit() {
		if (IS_POST) {
			$parentid = empty($_POST['parentid']) ? "0" : $_POST['parentid'];
			if (empty($parentid)) {
				$_POST['path'] = "0-{$_POST['id']}";
			} else {
				$map = array();
				$map['id'] = $parentid;
				$parent = M('Nav')->where($map)->find();
				$_POST['path'] = "{$parent['path']}-{$_POST['id']}";
			}
		} else {
			$this->_data_assign(array('cid' => I('get.cid'), 'id' => I('get.id')));
		}
	}

	/**
	 * 数据初始化方法
	 * @param type $map
	 * @param type $str
	 */
	private function _data_assign($map = array(), $str = "<option value='\$id' \$selected>\$spacer\$label</option>") {
		$result = M('Nav')->where($map)->order(array("listorder" => "ASC"))->select();
		foreach ($result as $nav) {
			$nav['str_manage'] = '<a href="' . U("nav:add", array("parentid" => $nav['id'])) . '">添加子菜单</a> |<a href="' . U("nav:edit", array("id" => $nav['id'])) . '">修改</a> | <a class="J_ajax_del" href="' . U("nav:delete", array("id" => $nav['id'])) . '">删除</a> ';
			$nav['status'] = $nav['status'] ? "显示" : "不显示";
			$nav['selected'] = $nav ['id'] == I("get.parentid") ? "selected" : "";
			$array[] = $nav;
		}
		$tree = new \Common\Lib\Util\Tree();
		$tree->icon = array('&nbsp;│ ', '&nbsp;├─ ', '&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$tree->init($array);
		$this->assign("nav_trees", $tree->get_tree(0, $str));
		$cats = M("NavCat")->select();
		$this->assign("navcats", $cats);
		$this->assign("navcid", I('get.cid'));
	}

	/**
	 *  删除
	 */
	public function delete() {
		$id = (int) I("get.id");
		$count = M('Nav')->where(array("parentid" => $id))->count();
		if ($count > 0) {
			$this->error("该菜单下还有子菜单，无法删除！");
		}
		if (M('Nav')->delete($id)) {
			$this->success("删除菜单成功！");
		} else {
			$this->error("删除失败！");
		}
	}

}
