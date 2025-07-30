<?php
session_start();

$code = strval(rand(1000, 9999));
$_SESSION['captcha'] = $code;

$width = 100;
$height = 40;

header('Content-Type: image/png');

$image = imagecreatetruecolor($width, $height);
$bg = imagecolorallocate($image, 255, 255, 255);
$textColor = imagecolorallocate($image, 0, 0, 0); 
$border = imagecolorallocate($image, 200, 200, 200);
$lineColor = imagecolorallocate($image, 150, 150, 150);

imagefilledrectangle($image, 0, 0, $width, $height, $bg);
imagerectangle($image, 0, 0, $width - 1, $height - 1, $border);

$x1 = rand(0, $width / 2);
$y1 = rand(0, $height);
$x2 = rand($width / 2, $width);
$y2 = rand(0, $height);
imageline($image, $x1, $y1, $x2, $y2, $lineColor);

$x1 = rand(0, $width / 2);
$y1 = rand(0, $height);
$x2 = rand($width / 2, $width);
$y2 = rand(0, $height);
imageline($image, $x1, $y1, $x2, $y2, $lineColor);

imagestring($image, 5, 25, 10, $code, $textColor);

imagepng($image);
imagedestroy($image);
?>