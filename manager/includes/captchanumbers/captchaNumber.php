<?
$size = isset($_GET['size']) ? $_GET['size'] : 4;
session_start();
if(isset($_GET['sessid'])) session_id($_GET['sessid']);
include('captcha_numbers.php');
$captcha = new CaptchaNumbers($size);
$captcha -> display();
$_SESSION['captchaNumber'] = $captcha -> getString();
?>
