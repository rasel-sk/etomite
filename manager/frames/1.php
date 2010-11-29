<?php
// Generates the manager page frameset
if(IN_ETOMITE_SYSTEM!="true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}
include_once("includes/browsercheck.inc.php");
$browser = $client->property('browser');
$_SESSION['browser']=$browser;

if(!isset($manager_layout) || $manager_layout==1 || $browser!='ie') {
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<link rel="stylesheet" type="text/css" href="media/style/style.css" />
<html>
<head>
<title><?php echo $site_name." - (Etomite site manager ".$version.")"; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $etomite_charset; ?>">
</head>
<frameset rows="0,25,*" frameborder="NO" border="0">
  <frame src="index.php?a=1&f=5" name="scripter" scrolling="NO" noresize>
  <frame src="index.php?a=1&f=12" name="topFrame"  scrolling="NO" noresize>
  <frameset cols="280,*" border="<?php echo ($browser=='mz' || $browser=='fb') ? 6 : 0 ;?>" frameborder="1" FRAMESPACING="<?php echo ($browser=='mz' || $browser=='fb') ? 1 : 6 ;?>" bordercolor="#4791C5">
    <frameset rows="290,*" name="menuFrame" border="<?php echo ($browser=='mz' || $browser=='fb') ? 6 : 0 ;?>" frameborder="1" FRAMESPACING="<?php echo ($browser=='mz' || $browser=='fb') ? 1 : 6 ;?>" bordercolor="#4791C5">
      <frame src="index.php?a=1&f=2" name="mainMenu" scrolling="no" FRAMEBORDER="no" BORDER="0" BORDERCOLOR="#4791C5">
      <frame src="index.php?a=1&f=3" name="menu" FRAMEBORDER="yes" BORDER="0" bordercolor="#4791C5" scrolling="yes">
    </frameset>
    <frame src="index.php?a=2" name="main" scrolling="auto" FRAMEBORDER="no" BORDER="0" BORDERCOLOR="#4791C5">
  </frameset>
</frameset><noframes>Etomite requires a browser with support for frames.</noframes>
</html>
<?php
} else {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title><?php echo $site_name." - (Etomite site manager ".$version.")"; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $etomite_charset; ?>">
<link rel="stylesheet" type="text/css" href="../manager/media/style/style.css" />
</head>
<frameset rows="0,21,25,*" frameborder="NO" border="0">
  <frame src="index.php?a=1&f=5" name="scripter" scrolling="NO" noresize>
  <frame src="index.php?a=1&f=8" name="mainMenu" scrolling="No" noresize>
  <frame src="index.php?a=1&f=12" name="topFrame" scrolling="NO" noresize>
  <frameset cols="280,*" border="<?php echo ($browser=='mz' || $browser=='fb') ? 6 : 0 ;?>" frameborder="1" FRAMESPACING="<?php echo ($browser=='mz' || $browser=='fb') ? 1 : 6 ;?>" bordercolor="#4791C5">
    <frame src="index.php?a=1&f=3" name="menu" FRAMEBORDER="yes" BORDER="0" bordercolor="#4791C5" scrolling="auto">
    <frame src="index.php?a=2" name="main" scrolling="auto" FRAMEBORDER="no" BORDER="0" BORDERCOLOR="#4791C5">
  </frameset>
</frameset><noframes>Etomite requires a browser with support for frames.</noframes>
</html>
<?php } ?>
