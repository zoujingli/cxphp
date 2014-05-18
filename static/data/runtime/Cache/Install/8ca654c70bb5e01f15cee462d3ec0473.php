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
				<li class="on"><em>1</em>检测环境</li>
				<li class="on"><em>2</em>创建数据</li>
				<li class="current"><em>3</em>完成安装</li>
			</ul>
		</div>
		<div class="install" id="log">
			<ul id="loginner">
			</ul>
		</div>
		<div class="bottom tac"> <a href="javascript:;" class="btn_old"><img src="/Cxphp.cn/static/install/images/loading.gif" align="absmiddle" />&nbsp;正在安装...</a> </div>
	</section>

		</div>
	<div class="footer"> &copy; 2012-<?php echo date('Y');?> <a href="http://www.cxphp.cn" target="_blank">www.cxphp.cn</a> 晨星网络工作室出品</div>
	
	<script src="/Cxphp.cn/static/install/js/jquery.js"></script>
	<script type="text/javascript">
		var n = 0;
		var data = eval('(<?php echo json_encode($_POST);?>)');
		$.ajaxSetup({cache: false});
		function reloads(n) {
			var url = "<?php echo U('install/index/s4');?>?install=1&n=" + n;
			$.ajax({
				type: "POST",
				url: url,
				data: data,
				dataType: 'json',
				beforeSend: function() {

				},
				success: function(msg) {
					if (msg.n == '999999') {
						$('#dosubmit').attr("disabled", false);
						$('#dosubmit').removeAttr("disabled");
						$('#dosubmit').removeClass("nonext");
						setTimeout('gonext()', 2000);
					}
					if (msg.n) {
						$('#loginner').append(msg.msg);
						reloads(msg.n);
					} else {
						//alert('指定的数据库不存在，系统也无法创建，请先通过其他方式建立好数据库！');
						alert(msg.msg);
					}
				}
			});
		}
		function gonext() {
			window.location.href = '<?php echo U("install/index/s5");?>';
		}
		$(document).ready(function() {
			reloads(n);
		})
	</script> 

</body>
</html>