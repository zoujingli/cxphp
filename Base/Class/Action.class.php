<?php

class Action extends Template{

	function __construct(){
		parent::__construct();
	}

	/**
	 * 静态方法  启用框架的入口方法 
	 */
	public static function run(){
		self::debug();  //启用Debug模块
		self::pathinfo();  //启用pathinfo
		self::initfile();  //检测并初始化系统
		$controler_src = C('APP_PATH') . '/Action/' . $_GET['m'] . 'Action.class.php';
		$runfile = C('RUN_PATH') . '/Action/' . $_GET['m'] . 'Action.class.php';
		if(file_exists($controler_src)){
			self::touch($runfile,strip_whitespace(file_get_contents($controler_src)),C('DEBUG'));
			$m = $_GET['m'] . 'Action';
			$tmp = new $m();
			Debug::addmsg("当前访问的控制器类： $controler_src");
			method_exists($tmp,'init') ? $tmp->init() : '';
			method_exists($tmp,$_GET['a']) ? $tmp->$_GET['a']() : (Debug::addmsg('<font color="red">当前访问的控制器类： ' . $controler_src . ' 不存在 ' . $_GET['a'] . ' 操作</font>'));
		}else{
			Debug::addmsg("<font color='red'>当前访问的控制器类： $controler_src 不存在！</font>");
		}
		if(C('DEBUG')) //DEBGU检测输出
			Debug::message();
	}

	protected function success($msg = '操作成功',$url = null,$timeout = 3,$type = true){
		C('DEBUG',false);
		if(!empty($url)){
			$urls = explode('/',trim($url,'/'));
			if(count($urls) == 1){
				$url = $_SERVER['SCRIPT_NAME'] . '/' . $_GET['m'] . '/' . $urls[0];
			}else{
				$url = $_SERVER['SCRIPT_NAME'] . '/' . join('/',$urls);
			}
		}
		$this->assign('url_s',$url);
		$this->assign('time',$timeout * 1000);
		$this->assign('content',$msg);
		if($type)
			$this->display(C('TPL_SUCCESS'));
		else
			$this->display(C('TPL_ERROR'));
		exit;
	}

	protected function error($msg = '操作失败',$url = null,$timeout = 3){
		$this->success($msg,$url,$timeout,false);
	}

	/**
	 * 开发调试设置 
	 */
	private static function debug(){

		if(C("DEBUG")){ //检查DEBUG设置
			error_reporting(E_ALL ^ E_NOTICE); //输出除了注意的所有错误报告
			set_error_handler(array("Debug",'Catcher')); //设置捕获系统异常
		}else{
			ini_set('display_errors','Off'); //屏蔽错误输出
			ini_set('log_errors','On'); //开启错误日志，将错误报告写入到日志中
			ini_set('error_log',C('RUN_PATH') . '/error.log'); //指定错误日志文件
		}
	}

	/**
	 * 启用pathinfo网址解释
	 */
	private static function pathinfo(){
		if(isset($_SERVER['PATH_INFO'])){
			$pathinfo = explode('/',trim($_SERVER['PATH_INFO'],"/"));
			$_GET['m'] = (!empty($pathinfo[0]) ? $pathinfo[0] : 'Index');
			array_shift($pathinfo);
			$_GET['a'] = (!empty($pathinfo[0]) ? $pathinfo[0] : 'index');
			array_shift($pathinfo);
			for($i = 0; $i < count($pathinfo); $i+=2){
				$_GET[$pathinfo[$i]] = $pathinfo[$i + 1];
			}
		}else{
			$_GET["m"] = (!empty($_GET['m']) ? $_GET['m'] : 'Index');
			$_GET["a"] = (!empty($_GET['a']) ? $_GET['a'] : 'index');
			if($_SERVER["QUERY_STRING"]){
				$m = $_GET["m"];
				unset($_GET["m"]);
				$a = $_GET["a"];
				unset($_GET["a"]);
				$query = http_build_query($_GET); //形成0=foo&1=bar&2=baz&3=boom&cow=milk格式
				$url = $_SERVER["SCRIPT_NAME"] . "/{$m}/{$a}/" . str_replace(array("&","="),"/",$query);
				header("Location:" . $url);
			}
		}
		$_GET['m'] = ucfirst(strtolower($_GET['m']));
	}

	/**
	 * 初始化目录及文件
	 */
	private static function initfile(){
		if(C('DEBUG') || !file_exists(C('RUN_PATH') . '/CXPHP_LOCK_FILE')){
			self::mkdir(require CXPHP . '/Config/init.config.php'); //初始化目录结构
			self::touch(require CXPHP . '/Config/init.file.php'); //创建系统初始PHP文件
		}
	}

	/**
	 * 递归创建目录(支持数组混传)
	 */
	protected static function mkdir(){
		$args = func_get_args();
		foreach($args as $ar){
			if(is_array($ar)){
				foreach($ar as $a){
					self::mkdir($a);
				}
			}else{
				$pdir = dirname($ar);
				if(!(file_exists($pdir) && is_dir($pdir))){
					self::mkdir($pdir);
				}
				if(!(file_exists($ar) && is_dir($ar)) && is_writable($pdir)){
					if(mkdir($ar,'0755'))
						Debug::addmsg('创建目录 ' . $ar . ' 成功');
					else
						Debug::addmsg('<font style="color:red">创建目录 ' . $ar . ' 失败</font>');
				} else if(!is_writable($pdir)){
					Debug::addmsg('上级目录 ' . $pdir . ' 为只读,不能创建下级目录');
				}
			}
		}
	}

	/**
	 * 创建文件(支持数组)
	 * @param type $file        文件名
	 * @param type $content     文件内容
	 * @param type $compel      是否强制生成新文件
	 */
	protected static function touch($file = '',$content = '',$compel = false){
		if(is_array($file)){
			foreach($file as $f => $c){
				self::touch($f,$c);
			}
		}else{
			if(!(file_exists($file) && is_file($file) ) || $compel){
				if(file_put_contents($file,$content)){
					Debug::addmsg('创建文件 ' . $file . ' 成功');
				}else{
					Debug::addmsg('创建文件 ' . $file . ' <b style="color:red">失败</b>');
				}
			}
		}
	}

}