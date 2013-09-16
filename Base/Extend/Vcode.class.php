<?php

/** ========================================================
 * 文件 code.class.php
 * 功能：生成验证码
 * 实例：$code=new Code();
 *      $code->out();
 * 参数：#width     生成图片的宽度
 * 参数：#height    生成图片的高度
 * 参数：#imgType   生成图片输出的类型
 * 参数：#type      验证码字符类型 1:为纯数字、2:小写字母、3:大写字母、其它:混合
 * 参数：#num       验证码字符长度
 * 作者：邹景立
 * 时间：2012-10-01
  =========================================================== */
class Vcode {

	//声明内部变量
	#width          图片的宽度
	#height         图片的高度
	#outType        图片输出的类型
	#type           字符类型     1:为纯数字、2:小写字母、3:大写字母、其它:混合
	#length         字符长度
	#code           生成的code值
	private $width, $height, $outType, $type, $length, $code;

	//构造方法
	function __construct($width = 80, $height = 20, $imgType = 'jpeg', $type = 3, $num = 4) {
		$this->width = $width;
		$this->height = $height;
		$this->outType = $imgType;
		$this->type = $type;
		$this->length = $num;
	}

	//随机生成字符
	private function getCode() {
		$str1 = '0123456789';
		$str2 = 'abcdefghijklmnopqrsuvwxyz';
		$str3 = 'ABCDEFGHIJKLMNOPQRSUVWXYZ';
		$str = '';
		switch ($this->type) {
			case 1://数字
				for ($i = 0; $i < $this->length; $i++) {
					$str.=$str1[rand(0, strlen($str1) - 1)];
				}
				break;
			case 2:#小字字母
				for ($i = 0; $i < $this->length; $i++) {
					$str.=$str2[rand(0, strlen($str2) - 1)];
				}
				break;
			case 3:#大字字母
				for ($i = 0; $i < $this->length; $i++) {
					$str.=$str3[rand(0, strlen($str3) - 1)];
				}
				break;
			default:#复杂
				$strs = $str1 . $str2 . $str3;
				for ($i = 0; $i < $this->length; $i++) {
					$str.=$strs[rand(0, strlen($strs) - 1)];
				}
				break;
		}
		return $this->code = $str;
	}

	//生成图片资源
	private function getImg() {
		//创建画板
		$img = imagecreatetruecolor($this->width, $this->height);
		//背景颜色
		$c0 = imagecolorallocate($img, 255, 255, 255);
		//文字颜色
		$c1 = imagecolorallocate($img, 50, 100, 150);
		$c2 = imagecolorallocate($img, 50, 150, 100);
		$c3 = imagecolorallocate($img, 150, 50, 100);
		//干扰颜色
		$d1 = imagecolorallocate($img, 255, 220, 220);
		$d2 = imagecolorallocate($img, 220, 220, 255);
		$d3 = imagecolorallocate($img, 220, 255, 220);
		//填允背景
		imagefill($img, 0, 0, $c0);
		//设定字体
		$font = dirname(__FILE__) . PATH_SEPARATOR . "font.ttf";
		//取得字符
		$code = $this->getCode();
		//将字符写入图片
		for ($i = 0; $i < $this->length; $i++) {
			$color = "c" . rand(1, 3);
			$char = $code[$i];
			//如果字体文件不存在，则将字符直接写入画板
			if (!is_file($font))
				imagestring($img, 5, ($this->width / $this->length) * $i + 2, 3, $char, $$color);
			else
				imagettftext($img, $this->height / $this->length * 2, 0, rand($this->width / $this->length * $i, $this->width / $this->length * ($i + 0.3)), rand($this->height / 2, $this->height / 1.2), $$color, $font, $char);
		}
		//设置session变量(需要启用SESSION)
		@$_SESSION["vcode"] = $code;
		//绘制干扰点
		for ($i = 0; $i < 100; $i++) {
			$color = "d" . rand(1, 3);
			imagesetpixel($img, rand(0, $this->width), rand(0, $this->height), $$color);
		}
		//绘制干扰线
		for ($i = 0; $i < 3; $i++) {
			$color = "d" . rand(1, 3);
			imageline($img, rand(0, $this->width / 3), rand(0, $this->height), rand(0, $this->width), rand(0, $this->height), $$color);
		}
		return $img;
	}

	//输出图片资源
	function out() {
		$img = $this->getImg();
		$imgtype = 'image' . $this->outType;
		$imgtype($img);
		imagedestroy($img);
	}

	function __toString() {
		$this->out();
	}

}

