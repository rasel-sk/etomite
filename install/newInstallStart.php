<?php
// newInstallStart.php
// Etomite CMS new installation instructions
// Modified 2008-04-08 [v1.0] by Ralph Dahlgren
// Modified 2008-05-08 [v1.1] by Ralph Dahlgren

ini_set('session.use_trans_sid', false);
session_start();
$_SESSION['sqlFile'] = $_GET['sqlFile'];
include("../manager/includes/version.inc.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>Etomite &raquo; Install</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
      @import url('../assets/site/style.css');
      .ok { color:green; }
      .notok { color:red; }
      .labelHolder {
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
    <td align="right"><span class="headers">New Installation</span></td>
  </tr>
  <tr class="fancyRow2">
    <td colspan="2" class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
  <tr align="left" valign="top">
    <td colspan="2">
      <table width="100%"  border="0" cellspacing="0" cellpadding="1">
        <tr align="left" valign="top">
          <td class="pad" id="content" colspan="2">
            <h1>New Installation Setup</h1>
            <p><b>NOTE:</b> If you are upgrading an existing installation of Etomite, please click your <a href="index.php" title="Go Back"><u><b>Back</b></u></a> button and select <b>Upgrade an existing installation</b>.</p>
            <p>Etomite setup has carried out a number of checks to see if everything's ready to start the setup.<br />
<?php

$errors = 0;

echo "<br />Checking PHP version:<b> ";

$php_ver_comp =  version_compare(phpversion(), "4.1.0");
$php_ver_comp2 =  version_compare(phpversion(), "4.3.8");
    // -1 if left is less, 0 if equal, +1 if left is higher
if($php_ver_comp < 0) {
  echo "<span class='notok'>Failed!</span> - You are running on PHP ".phpversion().", and Etomite requires PHP 4.1.0";
  $errors += 1;
} else {
  echo "<span class='ok'>OK!</span>";
  if($php_ver_comp2 < 0) {
  echo "</b><fieldset><legend>Security notice</legend>While Etomite will work on your PHP version (".phpversion()."), usage of Etomite on this version is not recommended. Your version of PHP is vulnerable to numerous security holes. As of typing, the latest PHP version is 4.3.8, which patches these holes. It is recommended you upgrade to this version for the security of your own website.</fieldset>";
  }
}

echo "</b><br />Checking if sessions are properly configured:<b> ";

if($_SESSION['session_test']!=1 ) {
  echo "<span class='notok'>Failed!</span>";
  $errors += 1;
} else {
  echo "<span class='ok'>OK!</span>";
}

echo "</b><br />Checking if <span class='mono'>assets/cache</span> directory exists:<b> ";

if(!file_exists("../assets/cache")) {
  echo "<span class='notok'>Failed!</span>";
  $errors += 1;
} else {
  echo "<span class='ok'>OK!</span>";
}

echo "</b><br />Checking if <span class='mono'>assets/cache</span> directory is writable:<b> ";

if(!is_writable("../assets/cache")) {
  echo "<span class='notok'>Failed!</span>";
  $errors += 1;
} else {
  echo "<span class='ok'>OK!</span>";
}

echo "</b><br />Checking if <span class='mono'>assets/images</span> directory exists:<b> ";

if(!file_exists("../assets/images")) {
  echo "<span class='notok'>Failed!</span>";
  $errors += 1;
} else {
  echo "<span class='ok'>OK!</span>";
}

echo "</b><br />Checking if <span class='mono'>assets/images</span> directory is writable:<b> ";

if(!is_writable("../assets/images")) {
  echo "<span class='notok'>Failed!</span>";
  $errors += 1;
} else {
  echo "<span class='ok'>OK!</span>";
}

echo "</b><br />Checking if <span class='mono'>assets/export</span> directory exists:<b> ";

if(!file_exists("../assets/export")) {
  echo "<span class='notok'>Failed!</span>";
  $errors += 1;
} else {
  echo "<span class='ok'>OK!</span>";
}

echo "</b><br />Checking if <span class='mono'>assets/export</span> directory is writable:<b> ";

if(!is_writable("../assets/export")) {
  echo "<span class='notok'>Failed!</span>";
  $errors += 1;
} else {
  echo "<span class='ok'>OK!</span>";
}

echo "</b><br />Checking if <span class='mono'>manager/includes/config.inc.php</span> file exists:<b> ";

if(!file_exists("../manager/includes/config.inc.php")) {
  echo "<span class='notok'>Failed!</span>";
  $errors += 1;
  $noConfig = true;
} else {
  echo "<span class='ok'>OK!</span>";
}

echo "</b><br />Checking if <span class='mono'>manager/includes/config.inc.php</span> is writable:<b> ";

if(!is_writable("../manager/includes/config.inc.php")) {
  echo "<span class='notok'>Failed!</span></b>";
  $errors += 1;
} else {
  echo "<span class='ok'>OK!</span></b>";
}

if($errors>0) {
  if($noConfig) {
?>

</p>
<p><b>NOTE:</b> You must rename or copy <span class='mono'>manager/includes/config.php</span> to <span class='mono'>manager/includes/config.inc.php</span> and change permissions to writable before proceeding.</p>

<?php  } ?>

<p>Unfortunately, Etomite setup cannot continue at the moment, due to the above <?php echo $errors > 1 ? $errors." " : "" ; ?>error<?php echo $errors > 1 ? "s" : "" ; ?>. Please correct the error<?php echo $errors > 1 ? "s" : "" ; ?>, and try again. If you need help figuring out how to fix the problem<?php echo $errors > 1 ? "s" : "" ; ?>, please read the documentation in the  <a class="external" href="http://docs.etomite.com/installation.html" target="_blank">Etomite Documentation</a>, or visit the  <a class="external" href="http://www.etomite.com/forums" target="_blank">Etomite Forums</a>.</p>
<br />
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr class="fancyRow2">
    <td class="border-top-bottom smallText">&nbsp;</td>
    <td class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
</table>
</body>
</html>
<?php
exit;
}
?>
</p>
<script type="text/javascript">
  function validate() {
    var f = document.myForm;
    if(f.databasename.value=="") {
      alert('You need to enter a value for database name!');
      return false;
    }
    if(f.databasehost.value=="") {
      alert('You need to enter a value for database host!');
      return false;
    }
    if(f.databaseloginname.value=="") {
      alert('You need to enter your database login name!');
      return false;
    }
    if(f.cmsadmin.value=="") {
      alert('You need to enter a username for the Etomite admin account!');
      return false;
    }
    if(f.cmspassword.value=="") {
      alert('You need to a password for the Etomite admin account!');
      return false;
    }
    if(f.cmspassword.value!=f.cmspasswordconfirm.value) {
      alert('The administrator password and the confirmation don\'t match!');
      return false;
    }
    return true;
  }
</script>
                <form action="license.php" method="post" name="myForm" onsubmit="return validate()">
                <p>Please enter the name of the database you've created for Etomite. If you haven't created<br />
                  a database yet, Etomite will attempt to do so for you, but this may fail depending on the <br />
                  MySQL setup your host uses.</p>
                <p><span class="labelHolder"><label for="databasename">Database name:</label></span><input type="text" id="databasename" name="databasename" style="width:200px" value="etomite" /><br />
                <span class="labelHolder"><label for="tableprefix">Table prefix:</label></span><input type="text" id="tableprefix" name="tableprefix" style="width:200px" value="etomite_" /></p>
                <p>Now please enter the login data for your database.</p>
                <p><span class="labelHolder"><label for="databasehost">Database host:</label></span><input type="text" id="databasehost" name="databasehost" value="localhost" style="width:200px" /><br />
                <span class="labelHolder"><label for="databaseloginname">Database login name:</label></span><input type="text" id="databaseloginname" name="databaseloginname" style="width:200px" /><br />
                <span class="labelHolder"><label for="databaseloginpassword">Database password:</label></span><input type="password" id="databaseloginpassword" name="databaseloginpassword" style="width:200px" /></p>
                <p>Now you'll need to enter some details for the main Etomite administrator account.<br />
                  You can fill in your own name here, and a password you're not likely to forget. <br />
                  You'll need these to log into Etomite once setup is complete.</p>
                <p><span class="labelHolder"><label for="cmsadmin">Administrator username:</label></span><input type="text" id="cmsadmin" name="cmsadmin" style="width:200px" value="admin" /><br />
                <span class="labelHolder"><label for="cmspassword">Administrator password:</label></span><input type="password" id="cmspassword" name="cmspassword" style="width:200px" value="" /><br />
                <span class="labelHolder"><label for="cmspasswordconfirm">Confirm password:</label></span><input type="password" id="cmspasswordconfirm" name="cmspasswordconfirm" style="width:200px" value="" /></p>
                <p><input type="submit" value="Setup Etomite!" /></p>
              </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr class="fancyRow2">
    <td class="border-top-bottom smallText">&nbsp;</td>
    <td class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
</table>
</body>
</html>
