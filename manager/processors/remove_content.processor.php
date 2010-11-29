<?php
// remove_content.processor.php

if(IN_ETOMITE_SYSTEM != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if($_SESSION['permissions']['delete_document'] != 1 && $_REQUEST['a'] == 64)
{
  $e->setError(3);
  $e->dumpError();
}

//'undelete' the document.
$sql = "DELETE FROM $dbase.".$table_prefix."site_content WHERE deleted=1;";
$rs = mysql_query($sql);
if(!$rs)
{
  echo "Something went wrong while trying to remove deleted documents!";
  exit;
}
else
{
  // empty cache
  include_once("cache_sync.class.processor.php");
  $sync = new synccache();
  $sync->setCachepath("../assets/cache/");
  $sync->setReport(false);
  $sync->emptyCache(); // first empty the cache

  // finished emptying cache - redirect
  $header="Location: index.php?r=1&a=7";
  header($header);
}

?>
