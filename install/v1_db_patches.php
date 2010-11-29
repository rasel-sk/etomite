<?php
// v1_db_patches.php
// Adds chunk permissions to user_roles
// Adds the `authenticate` column to site_content
// Adds the `showinmenu` column to site_content
// Removes the redundant `id` index for site_content
// Changes some int() columns to bigint() in the logging tables to resolve 64-bit server problems
// Created: 2008-04-25 by Ralph Dahlgren
// Modified: 2008-05-08 [v1.1] by Ralph Dahlgren

include("../manager/includes/config.inc.php");
include("../manager/includes/version.inc.php");

// Connecting, selecting database
$link = @mysql_connect($database_server, $database_user, $database_password)
   or die('<span class="notok">Failure:</span> Could not connect: ' . mysql_error());
$output .= '<p><span class="ok">OK</span> - Connected to MySQL server successfully.</p>';
mysql_select_db(trim($dbase,"`")) or die('<span class=\"notok\">Failure:</span> Could not select database!!!');
$output .= '<p><span class="ok">OK</span> - Connected to database successfully.</p>';

/**********************************************************/
// Alter user_roles table
// ADD chunk and export to HTML permissions
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."user_roles`
ADD `new_chunk` INT(1) NOT NULL DEFAULT '0',
ADD `save_chunk` INT(1) NOT NULL DEFAULT '0',
ADD `edit_chunk` INT(1) NOT NULL DEFAULT '0',
ADD `delete_chunk` INT(1) NOT NULL DEFAULT '0',
ADD `export_html` INT(1) NOT NULL DEFAULT '0'";
if($result = mysql_query($query))
{
  // Display the successful operation message
  $output .= "<p><span class=\"ok\">OK</span> - The new fields for <b>chunk permissions</b> were successfully added into the ".$table_prefix."user_roles table.</p>";
}
elseif(mysql_errno()==1060)
{
  // If the chunk permissions columns already exist (mysql_errno 1060) then send a friendly message
  $output .= "<p><span class=\"ok\">OK</span> - <b>chunk permissions</b> already exist. No action required.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  $output .= "<p><span class=\"notok\">Failure:</span> Query failed: " . mysql_error(). "</p>";
}

/**********************************************************/
// Update user_roles table only if user_roles was Altered
// Set all admins (role=1) to have new permissions set to 1
/**********************************************************/
$user_roles_updated = false;
if($result && !mysql_errno())
{
  $query = "UPDATE `".$table_prefix."user_roles`
  SET `new_chunk`=1, `save_chunk`=1,`edit_chunk`=1, `delete_chunk`=1, `export_html`=1
  WHERE `id`=1";
  if($result = mysql_query($query))
  {
    // If the user_roles columns were updated, display a success message
    $output .= "<p><span class=\"ok\">OK</span> - The table, <b>user roles</b>, was updated successfully.";
    $user_roles_updated = true;
  }
  else
  {
    // If all else fails, send the appropriate failure message
    $output .= "<p><span class=\"notok\">Failure:</span> Query failed: " . mysql_error(). "</p>";
  }
}

/**********************************************************/
// Alter site_content table to ADD COLUMN `authenticate`
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."site_content` ADD COLUMN `authenticate` int(1) NOT NULL default '0'";
if($result = mysql_query($query))
{
  // Display the successful operation message
  $output .= "<p><span class=\"ok\">OK</span> - The new field, <b>`authenticate`</b>, was successfully added into the ".$table_prefix."site_content table.</p>";
}
elseif(mysql_errno()==1060)
{
  // If the `showinmenu` column already exists (mysql_errno 1060) then send a friendly message
  $output .= "<p><span class=\"ok\">OK</span> - <b>`authenticate`</b> column already exists. No action required.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  $output .= "<p><span class=\"notok\">Failure:</span> Query failed: " . mysql_error(). "</p>";
}

/**********************************************************/
// Alter site_content table to ADD COLUMN `showinmenu`
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."site_content` ADD COLUMN `showinmenu` int(1) NOT NULL default '1'";
if($result = mysql_query($query))
{
  // Display the successful operation message
  $output .= "<p><span class=\"ok\">OK</span> - The new field, <b>`showinmenu`</b>, was successfully added into the ".$table_prefix."site_content table.</p>";
}
elseif(mysql_errno()==1060)
{
  // If the `showinmenu` column already exists (mysql_errno 1060) then send a friendly message
  $output .= "<p><span class=\"ok\">OK</span> - <b>`showinmenu`</b> column already exists. No action required.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  $output .= "<p><span class=\"notok\">Failure:</span> Query failed: " . mysql_error(). "</p>";
}

/**********************************************************/
// Alter site_content table to DROP INDEX `id`
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."site_content` DROP INDEX `id`";
if($result = mysql_query($query))
{
  // Display the successful operation message
  $output .= "<p>The redundant index, <b>`id`</b>, was successfully removed from the ".$table_prefix."site_content table.</p>";
}
elseif(mysql_errno()==1091)
{
  // If the `id` index doesn't exist (mysql_errno 1091) then send a friendly message
  $output .= "<p><span class=\"ok\">OK</span> - <b>{$table_prefix}site_content.id</b> index doesn't exist. No action required.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  $output .= "<p><span class=\"notok\">Failure:</span> Query failed: " . mysql_error() . "</p>";
}

/**********************************************************/
// Alter log_access table to use BIGINT
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."log_access`
CHANGE `visitor` `visitor` BIGINT( 11 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `document` `document` BIGINT( 11 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `referer` `referer` BIGINT( 11 ) UNSIGNED NOT NULL DEFAULT '0'";

// Perform the query
if($result = mysql_query($query))
{
  // Display the successful operation message
  $output .= "<p><span class=\"ok\">OK</span> - The table, <b>log_access</b>, was updated successfully.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  $output .= "<p><span class=\"notok\">Failure:</span> Query failed: " . mysql_error() . "</p>";
}

/**********************************************************/
// Alter log_hosts table to use BIGINT
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."log_hosts` CHANGE `id` `id` BIGINT( 11 ) NOT NULL DEFAULT '0'";

// Perform the query
if($result = mysql_query($query))
{
  // Display the successful operation message
  $output .= "<p><span class=\"ok\">OK</span> - The table, <b>log_hosts</b>, was updated successfully.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  $output .= "<p><span class=\"notok\">Failure:</span> Query failed: " . mysql_error() . "</p>";
}

/**********************************************************/
// Alter log_operating_systems table to use BIGINT
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."log_operating_systems` CHANGE `id` `id` BIGINT( 11 ) NOT NULL DEFAULT '0'";

// Perform the query
if($result = mysql_query($query))
{
  // Display the successful operation message
  $output .= "<p><span class=\"ok\">OK</span> - The table, <b>log_operating_systems</b>, was updated successfully.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  $output .= "<p><span class=\"notok\">Failure:</span> Query failed: " . mysql_error() . "</p>";
}

/**********************************************************/
// Alter log_referers table to use BIGINT
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."log_referers` CHANGE `id` `id` BIGINT( 11 ) NOT NULL DEFAULT '0'";

// Perform the query
if($result = mysql_query($query))
{
  // Display the successful operation message
  $output .= "<p><span class=\"ok\">OK</span> - The table, <b>log_referers</b>, was updated successfully.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  $output .= "<p><span class=\"notok\">Failure:</span> Query failed: " . mysql_error() . "</p>";
}

/**********************************************************/
// Alter log_user_agents table to use BIGINT
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."log_user_agents` CHANGE `id` `id` BIGINT( 11 ) NOT NULL DEFAULT '0'";

// Perform the query
if($result = mysql_query($query))
{
  // Display the successful operation message
  $output .= "<p><span class=\"ok\">OK</span> - The table, <b>log_user_agents</b>, was updated successfully.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  $output .= "<p><span class=\"notok\">Failure:</span> Query failed: " . mysql_error() . "</p>";
}

/**********************************************************/
// Alter log_visitors table to use BIGINT
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."log_visitors` CHANGE `id` `id` BIGINT( 11 ) NOT NULL DEFAULT '0',
CHANGE `os_id` `os_id` BIGINT( 11 ) NOT NULL DEFAULT '0',
CHANGE `ua_id` `ua_id` BIGINT( 11 ) NOT NULL DEFAULT '0',
CHANGE `host_id` `host_id` BIGINT( 11 ) NOT NULL DEFAULT '0'";

// Perform the query
if($result = mysql_query($query))
{
  // Display the successful operation message
  $output .= "<p><span class=\"ok\">OK</span> - The table, <b>log_user_agents</b>, was updated successfully.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  $output .= "<p><span class=\"notok\">Failure:</span> Query failed: " . mysql_error() . "</p>";
}

/**********************************************************/

$output .= "<br /><b>All update processes have been completed and results posted.</b>";
$output .= "<br /><b>Please document any <span class=\"notok\"><i>Failure</i></span> messages for support purposes.</b>";
$output .= "<br /><b>If no <span class=\"notok\"><i>Failure</i></span> status was returned then all updates were successful.</b>";
$output .= "<br /><br /><b>Note:</b> Only Administrators (Role ID = 1) have had new chunk and export permissions automatically assigned. If additional user roles have been defined, these new permissions must be manually assigned to them, if required, within the <b>Etomite Manager > Manage Users > Role Management</b> panel.";
$output .= "<br /><br /><a href='upgradeStart.php' title='Run the upgradeStart.php script'>Click here for the upgrade instructions.</a><br /><br />";

// Free resultset
@mysql_free_result($result);

// Closing connection
mysql_close($link);

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
    <td align="right"><span class="headers">Database Structure</span></td>
  </tr>
  <tr class="fancyRow2">
    <td colspan="2" class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
  <tr align="left" valign="top">
    <td class="pad" id="content" colspan="2" style="padding:1em 2em;">

      <h1><b>Database Structure Upgrade Status</b></h1>

      <p><?php echo $output; ?></p>

    </td>
  </tr>
  <tr class="fancyRow2">
    <td class="border-top-bottom smallText">&nbsp;</td>
    <td class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
</table>
</body>
</html>
