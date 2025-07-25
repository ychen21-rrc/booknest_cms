<?php
session_start();

// 生成随机验证码文本
$code = strval(rand(1000, 9999));
$_SESSION['captcha'] = $code;

// 创建图片
$width = 100;
$height = 40;

header('Content-Type: image/png');

$image = imagecreatetruecolor($width, $height);
$bg = imagecolorallocate($image, 255, 255, 255); // 背景白色
$textColor = imagecolorallocate($image, 0, 0, 0); // 黑色文字
$border = imagecolorallocate($image, 200, 200, 200); // 边框

imagefilledrectangle($image, 0, 0, $width, $height, $bg); // 填充背景
imagerectangle($image, 0, 0, $width - 1, $height - 1, $border); // 加边框
imagestring($image, 5, 25, 10, $code, $textColor); // 写验证码

imagepng($image); // 输出图像
imagedestroy($image); // 释放内存
?>
