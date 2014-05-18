<?php

namespace Admin\Controller;

/**
 * 系统后台管理公共控制器类
 * 
 * @author Anyon <cxphp@qq.com>
 * @date 2014/04/06 20:08
 */
class AdminController extends \Think\Controller {

	const OP_TYPE_SUCCESS = 'success';
	const OP_TYPE_ERROR = 'error';
	const OP_TYPE_FILTER = 'filter';

	/**
	 * 初始化调用方法
	 */
	public function _initialize() {
		$this->assign("js_debug", APP_DEBUG ? "?v=" . time() : "");
		if (!!is_login()) {
			$user = session('user');
			if (!checkAuth($user['role_id'])) {
				$this->error("您没有访问权限！");
				exit();
			}
			$this->assign("admin", $user);
		} else {
			$this->redirect('admin/public/login');
		}
		$this->initMenu();
	}

	/**
	 * 显示模块首页
	 * @param type $_model 数据模型
	 * @param type $_tpl 显示模板文件
	 * @param type $map 数据查询条件
	 */
	public function index($_model = null, $_tpl = null, $map = array()) {
		is_null($_model) && $_model = D(CONTROLLER_NAME);
		/* 列表过滤器，生成查询Map对象 */
		$map = $this->_search($_model, $map);
		/* 条件过滤 */
		$this->callback($map, ACTION_NAME, self::OP_TYPE_FILTER);
		/* 列表处理 */
		$this->_list($_model, $map);
		/* 显示页面 */
		is_null($_tpl) && $_tpl = $this->getLowerTplName();
		$this->display($_tpl);
	}

	/**
	 * 搜索条件拼接
	 * @param type $_model 数据模型
	 * @param type $map 查询过滤条件
	 * @return array
	 */
	protected function _search($_model, $map = array()) {
		/* 去除搜索内容两端的空格 */
		foreach ($_REQUEST as &$param) {
			if (is_string($param)) {
				$param = trim($param);
			}
		}
		/* 自定义 _fields 指定要查找的字段 */
		$dbFields = $_model->getDbFields();
		/* 指定查询的字段 */
		if (!empty($_REQUEST['_fields'])) {
			$dbFields = array_intersect($dbFields, trimArrayKeyLeft(explode(',', deCode($_REQUEST['_fields']))));
		}
		/* 关键字模糊查询 */
		if (isset($_REQUEST['_keywords']) && $_REQUEST['_keywords'] != '') {
			$map[join($dbFields, '|')] = array('like', "%{$_REQUEST['_keywords']}%");
		}
		return $map;
	}

	/**
	 * 生成数据列表，回调过方法
	 * @param type $_model 数据列表来源模型
	 * @param type $map 查询条件规则
	 * @param type $firstRow 列表分页始记录数
	 * @param type $listRow 列表分页页数
	 * @param type $listRows 列表分页每页面显示记录数
	 * @param type $order 列表排序字段
	 * @param type $sort  列表排序方式
	 */
	protected function _list($_model, $map = array(), $firstRow = 0, $listRow = 2, $listRows = 1, $order = null, $sort = 'desc') {
		/* 查询统计条数 */
		$count = $_model->where($map)->count();
		/* 数据分页 */
		$page = new \Think\Page($count, $listRows);
		$page->firstRow = $firstRow;
		$page->listRows = $listRow * $listRows;
		$limit = $page->firstRow . ',' . $page->listRows;

		is_null($order) && $order = $_model->getPk();

		/* 查询数据列表 */
		$voList = $_model->where($map)->order($order . ' ' . $sort)->limit($limit)->select();
//		die(M()->_sql());
		$this->assign("page", $page->show());
		$this->assign('list', $voList);
	}

	/**
	 * 编辑数据页面显示
	 * @param type $_model 数据模模型
	 * @param type $id 数据主键ID
	 * @param type $_tpl 显示的模板
	 * @author Anyon <cxphp@qq.com>
	 */
	public function edit($_model = null, $id = null, $_tpl = null) {
		is_null($_model) && $_model = D(CONTROLLER_NAME);
		if (IS_POST) {
			$this->callback($_POST, ACTION_NAME, self::OP_TYPE_FILTER);
			if (false === $_model->create()) {
				$this->error($_model->getError());
			}
			$result = $_model->save();
			if (false !== $result) {
				$this->callback($result, ACTION_NAME, self::OP_TYPE_SUCCESS);
				$this->success("编辑成功", U(CONTROLLER_NAME . ':index'));
			} else {
				$this->callback($result, ACTION_NAME, self::OP_TYPE_ERROR);
				$this->error("编辑失败，请稍候再试~");
			}
		} else {
			is_null($id) && $id = I("get.{$_model->getPk()}");
			$vo = $_model->where(array($_model->getPk() => $id))->find();
			if (!empty($vo)) {
				$this->callback($vo, ACTION_NAME, self::OP_TYPE_FILTER);
				$this->assign('vo', $vo);
				is_null($_tpl) && $_tpl = $this->getLowerTplName('info');
				$this->display($_tpl);
			} else {
				E('系统无法定位要编辑的数据');
			}
		}
	}

	/**
	 *  排序 排序字段为listorders数组 POST 排序字段为：listorder
	 */
	public function order($_model = null, $field = 'listorder') {
		is_null($_model) && $_model = D(CONTROLLER_NAME);
		$ids = I("post.{$field}");
		$_model->startTrans();
		$is_rol = false;
		foreach ($ids as $key => $r) {
			$data[$field] = $r;
			$result = $_model->where(array($_model->getPk() => $key))->save($data);
			if ($result === false) {
				$is_rol = true;
			}
		}
		if (!$is_rol && $_model->commit()) {
			$this->success("排序更新成功！");
		} else {
			$_model->rollback();
			$this->error("排序更新失败！");
		}
	}

	/**
	 * 数据添加操作
	 * @param type $_model 数据模型
	 * @param type $_tpl 编辑时显示的模板
	 */
	public function add($_model = null, $_tpl = null) {
		/* 如果POST提交数据过来表示需要添加操作 */
		if (IS_POST) {
			$this->callback($_POST, ACTION_NAME, self::OP_TYPE_FILTER);
			/* 创建数据前的回调过滤机制 _add_filter */
			is_null($_model) && $_model = D(CONTROLLER_NAME);
			if (false === $_model->create()) {
				$this->error($_model->getError());
			}
			$result = $_model->add();
			if (false !== $result) {
				/* 创建数据成功后的回调过滤机制 _add_success */
				$this->callback($result, ACTION_NAME, self::OP_TYPE_SUCCESS);
				$this->success("添加成功", U(CONTROLLER_NAME . ':index'));
			} else {
				/* 创建数据失败后的回调过滤机制 _add_error */
				$this->callback($result, ACTION_NAME, self::OP_TYPE_ERROR);
				$this->error("添加失败，请稍候再试~");
			}
		} else {
			/* 显示添加数据的页面 */
			is_null($_tpl) && $_tpl = $this->getLowerTplName('info');
			$this->display($_tpl);
		}
	}

	/**
	 * 操作后的回调机制
	 * @param type $data 回调传的数据
	 * @param type $action 回调执行的方法
	 * @param type $op_type 操作结果类型
	 * @return type
	 */
	protected function callback(&$data = null, $action = ACTION_NAME, $op_type = self::OP_TYPE_SUCCESS) {
		$method = "_{$action}_{$op_type}";
		if (method_exists($this, $method)) {
			return $this->$method($data);
		}
	}

	/**
	 * 获取显示模板的名字
	 * @param type $name 文件名
	 * @param type $module 模块名
	 * @return type
	 */
	protected function getLowerTplName($name = ACTION_NAME, $module = CONTROLLER_NAME) {
		return strtolower("{$module}:{$name}");
	}

	/**
	 * 标识删除操作
	 * @param type $_model 数据模型
	 * @param type $id 数据主键ID，支持逗号分隔
	 * @param type $field 更新标志的数据库字段
	 * @author Anyon <cxphp@qq.com>
	 */
	public function delete($_model = null, $id = null, $field = 'is_deleted') {
		is_null($_model) && $_model = D(CONTROLLER_NAME);
		is_null($id) && $id = I("request.{$_model->getPk()}");
		$map = array($_model->getPk() => array('in', explode(',', $id)));
		$this->callback($map, ACTION_NAME, self::OP_TYPE_FILTER);
		$result = $_model->where($map)->save(array($field => 1));
		if (false !== $result) {
			$this->callback($result, ACTION_NAME, self::OP_TYPE_SUCCESS);
			$this->success('删除成功');
		} else {
			$this->callback($result, ACTION_NAME, self::OP_TYPE_ERROR);
			$this->error('删除失败，请稍候再试~');
		}
	}

	/**
	 * 物理删除数据
	 * @param type $_model 数据模型
	 * @param type $id 数据主键ID
	 * @author Anyon <cxphp@qq.com>
	 */
	public function clean($_model = null, $id = null) {
		is_null($_model) && $_model = D(CONTROLLER_NAME);
		is_null($id) && $id = I("get.{$_model->getPk()}");
		$map = array("{$_model->getPk()}" => array('in', explode(',', $id)));
		$this->callback($map, ACTION_NAME, self::OP_TYPE_FILTER);
		$result = $_model->where($map)->delete();
		if (false !== $result) {
			$this->callback($result, ACTION_NAME, self::OP_TYPE_SUCCESS);
			$this->success('删除成功');
		} else {
			$this->callback($result, ACTION_NAME, self::OP_TYPE_ERROR);
			$this->error('删除失败，请稍候再试~');
		}
	}

	/**
	 * 初始化后台菜单
	 */
	public function initMenu() {
		$Menu = F("Menu");
		if (!$Menu) {
			$model = new \Admin\Model\MenuModel();
			$model->menu_cache();
		}
	}

	/**
	 * 获取菜单导航
	 * @param type $app
	 * @param type $model
	 * @param type $action
	 */
	public static function getMenu() {
		$menuid = (int) I('get.menuid');
		$menuid = $menuid ? $menuid : cookie("menuid", "", array("prefix" => ""));
		//cookie("menuid",$menuid);
		$db = D("Menu");
		$info = $db->cache(true, 60)->where(array("id" => $menuid))->getField("id,action,app,model,parentid,data,type,name");
		$find = $db->cache(true, 60)->where(array("parentid" => $menuid, "status" => 1))->getField("id,action,app,model,parentid,data,type,name");
		if ($find) {
			array_unshift($find, $info[$menuid]);
		} else {
			$find = $info;
		}
		foreach ($find as $k => $v) {
			$find[$k]['data'] = $find[$k]['data'] . "&menuid=$menuid";
		}
		return $find;
	}

	/**
	 * 当前位置
	 * @param $id 菜单id
	 */
	final public static function current_pos($id) {
		$menudb = M("Menu");
		$r = $menudb->where(array('id' => $id))->find();
		$str = '';
		if ($r['parentid']) {
			$str = self::current_pos($r['parentid']);
		}
		return $str . $r['name'] . ' > ';
	}

}
