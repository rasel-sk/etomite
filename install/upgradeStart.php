<?php
// upgradeStart.php
// Etomite CMS upgrade instructions
// Modified: 2006-04-08 by Ralph Dahlgren
// Modified: 2007-05-03 by Ralph Dahlgren
// Modified: 2008-04-25 [v1.0] by Ralph Dahlgren
// Modified 2008-05-08 [v1.1] by Ralph Dahlgren

session_start();
$_SESSION['session_test'] = 1;
include("../manager/includes/version.inc.php");
$errors = 0;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>Etomite &raquo; Install</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
      @import url('../assets/site/style.css');
      .ok
      {
        color:green;
        font-weight: bold;
      }
      .notok
      {
        color:red;
        font-weight: bold;
      }
      .labelHolder
      {
        width : 180px;
        float : left;
        font-weight: bold;
      }
    </style>
  <script type="text/javascript" src="extLinks.js"> </script>
</head>

<body>
<table border="0" cellpadding="0" cellspacing="0" class="mainTable">
  <tr class="fancyRow">
    <td><span class="headers">&nbsp;<img src="../manager/media/images/misc/dot.gif" alt="" style="margin-top: 1px;" />&nbsp;Etomite <?php echo $code_name." v".$release; ?></span></td>
    <td align="right"><span class="headers">Previous Release Upgrade</span></td>
  </tr>
  <tr class="fancyRow2">
    <td colspan="2" class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
  <tr align="left" valign="top">
    <td colspan="2"><table width="100%"  border="0" cellspacing="0" cellpadding="1">
      <tr align="left" valign="top">
        <td class="pad" id="content" colspan="2">

          <h1>Releases Not Supported</h1>

          <p>Support for Etomite releases prior to 0.6.1 (0.6 Heliades and earlier) has been dropped. Please see the file,  <a class="external" href="pre_061_notes.php" title="pre_061_notes.php"><b><u>pre_061_notes.php</u></b></a>, or the Etomite forums for information regarding older releases.</p>

          <h1>Performing System Checks</h1>

<?php
echo "Checking if <span class='mono'>manager/includes/config.inc.php</span> file exists: ";
if(!file_exists("../manager/includes/config.inc.php")) {
  echo "<span class='notok'>Failed!</span>";
  $errors += 1;
  $noConfig = true;
} else {
  echo "<span class='ok'>OK!</span>";
}

if($noConfig != true) {
  echo "<br />Checking if <span class='mono'>manager/includes/config.inc.php</span> is writable: ";
  if(!is_writable("../manager/includes/config.inc.php")) {
    echo "<span class='notok'>Failed!</span>";
    $errors += 1;
    $notWritable = true;
  } else {
    echo "<span class='ok'>OK!</span>";
  }

  echo "<br />Checking if <span class='mono'>manager/includes/config.inc.php</span> is valid: ";
  @include("../manager/includes/config.inc.php");
  // If config.inc.php doesn't exist or isn't complete, display installer link and die
  if(empty($database_server)
  || strpos($database_server, "{") !== false
  || strpos($database_server, "}") !== false
  || strpos($database_user, "{") !== false
  || strpos($database_user, "}") !== false
  || strpos($database_password, "{") !== false
  || strpos($database_password, "}") !== false
  || strpos($dbase, "{") !== false
  || strpos($dbase, "}") !== false
  || strpos($table_prefix, "{") !== false
  || strpos($table_prefix, "}") !== false)
  {
    echo "<span class='notok'>Failed!</span>";
    $errors += 1;
    $notValid = true;
  } else {
    echo "<span class='ok'>OK!</span>";
  }
}

if($errors > 0) {
?>

<p>Unfortunately, the Etomite upgrade cannot continue at the moment, due to the above <?php echo $errors > 1 ? $errors." " : "" ; ?>error<?php echo $errors > 1 ? "s" : "" ; ?>. Please correct the error<?php echo $errors > 1 ? "s" : "" ; ?>, and <a href="./upgradeStart.php" title="Try Again">try again</a>. If the explanation below doesn't help, please visit the  <a class="external" href="http://www.etomite.com/forums" title="Get Help Now" target="_blank">Etomite Forums</a>.</p>

<?php
if($noConfig) {
?>

<p>The configuration file was not found. There are two main reasons why the file may be missing - either you have deleted or moved this file, or you have accidentally chosed to upgrade instead of performing a new installation.</p>

<?php } ?>

<?php
if($notWritable) {
?>

<p>The <span class='mono'>manager/includes/config.inc.php</span> file must be writable so that this script can make any required changes to that file.</p>

<?php } ?>

<?php
if($notValid) {
?>

<p>Because you have chosen to upgrade a pervious Etomite installation a valid configuration file should already be in place. While a configuration file was found, it does not currently contain all of the required valid settings.</p>

<?php } ?>

<?php
if($noConfig || $notValid) {
?>

<p>If you have accidentally deleted this file, and don't have a backup, you can edit <span class='mono'>manager/includes/config.inc.generic.php</span> by replacing each of the placeholder <b>{TAG}</b>'s with the appropriate database settings for your installation and save it as <span class='mono'>manager/includes/config.inc.php</span> and then <a href="./upgradeStart.php" title="Try Again">try again</a>. If custom sessions were being used then those settings will also need to be reset.</p>

<?php } ?>

<p>If you are attempting to perform a <b>New installation</b>, please <a href="./index.php" title="Go Back">go back</a> and make the proper selection.</p>

<?php
// everything checks out ok so we can proceed with the upgrade
} else {
  // check the current configuration file to see if it needs to be upgraded
  echo "<br />Checking if <span class='mono'>manager/includes/config.inc.php</span> file upgraded: ";
  include("../manager/includes/config.inc.php");
  if($config_release != $release) {
    echo "<span class='notok'>Failed!</span> -- <a href='upgradeConfig.php' title='Fix it now'><b><u>Upgrade Now</u></b></a>";
    $oldConfig = true;
  } else {
    echo "<span class='ok'>OK!</span>";
  }
?>
      <p></p>
      <h1>Upgrade Instructions for Etomite 0.6.1 and newer</h1>

      <p>The fact that you are reading these instructions indicates that you have already copied the new files for this release onto your server.</p>

      <p>If you have not already done so, please take the time to read the <a class="external" href="README.html" title="Click to read this file now.">README</a> file. There is also a text version located in the root directory where this package was installed. Doing so could eliminate the possibility of encountering un-needed problems during your Etomite upgrade.</p>

      <p>All existing installations being upgraded which had a release number prior to Etomite Prelude v1.0 will require various database updates. Once you have successfully completed the configuration file modifications listed below it is <b>mandatory</b> that you run the script, <b> <a href="v1_db_patches.php" title="Click to run this script now"><b><u>v1_db_patches.php</u></b></a></b>, before attempting to access your sites main page or entering your Etomite Manager. You can execute this script now by clicking on the script name above and, upon successful completion, you can return to this script - or you may opt to run the script independently. Attempting to run this script on a previously updated database will not result in any problems - you will simply receive messages stating an OK completion status for each item.</p>

      <p>Once the above steps have been completed login to your  <a class="external" href="../manager/" title="Etomite Manager Login"><b><u>Etomite manager</u></b></a>, verify and save your Etomite Configuration, and perform a Clear site cache from the Etomite Main Menu. After these steps have been completed you should have a fully function Etomite Prelude v<?php echo $release; ?> installation.</p>

      <p>Once you have completed the required tasks in the Etomite manager you can <a href="../" title="Go There Now"><b><u>Click Here</u></b></a> to see your upgraded site in action.</p>

      <p><b>Good luck!</b></p>

      <p><b>The Etomite CMS Project Development Team</b></p>

      <p style="text-align:center;"> <a class="external" href="http://www.etomite.com" title="www.etomite.com">Etomite Website</a>&nbsp;|&nbsp; <a class="external" href="http://www.etomite.com/forums/" title="Support Forums">Etomite Support Forums</a></p>

<?php } ?>

    </td>
      </tr>
    </table></td>
  </tr>
  <tr class="fancyRow2">
    <td class="border-top-bottom smallText">&nbsp;</td>
    <td class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
</table>
</body>
</html>