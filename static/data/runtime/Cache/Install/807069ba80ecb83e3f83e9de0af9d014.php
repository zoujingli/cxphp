<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title><?php echo ($title); ?> - <?php echo ($step); ?></title>
		<link rel="stylesheet" href="/Cxphp.cn/static/install/css/install.css" />
	</head>
	<body>
		<div class="wrap">
			<div class="header">
    <h1 class="logo">logo</h1>
    <div class="icon_install">安装向导</div>
    <div class="version"></div>
</div>
			
	<section class="section">
		<div class="">
			<div class="success_tip cc">
				<span class='f16 b'>安装完成，</span>
				【<a href="<?php echo U('home/index/index');?>" class="f16 b">进入网站首页</a>】
				【<a href="<?php echo U('admin/public/login');?>" class="f16 b">进入后台管理</a>】
			</div>
			<div class=""> </div>
		</div>
	</section>

		</div>
	<div class="footer"> &copy; 2012-<?php echo date('Y');?> <a href="http://www.cxphp.cn" target="_blank">www.cxphp.cn</a> 晨星网络工作室出品</div>
	
</body>
</html>