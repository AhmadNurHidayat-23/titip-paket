<?php

$img = imagecreatetruecolor(640, 480);
$bg = imagecolorallocate($img, 231, 232, 221);
$box = imagecolorallocate($img, 175, 133, 83);
$edge = imagecolorallocate($img, 112, 85, 56);
$tape = imagecolorallocate($img, 220, 191, 134);
$label = imagecolorallocate($img, 250, 249, 237);
imagefill($img, 0, 0, $bg);
imagefilledrectangle($img, 110, 100, 530, 370, $box);
imagerectangle($img, 110, 100, 530, 370, $edge);
imagefilledrectangle($img, 290, 100, 350, 370, $tape);
imagefilledrectangle($img, 145, 200, 280, 280, $label);
imagestring($img, 4, 155, 220, 'PAKET UJI', $edge);
if (! is_dir(__DIR__.'/fixtures')) {
    mkdir(__DIR__.'/fixtures');
}imagejpeg($img, __DIR__.'/fixtures/package.jpg', 90);
imagedestroy($img);
