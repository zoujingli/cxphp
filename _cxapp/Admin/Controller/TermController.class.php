<?php

namespace Admin\Controller;

/**
 * 内容分类管理
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class TermController extends \Admin\Controller\AdminController {

	protected $taxonomys = array("article" => "内容", "picture" => "图片");

	function _initialize() {
		parent::_initialize();
		$this->assign("taxonomys", $this->taxonomys);
	}

	public function _before_index() {
		$tree = new \Common\Lib\Util\Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$result = D("Terms")->order(array("listorder" => "asc"))->select();
		foreach ($result as $r) {
			$r['str_manage'] = '<a href="' . U("term/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("term/edit", array("id" => $r['term_id'])) . '">修改</a> | <a class="J_ajax_del" href="' . U("term/delete", array("id" => $r['term_id'])) . '">删除</a> ';
			$r['visit'] = "<a href='#'>访问</a>";
			$r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
			$r['id'] = $r['term_id'];
			$r['parentid'] = $r['parent'];
			$array[] = $r;
		}
		$tree->init($array);
		$str = "<tr>
				<td align='center'><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input'></td>
				<td align='center'>\$id</td>
				<td >\$spacer\$name</td>
    			<td align='center'>\$taxonomys</td>
				<!--<td align='center'>\$visit</td>-->
				<td align='center'>\$str_manage</td>
				</tr>";
		$taxonomys = $tree->get_tree(0, $str);
		$this->assign("taxonomys", $taxonomys);
	}

	/**
	 * 添加分类记录
	 */
	function add() {
		if (IS_POST) {
			if (D("Terms")->create()) {
				if (D("Terms")->add()) {
					$this->success("新增成功！", U("term/index"));
				} else {
					$this->error("新增失败！");
				}
			} else {
				$this->error(D("Terms")->getError());
			}
		} else {
			$parentid = (int) I("get.parent");
			$tree = new \Common\Lib\Util\PathTree();
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '---';
			$result = D("Terms")->order(array("path" => "asc"))->select();
			$tree->init($result);
			$this->assign("terms", $tree->get_tree());
			$this->assign("parent", $parentid);
			$this->_get_tpls();
			$this->display($this->getLowerTplName('info'));
		}
	}

	function edit() {
		if (IS_POST) {
			if (D("Terms")->create()) {
				if (D("Terms")->save() !== false) {
					$this->success("修改成功！");
				} else {
					$this->error("修改失败！");
				}
			} else {
				$this->error(D("Terms")->getError());
			}
		} else {
			$tree = new \Common\Lib\Util\PathTree();
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '---';
			$result = D("Terms")->order(array("path" => "asc"))->select();
			$tree->init($result);
			$id = (int) I("get.id");
			$data = D("Terms")->where(array("term_id" => $id))->find();
			$this->assign("terms", $tree->get_tree());
			$this->assign("data", $data);
			$this->_get_tpls();
			$this->display($this->getLowerTplName('info'));
		}
	}

	/**
	 * 读取内容模板
	 */
	private function _get_tpls() {
		//读取列表模板
		$list_tpls = get_file_list(C('APP_TPL_PATH') . C('site_tpl') . '/list/');
		$this->assign('list_tpls', $list_tpls);
		//读取内容模板
		$article_tpls_path = C('APP_TPL_PATH') . C('site_tpl') . '/article/';
		$article_tpls = get_file_list($article_tpls_path);
		$this->assign('article_tpls', $article_tpls);
	}

	//排序
	public function listorders() {
		$status = parent::listorders(D("Terms"));
		if ($status) {
			$this->success("排序更新成功！");
		} else {
			$this->error("排序更新失败！");
		}
	}

	/**
	 *  删除
	 */
	public function delete() {
		$id = (int) I("get.id");
		$count = D("Terms")->where(array("parent" => $id))->count();
		if ($count > 0) {
			$this->error("该菜单下还有子类，无法删除！");
		}
		if (D("Terms")->delete($id)) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}

	public function show() {
		$result = D("Terms")->order(array("listorder" => "asc"))->select();
		$tree = new \Common\Lib\Util\Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		foreach ($result as $r) {
			$r['id'] = $r['term_id'];
			$r['parentid'] = $r['parent'];
			$name = $r['name'];
			$url = U('post/lists', array('term' => $r['term_id']));
			$r['name'] = "<a class='term_link' href='$url' >$name</a>";
			$array[$r['term_id']] = $r;
		}
		$str = "<tr>
				<td >\$spacer\$name</td>
				</tr>";
		$tree->init($array);

		$categorys = $tree->get_tree(0, $str);


		$this->assign("categorys", $categorys);
		$this->display();
	}

}
