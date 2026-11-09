<?php

$size = isset($_GET['size']) ? intval($_GET['size']) : 192;
$im = imagecreatetruecolor($size, $size);
$bg = imagecolorallocate($im, 23, 107, 82);
imagefill($im, 0, 0, $bg);
$text = imagecolorallocate($im, 255, 255, 255);
imagestring($im, 5, $size / 2 - 20, $size / 2 - 10, 'K', $text);
imagepng($im, "C:/laragon/www/konsultasi layanan/public/icon-{$size}.png");
imagedestroy($im);
echo "icon-{$size}.png created";
