<?php
/* * ******************************************************************
 *
 * 		============== 多文件上传处理类 =================
 * 
 * 		说明：上传的结果可以使用对象下的result方法获得
 * 			由status可判断文件上传是否成功，msg可获得相关信息
 * 		参数：#path 设定文件存放目录 （ 默认存放目录 ./Upload/tmp ）
 * 		参数：#exts 设定允许上传的文件类型( 仅限一维数组小写字母后缀名 )
 * 		参数：#size 设定允许上传的最大值（ 以B为单位，不设置则不限定）
 *
 * 		    作者：邹景立 时间：2012-10-09
 *
 * ***************************************************************** */

class Upload {

	private $path; #文件上传成功后存放的目录
	private $exts; #允许上传的文件类型
	private $size; #允许上传的文件大小
	private $result; #装载文件上传后的结果集
	static $msg = array(#预定义消息类型
		0 => '文件上传成功',
		1 => '上传的文件超过了配置PHP配置的文件大小',
		2 => '上传的文件超过了HTML表单指定的文件大小',
		3 => '文件在上传过程中损坏',
		4 => '没有文件上传',
		5 => '不支持的上传格式',
		6 => '找不到临时文件夹',
		7 => '文件写入失败',
		8 => '上传的文件超过了限定的大小'
	);

	/* 构造方法 */

	public function __construct($path = '', $exts = '', $size = 0) {
		$this->path = empty($path) ? C('PRO_PATH') . "/Upload/tmp" : $path;
		$this->exts = $exts;
		$this->size = $size;
		$this->init();
	}

	/* 初始操作 */

	private function init() {
		foreach ($_FILES as $file) {
			foreach ($file['name'] as $key => $name) {

				/* 文件类型检查 */
				if (!empty($this->exts) && $file['error'][$key] == 0 && !in_array($this->getExt($name), $this->exts)) {
					$file['error'][$key] = 5; #文件类型不支持上传
				}

				/* 开始做文件上传 */
				if ($file['error'][$key] == 0) {
					/* 文件大小超出检查 */
					if (!empty($this->size) && $file['size'][$key] > $this->size) {
						$file['error'][$key] = 8;
					} else {
						/*  开始处理上传的文件 */
						$newname = $this->getName($name);
						$this->doDir(); #检查目录
						$mvpath = realpath($this->path) . '/' . $newname;
						if (!move_uploaded_file($file['tmp_name'][$key], $mvpath)) {
							$file['error'][$key] = 7;
						}
					}
				}

				/* 装载结果 */
				$this->result[] = array(
					'name' => $file['name'][$key],
					'filename' => @$newname,
					'path' => $this->path,
					'size' => $file['size'][$key],
					'ext' => $this->getExt($name),
					'status' => $file['error'][$key],
					'msg' => self::$msg[$file['error'][$key]]
				);
				unset($newname);
			}
		}
	}

	/* 返回结果集 ( 二维数组 ) */

	public function result() {
		return $this->result;
	}

	/* 得到文件的类型（通过文件后缀） */

	private function getExt($name) {
		return strtolower(pathinfo($name, PATHINFO_EXTENSION));
	}

	/* 生成文件名 */

	private function getName($name) {
		return date('Ymd-His') . '-' . rand(1000, 9999) . '.' . $this->getExt($name);
	}

	/* 目录处理(递归) 注意在linux中的权限 */

	private function doDir($path = '') {
		$path = empty($path) ? $this->path : $path;
		if (!empty($path) && (!(file_exists($path) && is_dir($path)))) {
			$ppath = dirname($path);
			if (!(file_exists($ppath) && is_dir($ppath))) {
				$this->doDir($ppath);
			}
			if (is_writable($ppath))
				mkdir($path);
			else
				echo '<p>目录：' . $ppath . ' 没有写权限</p>';
		}
	}

}