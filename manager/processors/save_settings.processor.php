<?php
// save_settings.processor.php
// Last Modified: 2006-04-18 by Ralph Dahlgren

if(IN_ETOMITE_SYSTEM != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if($_SESSION['permissions']['settings'] != 1 && $_REQUEST['a'] == 30)
{
  $e->setError(3);
  $e->dumpError();
}

// added this line to ensure that friendly aliases are disabled when friendly urls are disabled
if($_POST['friendly_urls'] != 1)
{
  $_POST['friendly_alias_urls'] = 0;
}

// added this line to ensure that maximum login attempts is set
if(empty($_POST['max_attempts']))
{
  $_POST['max_attempts'] = 3;
}

// added the following lines to bypass un-needed $_POST components
unset($_POST['submit']);

foreach ($_POST as $k => $v)
{
  $sql = "REPLACE INTO $dbase.".$table_prefix."system_settings(setting_name, setting_value) VALUES('".addslashes($k)."', '".addslashes($v)."')";

  if(!@$rs = mysql_query($sql))
  {
    echo "Failed to update setting value!";
    exit;
  }
}

// empty cache
include_once("cache_sync.class.processor.php");
$sync = new synccache();
$sync->setCachepath("../assets/cache/");
$sync->setReport(false);
$sync->emptyCache(); // first empty the cache

$header="Location: index.php?a=7&r=10";
header($header);

?>
