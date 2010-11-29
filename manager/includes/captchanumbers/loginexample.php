<?
session_start();
?>
<html>
</html>
<body>
<?
// check login - only code
if (isset($_REQUEST['submit'])) {
	if ($_SESSION['captcha']==$_REQUEST['code']) echo 'login ok';
	else echo 'login failed';
}
else {
?>

<img src="example.php" />
<form action="<? echo $PHP_SELF ?>" method="POST">
Username: <input type="text" name="username" /><br />
Password: <input type="text" name="password" /><br />
Code: <input type="text" name="code" /><br />
<input type="submit" name="submit" value="Login" />
</form>
<? } ?>
</body>
</html>
