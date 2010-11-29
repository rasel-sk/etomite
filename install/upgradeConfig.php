<?php
// upgradeConfig.php
//
// The purpose of this script is to upgrade the existing Etomite configuration
// file by include()ing the existing file to acquire the current values,
// reading in the new configuration file template, populating the templates
// placeholder tags with proper values, and writing the configuration file
// back out to its original location.
//
// Modified 2008-05-10 [v1.1] by Ralph Dahlgren

// include current version information
include("../manager/includes/version.inc.php");
// filename of the new configuration template file
$file1 = "config.inc.php";
// filename of the existing configuration file
$file2 = "../manager/includes/config.inc.php";

// attempt to include the original configuration file
if(file_exists($file2))
{
  include($file2);
}
else
{
  die("Configuration file, $file2, not found. Operation aborted.");
}

if($config_release == $release)
{
  $_SESSION['upgradeConfig'] = true;
  $message = "Configuration appears to be up to date. No action required.";
}
else
{
  // Attempt to open $file1 and read in its contents
  if(($fh = @fopen($file1, 'r')) === false)
  {
    die("Error opening file, $file1, in READ mode. Operation aborted.");
  }
  else
  {
    if(($contents = @fread($fh, filesize($file1))) === false)
    {
      die("Error reading file, $file1. Operation aborted.");
    }
    fclose($fh);
  }

  // Make required changes to contents
  $contents = str_replace("{HOST}", $database_server, $contents);
  $contents = str_replace("{USER}", $database_user, $contents);
  $contents = str_replace("{PASS}", $database_password, $contents);
  $contents = str_replace("{DBASE}", trim($dbase, "`"), $contents);
  $contents = str_replace("{PREFIX}", $table_prefix, $contents);
  $contents = str_replace("{SESSDIR}", $sess_dir, $contents);
  $contents = str_replace("USECUSTOMSESSIONS", $use_custom_sessions, $contents);
  $contents = str_replace("COOKIETIMEOUT", $cookie_timeout, $contents);

  // Attempt to open file2 and write out upgraded contents
  if(($fh = @fopen($file2, 'w')) === false)
  {
    die("Error opening file, $file2, in WRITE mode. Operation aborted.");
  }
  else
  {
    if(fwrite($fh, $contents) === false)
    {
      die("Error writing file, $file2. Operation aborted.");
    }
    fclose($fh);
    $message = "If you are reading this message then your configuration file upgrade has been completed successfully.";
  }
}

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
    <td align="right"><span class="headers">Configuration Upgrade</span></td>
  </tr>
  <tr class="fancyRow2">
    <td colspan="2" class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
  <tr align="left" valign="top">
    <td class="pad" id="content" colspan="2" style="padding:1em 2em;">

      <h1><b>Configuration Status</b></h1>

      <p><?php echo $message; ?></p>

      <p>You may now return to the main upgrade page to complete the upgrade procedure.</p>

      <p><a href="./upgradeStart.php" title="Go Back Now">Return to upgrade page</a></p>

    </td>
  </tr>
  <tr class="fancyRow2">
    <td class="border-top-bottom smallText">&nbsp;</td>
    <td class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
</table>
</body>
</html>
