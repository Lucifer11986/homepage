<?php session_start(); ?>

<?php
session_start();
$code = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5);
$_SESSION['captcha'] = $code;

header('Content-type: image/png');
$img = imagecreatetruecolor(100, 40);
$bg = imagecolorallocate($img, 30, 30, 30);
$txt = imagecolorallocate($img, 255, 255, 255);
imagefilledrectangle($img, 0, 0, 100, 40, $bg);

// Text mit Standard-GD-Schrift (keine TTF notwendig)
imagestring($img, 5, 15, 10, $code, $txt);

imagepng($img);
imagedestroy($img);
