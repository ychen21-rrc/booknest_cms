
<?php
session_start();
$code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
$_SESSION['captcha'] = $code;

header("Content-type: image/png");
$img = imagecreate(120, 40);
$bg = imagecolorallocate($img, 255, 255, 255);
$txt = imagecolorallocate($img, 0, 0, 0);
$line = imagecolorallocate($img, 100, 100, 100);

for ($i = 0; $i < 4; $i++) {
    imageline($img, rand(0,120), rand(0,40), rand(0,120), rand(0,40), $line);
}

imagestring($img, 5, 20, 10, $code, $txt);
imagepng($img);
imagedestroy($img);
?>
