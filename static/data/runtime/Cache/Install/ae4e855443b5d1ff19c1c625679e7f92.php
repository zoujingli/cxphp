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
		<div class="step">
			<ul>
				<li class="current"><em>1</em>检测环境</li>
				<li><em>2</em>创建数据</li>
				<li><em>3</em>完成安装</li>
			</ul>
		</div>
		<div class="server">
			<table width="100%">
				<tr>
					<td class="td1">环境检测</td>
					<td class="td1" width="25%">推荐配置</td>
					<td class="td1" width="25%">当前状态</td>
					<td class="td1" width="25%">最低要求</td>
				</tr>
				<tr>
					<td>操作系统</td>
					<td>Linux</td>
					<td><span class="correct_span">&radic;</span> <?php echo ($os); ?></td>
					<td>不限制</td>
				</tr>
				<tr>
					<td>PHP版本</td>
					<td>>5.4.x</td>
					<td><span class="correct_span">&radic;</span> <?php echo ($phpv); ?></td>
					<td>5.3.0</td>
				</tr>
				<tr>
					<td>Mysql版本（client）</td>
					<td>>5.x.x</td>
					<td><?php echo ($mysql); ?></td>
					<td>4.2</td>
				</tr>
				<tr>
					<td>附件上传</td>
					<td>>2M</td>
					<td><?php echo $uploadSize; ?></td>
					<td>不限制</td>
				</tr>
				<tr>
					<td>session</td>
					<td>开启</td>
					<td><?php echo $session; ?></td>
					<td>开启</td>
				</tr>
			</table>
			<table width="100%">
				<tr>
					<td class="td1">目录、文件权限检查</td>
					<td class="td1" width="25%">写入</td>
					<td class="td1" width="25%">读取</td>
				</tr>
				<?php if(is_array($folder)): $i = 0; $__LIST__ = $folder;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dir): $mod = ($i % 2 );++$i; dir_create($dir); ?>
					<tr>
						<td><?php echo ($dir); ?></td>
						<td>
					<?php if(TestWrite($dir)): ?><span class="correct_span">&radic;</span>可写
						<?php else: ?>
						<span class="correct_span error_span">&radic;</span>不可写 
						<?php $err++; endif; ?>
					</td>
					<td>
					<?php if(is_readable($dir)): ?><span class="correct_span">&radic;</span>可读
						<?php else: ?>
						<span class="correct_span error_span">&radic;</span>不可读
						<?php $err++; endif; ?>
					</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</table>
		</div>
		<div class="bottom tac"> 
			<a href="<?php echo U('install/index/s2');?>" class="btn">重新检测</a>
			<a href="<?php echo U('install/index/s3');?>" class="btn">下一步</a> 
		</div>
	</section>

		</div>
	<div class="footer"> &copy; 2012-<?php echo date('Y');?> <a href="http://www.cxphp.cn" target="_blank">www.cxphp.cn</a> 晨星网络工作室出品</div>
	
</body>
</html>