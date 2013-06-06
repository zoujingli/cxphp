<?php

class Template extends Smarty{

	protected $id = null;

	function __construct(){
		$this->id = md5($_SERVER['REQUEST_URI']); //建立页面的ID

		$this->left_delimiter = C('TPL_L_TAG');  //模板文件中使用的“左”分隔符号
		$this->right_delimiter = C('TPL_R_TAG');  //模板文件中使用的“右”分隔符号
		$this->template_dir = C('TPL_DIR');   //模板目录
		$this->compile_dir = C('TPL_COMPILE_DIR');  //里的文件是自动生成的，合成的文件
		$this->caching = C('TPL_CACHEING');   //设置缓存开启
		$this->cache_dir = C('TPL_CACHE_DIR');   //设置缓存的目录
		$this->cache_lifetime = C('TPL_CACHE_TIME'); //设置缓存的时间 
		$this->debugging = false;

		parent::__construct();
	}

	function display($resource_name = null,$cache_id = null,$compile_id = null){
		if(is_null($resource_name)){
			$resource_name = "{$_GET["m"]}/{$_GET["a"]}" . C('TPL_SUFFIX');
		}else if(strstr($resource_name,"/")){
			$resource_name = $resource_name . C('TPL_SUFFIX');
		}else{
			$resource_name = $_GET["m"] . "/" . $resource_name . C('TPL_SUFFIX');
		}
		$tplpath = rtrim(C('TPL_DIR'),'/') . '/' . $resource_name;
		if(!file_exists($tplpath)){
			if(C('DEBUG'))
				Debug::addmsg("<font style='color:red'>当前访问的模板文件：  $tplpath 不存在</font>");
			else
				$this->error('抱歉, 访问的页面不存在！');
		}else{
			if(C('DEBUG')){
				Debug::addmsg("当前访问的模板文件： $tplpath");
			}
			//预定义目录
			$root = rtrim(substr(C('PRO_PATH'),strlen(rtrim($_SERVER["DOCUMENT_ROOT"],"/\\"))),'/\\');
			$resource = rtrim(dirname($_SERVER["SCRIPT_NAME"]),"/\\") . '/' . ltrim(C('APP_PATH'),'./') . "/View/" . C('TPL_STYLE') . "/Resource/";
			$url = $_SERVER['SCRIPT_NAME'] . '/' . $_GET['m'];
			$this->assign('root',$root);
			$this->assign('public',$root . '/Public');
			$this->assign('res',$resource);
			$this->assign('url',$url);
			parent::display($resource_name,$cache_id,$compile_id);
		}
	}

}
