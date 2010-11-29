<?php
// 0614_db_patches.php
// Adds the `authenticate` column to site_content
// Adds the `showinmenu` column to site_content
// Removes the redundant `id` index for site_content
// Changes some int() columns to bigint() in the logging tables to resolve 64-bit server problems
// Created: 2007-05-03 by Ralph Dahlgren

include("../manager/includes/config.inc.php");

// Connecting, selecting database
$link = mysql_connect($database_server, $database_user, $database_password)
   or die('<b>Failure:</b> Could not connect: ' . mysql_error());
echo '<p><b>OK</b> - Connected to MySQL server successfully.</p>';
mysql_select_db(trim($dbase,"`")) or die('<b>Failure:</b> Could not select database!!!');
echo '<p><b>OK</b> - Connected to database successfully.</p>';

/**********************************************************/
// Alter site_content table to ADD COLUMN `authenticate`
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."site_content` ADD COLUMN `authenticate` int(1) NOT NULL default '0'";
if($result = mysql_query($query))
{
  // Display the successful operation message
  echo "<p><b>OK</b> - The new field, <b>`authenticate`</b>, was successfully added into the ".$table_prefix."site_content table.</p>";
}
elseif(mysql_errno()==1060)
{
  // If the `showinmenu` column already exists (mysql_errno 1060) then send a friendly message
  echo "<p><b>OK</b> - <b>`authenticate`</b> column already exists. No action required.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  echo "<p><b>Failure:</b> Query failed: " . mysql_error(). "</p>";
}

/**********************************************************/
// Alter site_content table to ADD COLUMN `showinmenu`
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."site_content` ADD COLUMN `showinmenu` int(1) NOT NULL default '1'";
if($result = mysql_query($query))
{
  // Display the successful operation message
  echo "<p><b>OK</b> - The new field, <b>`showinmenu`</b>, was successfully added into the ".$table_prefix."site_content table.</p>";
}
elseif(mysql_errno()==1060)
{
  // If the `showinmenu` column already exists (mysql_errno 1060) then send a friendly message
  echo "<p><b>OK</b> - <b>`showinmenu`</b> column already exists. No action required.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  echo "<p><b>Failure:</b> Query failed: " . mysql_error(). "</p>";
}

/**********************************************************/
// Alter site_content table to DROP INDEX `id`
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."site_content` DROP INDEX `id`";
if($result = mysql_query($query))
{
  // Display the successful operation message
  echo "<p>The redundant index, <b>`id`</b>, was successfully removed from the ".$table_prefix."site_content table.</p>";
}
elseif(mysql_errno()==1091)
{
  // If the `id` index doesn't exist (mysql_errno 1091) then send a friendly message
  echo "<p><b>OK</b> - <b>{$table_prefix}site_content.id</b> index doesn't exist. No action required.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  echo "<p><b>Failure:</b> Query failed: " . mysql_error() . "</p>";
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
  echo "<p><b>OK</b> - The table, <b>log_access</b>, was updated successfully.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  echo "<p><b>Failure:</b> Query failed: " . mysql_error() . "</p>";
}

/**********************************************************/
// Alter log_hosts table to use BIGINT
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."log_hosts` CHANGE `id` `id` BIGINT( 11 ) NOT NULL DEFAULT '0'";

// Perform the query
if($result = mysql_query($query))
{
  // Display the successful operation message
  echo "<p><b>OK</b> - The table, <b>log_hosts</b>, was updated successfully.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  echo "<p><b>Failure:</b> Query failed: " . mysql_error() . "</p>";
}

/**********************************************************/
// Alter log_operating_systems table to use BIGINT
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."log_operating_systems` CHANGE `id` `id` BIGINT( 11 ) NOT NULL DEFAULT '0'";

// Perform the query
if($result = mysql_query($query))
{
  // Display the successful operation message
  echo "<p><b>OK</b> - The table, <b>log_operating_systems</b>, was updated successfully.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  echo "<p><b>Failure:</b> Query failed: " . mysql_error() . "</p>";
}

/**********************************************************/
// Alter log_referers table to use BIGINT
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."log_referers` CHANGE `id` `id` BIGINT( 11 ) NOT NULL DEFAULT '0'";

// Perform the query
if($result = mysql_query($query))
{
  // Display the successful operation message
  echo "<p><b>OK</b> - The table, <b>log_referers</b>, was updated successfully.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  echo "<p><b>Failure:</b> Query failed: " . mysql_error() . "</p>";
}

/**********************************************************/
// Alter log_user_agents table to use BIGINT
/**********************************************************/
$query = "ALTER TABLE `".$table_prefix."log_user_agents` CHANGE `id` `id` BIGINT( 11 ) NOT NULL DEFAULT '0'";

// Perform the query
if($result = mysql_query($query))
{
  // Display the successful operation message
  echo "<p><b>OK</b> - The table, <b>log_user_agents</b>, was updated successfully.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  echo "<p><b>Failure:</b> Query failed: " . mysql_error() . "</p>";
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
  echo "<p><b>OK</b> - The table, <b>log_user_agents</b>, was updated successfully.</p>";
}
else
{
  // If all else fails, send the appropriate failure message
  echo "<p><b>Failure:</b> Query failed: " . mysql_error() . "</p>";
}

/**********************************************************/

echo "<br /><b>All update processes have been completed and results posted.</b>";
echo "<br /><b>Please document any <i>Failure</i> messages for support purposes.</b>";
echo "<br /><b>If no <i>Failure</i> status was returned then all updates were successful.</b>";
echo "<br /><a href='upgradeStart.php' title='Run the upgradeStart.php script'>Click here for the upgrade instructions.</a><br />";

// Free resultset
@mysql_free_result($result);

// Closing connection
mysql_close($link);

?>
