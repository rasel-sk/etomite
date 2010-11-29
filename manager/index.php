<?php
/**************************************************************************
Etomite Content Management System
Copyright 2003, 2004 Alexander Andrew Butter
Copyright 2005 - 2008 The Etomite CMS Project

Development continued 03/2005 by Ralph A. Dahlgren with Etomite 0.6.1

This file and all dependant and otherwise related files are part of Etomite.

Etomite is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

Etomite is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
ERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Etomite; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
/***************************************************************************
 Filename: manager/index.php [v1.0]
 Function: This file is the main root file for Etomite. It is
           only file that will be directly requested, and
           depending on the request, will branch different
           content
 Last Modified: 2008-04-18 [v1.0] By: Ralph A. Dahlgren
/***************************************************************************/
// start our script timer
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
/***************************************************************************/
// before we do anything, let's help avoid XSS attacks
$_SERVER['PHP_SELF'] = htmlentities($_SERVER['PHP_SELF']);

// send anti caching headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// make sure all Etomite pages use the same session cookie
ini_set('session.cookie_domain',str_replace('www.','.',$_SERVER['HTTP_HOST']));

// set error reporting
error_reporting(E_ALL ^ E_NOTICE);

// check PHP version. Etomite is compatible with php 4 (4.1.0 up)
$php_ver_comp =  version_compare(phpversion(), "4.1.0");
// -1 if left is less, 0 if equal, +1 if left is higher
if($php_ver_comp < 0)
{
  echo "Wrong php version! You're using PHP version '".phpversion()."', and Etomite only works on 4.1.0 or higher."; // $_lang['php_version_check'];
  exit;
}

// set some runtime options
ini_set("include_path",ini_get("include_path").PATH_SEPARATOR.dirname(__FILE__)."/includes/");
error_reporting(E_ALL ^ E_NOTICE);
@set_magic_quotes_runtime(0);
include_once("includes/quotes_stripper.inc.php");

// include the html_entity_decode fake function :)
if(!function_exists('html_entity_decode'))
{
  function html_entity_decode($string, $opt = ENT_COMPAT)
  {
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    if($opt & 1)
    {
      $trans_tbl["&apos;"] = "'";
    }
    if(!($opt & 2))
    {
      unset($trans_tbl["&quot;"]);
    }
    return strtr($string, $trans_tbl);
  }
}

// define various system constant values
if(!defined("ENT_COMPAT")) define("ENT_COMPAT", 2);
if(!defined("ENT_NOQUOTES")) define("ENT_NOQUOTES", 0);
if(!defined("ENT_QUOTES")) define("ENT_QUOTES", 3);
if(!defined("IN_ETOMITE_SYSTEM")) define("IN_ETOMITE_SYSTEM", "true");

// set the document_root
if(!isset($_SERVER["DOCUMENT_ROOT"]) || empty($_SERVER["DOCUMENT_ROOT"]))
{
  $_SERVER["DOCUMENT_ROOT"] = str_replace($_SERVER["PATH_INFO"], "", ereg_replace("[\][\]", "/", $_SERVER["PATH_TRANSLATED"]))."/";
}

// include_once config file
$config_filename = "./includes/config.inc.php";
if(!file_exists($config_filename))
{
   print "Main configuration file not found. Please run the Etomite installer.<p>Check the documentation for more information.";
   exit;
}

// include the database configuration file
include_once($config_filename);

// connect to the database
if(@!$etomiteDBConn = mysql_connect($database_server, $database_user, $database_password))
{
  die("Failed to create the database connection!");
}
else
{
  mysql_select_db($dbase);
}

// get the settings from the database
include_once("includes/settings.inc.php");

// send the charset header
header('Content-Type: text/html; charset='.$etomite_charset);

// include version info
include_once("includes/version.inc.php");

// accesscontrol.php checks to see if the user is logged in. If not, a log in form is shown
include_once("includes/accesscontrol.inc.php");

// double check the session
if(!isset($_SESSION['validated']))
{
  echo "Not Logged In!";
  exit;
}

// include_once the language file
if(!isset($manager_language))
{
  $manager_language = "english"; // if not set, get the english language file.
}

$_lang = array();
include_once("includes/lang/english.inc.php");
$length_eng_lang = count($_lang);
if($manager_language!="english")
{
  include_once("includes/lang/".$manager_language.".inc.php");
}

// include_once the error handler
include_once("includes/error.class.inc.php");
$e = new errorHandler;

// first we check to see if this is a frameset request
if(!isset($_POST['a']) && !isset($_GET['a']) && ($e->getError()==0))
{
  // this looks to be a top-level frameset request, so let's serve up a frameset
  include_once("frames/1.php");
  exit;
}

// retrieve the action directive form the request
if(isset($_GET['a']) && isset($_POST['a']))
{
  $e->setError(100);
  $e->dumpError();
  // set $e to a corresponding errorcode
  // we know that if an error occurs here, something's wrong,
  // so we dump the error, thereby stopping the script.
}
else
{
  $action=$_REQUEST['a'];
}

// now we decide what to do according to the action request.
switch($action)
{
/********************************************************************/
// frame management - show the requested frame
/********************************************************************/
  case "1" :
    // get the requested frame
    $frame = $_REQUEST['f'];
    // if $frame isn't an integer this might be an XSS attack attempt so die
    if(!is_numeric($frame) || $frame < 1 || $frame > 12)
    {
      die("<b>".$_lang['functionnotimpl_message']."</b>");
    }
    if($frame > 9)
    {
      $enable_debug = false; // don't attached debug to these framesets
    }
    include_once("frames/".$frame.".php");
  break;
/********************************************************************/
// show the homepage
/********************************************************************/
  case "2" :
    // get the home page
    include_once("includes/header.inc.php");
    include_once("actions/static/welcome.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// document data
/********************************************************************/
  case "3" :
    // get the page to show document's data
    include_once("includes/header.inc.php");
    include_once("actions/static/document_data.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// content management
/********************************************************************/
  case "27" :
    // get the mutate page for changing content
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_content.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "4" :
    // get the mutate page for adding content
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_content.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "5" :
    // get the save processor
    include_once("processors/save_content.processor.php");
  break;
  case "6" :
    // get the delete processor
    include_once("processors/delete_content.processor.php");
  break;
  case "63" :
    // get the undelete processor
    include_once("processors/undelete_content.processor.php");
  break;
  case "51" :
    // get the move action
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/move_document.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "52" :
    // get the move document processor
    include_once("processors/move_document.processor.php");
  break;
  case "61" :
    // get the processor for publishing content
    include_once("processors/publish_content.processor.php");
  break;
  case "62" :
    // get the processor for publishing content
    include_once("processors/unpublish_content.processor.php");
  break;
  case "49" :
    // get the processor for document showinmenu
    include_once("processors/showinmenu.processor.php");
  break;
/********************************************************************/
// show the wait page - gives the tree time to refresh (hopefully)
/********************************************************************/
  case "7" :
    // get the wait page (so the tree can reload)
    include_once("includes/header.inc.php");
    include_once("actions/static/wait.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// let the user log out
/********************************************************************/
  case "8" :
    // get the logout processor
    include_once("processors/logout.processor.php");
  break;
/********************************************************************/
// user management
/********************************************************************/
  case "11" :
    // get the new user page
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_user.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "12" :
    // get the edit user page
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_user.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "32" :
    // get the save user processor
    include_once("processors/save_user.processor.php");
  break;
  case "28" :
    // get the change password page
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_password.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "34" :
    // get the save new password page
    include_once("processors/save_password.processor.php");
  break;
  case "33" :
    // get the delete user page
    include_once("processors/delete_user.processor.php");
  break;
/********************************************************************/
// role management
/********************************************************************/
  case "38" :
    // get the new role page
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_role.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "35" :
    // get the edit role page
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_role.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "36" :
    // get the save role page
    include_once("processors/save_role.processor.php");
  break;
  case "37" :
    // get the delete role page
    include_once("processors/delete_role.processor.php");
  break;
/********************************************************************/
// template management
/********************************************************************/
  case "16" :
    // get the edit template action
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_templates.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "19" :
    // get the new template action
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_templates.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "20" :
    // get the save processor
    include_once("processors/save_template.processor.php");
  break;
  case "21" :
    // get the delete processor
    include_once("processors/delete_template.processor.php");
  break;
/********************************************************************/
// snippet management
/********************************************************************/
  case "22" :
    // get the edit snippet action
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_snippet.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "23" :
    // get the new snippet action
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_snippet.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "24" :
    // get the save processor
    include_once("processors/save_snippet.processor.php");
  break;
  case "25" :
    // get the delete processor
    include_once("processors/delete_snippet.processor.php");
  break;
/********************************************************************/
// htmlsnippet management
/********************************************************************/
  case "78" :
    // get the edit snippet action
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_htmlsnippet.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "77" :
    // get the new snippet action
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_htmlsnippet.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "79" :
    // get the save processor
    include_once("processors/save_htmlsnippet.processor.php");
  break;
  case "80" :
    // get the delete processor
    include_once("processors/delete_htmlsnippet.processor.php");
  break;
/********************************************************************/
// show the credits page
/********************************************************************/
  case "18" :
    // get the credits page
    include_once("includes/header.inc.php");
    include_once("actions/static/credits.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// empty cache & synchronisation
/********************************************************************/
  case "26" :
    // get the cache emptying processor
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/refresh_site.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// preview a page
/********************************************************************/
  case "100" :
    // get the credits page
    include_once("actions/static/preview.php");
  break;
/********************************************************************/
// show phpInfo
/********************************************************************/
  case "200" :
?>

<script type="text/javascript">
function stopWorker()
{
  try
  {
    parent.scripter.stopWork();
  }
  catch(oException)
  {
    ww = window.setTimeout('stopWorker()',500);
  }
}
stopWorker();
</script>

<?php
    phpinfo();
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// errorpage
/********************************************************************/
  case "29" :
    // get the error page
    include_once("actions/static/error_dialog.static.action.php");
  break;
/********************************************************************/
// file manager
/********************************************************************/
  case "31" :
    // get the page to manage files
    include_once("includes/header.inc.php");
    include_once("actions/static/files.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// access permissions
/********************************************************************/
  case "40" :
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/access_permissions.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// access groups processor
/********************************************************************/
  case "41" :
    include_once("processors/access_groups.processor.php");
  break;
/********************************************************************/
// settings editor
/********************************************************************/
  case "17" :
    // get the settings editor
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_settings.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// save settings
/********************************************************************/
  case "30" :
    // get the save settings processor
    include_once("processors/save_settings.processor.php");
  break;
/********************************************************************/
// system information
/********************************************************************/
  case "53" :
    // get the system information module
    include_once("includes/header.inc.php");
    include_once("actions/static/sysinfo.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// optimise table
/********************************************************************/
  case "54" :
    // get the table optimizer
    include_once("processors/optimize_table.processor.php");
  break;
/********************************************************************/
// view logging
/********************************************************************/
  case "13" :
    // view logging
    include_once("includes/header.inc.php");
    include_once("actions/static/logging.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// empty logs
/********************************************************************/
  case "55" :
    // get the manager user logs emptier
    include_once("processors/empty_table.processor.php");
  break;
  case "56" :
    // get the visitor logs emptier
    include_once("processors/empty_visitor_logs.processor.php");
  break;
/********************************************************************/
// calls test page
/********************************************************************/
  case "999" :
    // get the test page
    include_once("includes/header.inc.php");
    include_once("includes/test_page.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// Empty recycle bin
/********************************************************************/
  case "64" :
    // get the Recycle bin emptier
    include_once("processors/remove_content.processor.php");
  break;
/********************************************************************/
// Messages
/********************************************************************/
  case "10" :
    // get the messages page
    include_once("includes/header.inc.php");
    include_once("actions/static/messages.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// Delete a message
/********************************************************************/
  case "65" :
    // get the message deleter
    include_once("processors/delete_message.processor.php");
  break;
/********************************************************************/
// Send a message
/********************************************************************/
  case "66" :
    // get the message deleter
    include_once("processors/send_message.processor.php");
  break;
/********************************************************************/
// Remove locks
/********************************************************************/
  case "67" :
    // get the lock remover
    include_once("processors/remove_locks.processor.php");
  break;
/********************************************************************/
// site logging
/********************************************************************/
  case "68" :
    // get the site_logging page
    include_once("includes/header.inc.php");
    include_once("actions/static/site_logging.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// online now
/********************************************************************/
  case "69" :
    // get the online_now page
    include_once("includes/header.inc.php");
    include_once("actions/static/online_now.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// Site schedule
/********************************************************************/
  case "70" :
    // get the schedule page
    include_once("includes/header.inc.php");
    include_once("actions/static/site_schedule.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// Search
/********************************************************************/
  case "71" :
    // get the search page
    include_once("includes/header.inc.php");
    include_once("actions/static/search.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// About
/********************************************************************/
  case "59" :
    // get the about page
    include_once("includes/header.inc.php");
    include_once("actions/static/about_etomite.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// Add weblink
/********************************************************************/
  case "72" :
    // get the weblink page
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_content.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// Personal preferences
/********************************************************************/
  case "74" :
    // get the preferences page
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/mutate_personal_prefs.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// User management
/********************************************************************/
  case "75" :
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/user_management.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// template/ snippet management
/********************************************************************/
  case "76" :
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/resources.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// keywords management
/********************************************************************/
  case "81" :
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/manage_keywords.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
  case "82" :
    include_once("processors/keywords.processor.php");
  break;
/********************************************************************/
// Export to file
/********************************************************************/
  case "83" :
    include_once("includes/header.inc.php");
    include_once("actions/static/export_site.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// MOD management
/********************************************************************/
  case "84" :
    include_once("includes/header.inc.php");
    include_once("actions/dynamic/module_management.dynamic.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// Help
/********************************************************************/
  case "9" :
    // get the help page
    include_once("includes/header.inc.php");
    include_once("actions/static/help.static.action.php");
    include_once("includes/footer.inc.php");
  break;
/********************************************************************/
// default action: show not implemented message
/********************************************************************/
  default :
    // say that what was requested doesn't do anything yet
    include_once("includes/header.inc.php");
?>

<div class="subTitle">
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <?php echo $site_name ;?> - <?php echo $_lang['functionnotimpl']; ?>
  </span>
</div>

<div class="sectionHeader">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['functionnotimpl']; ?>
</div>

<div class="sectionBody">
  <?php echo $_lang['functionnotimpl_message']; ?>
</div>

<?php
    include_once("includes/footer.inc.php");
}

/********************************************************************/
// log action, unless it's a frame request

if($action != 1 && $action != 7 && $action != 2 && $use_mgr_logging != 0)
{
  include_once("includes/log.class.inc.php");
  $log = new logHandler;
  $log->initAndWriteLog();
}

/********************************************************************/
// show debug
// clear this, because it's only set for logging purposes
unset($_SESSION['itemname']);
include_once("includes/debug.inc.php");

// THE END

?>
