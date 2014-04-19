<?php
define('PAGENAME', 'code');

require_once('./funcs/common.php');

session_start();
ob_clean();
$length=4;
$mode=1;
$type='png';
$width=70;
$height=28;
$verifyName='code';
$str = '';
$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
for($i=0;$i<$length;$i++){
	$str.= mb_substr($chars, floor(mt_rand(0,mb_strlen($chars)-1)),1);
}
$randval = $str;
set_session($verifyName, md5(strtolower($randval)));
$width = ($length*10+10)>$width?$length*10+10:$width;
if ( $type!='gif' && function_exists('imagecreatetruecolor')) {
	$im = @imagecreatetruecolor($width,$height);
}else {
	$im = @imagecreate($width,$height);
}
$r = Array(225,255,255,223);
$g = Array(225,236,237,255);
$b = Array(225,236,166,125);
$key = mt_rand(0,3);

$backColor = imagecolorallocate($im, $r[$key],$g[$key],$b[$key]);    //背景色（随机）
$borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
$pointColor = imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));                 //点颜色

@imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
@imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
$stringColor = imagecolorallocate($im,mt_rand(0,200),mt_rand(0,120),mt_rand(0,120));
// 干扰
for($i=0;$i<10;$i++){
	$fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
	imagearc($im,mt_rand(-10,$width),mt_rand(-10,$height),mt_rand(30,300),mt_rand(20,200),55,44,$fontcolor);
}
for($i=0;$i<25;$i++){
	$fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
	imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$pointColor);
}
for($i=0;$i<$length;$i++) {
	imagestring($im,5,$i*10+5,mt_rand(1,8),$randval{$i}, $stringColor);
}
//@imagestring($im, 5, 5, 3, $randval, $stringColor);
#Image::output($im,$type);
header("Content-type: image/".$type);
$ImageFun='image'.$type;
if(empty($filename)) {
	$ImageFun($im);
}else{
	$ImageFun($im,$filename);
}
imagedestroy($im);
