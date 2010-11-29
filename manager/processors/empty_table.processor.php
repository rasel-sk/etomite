<?php
// empty_table.processor.php

if(IN_ETOMITE_SYSTEM != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if($_SESSION['permissions']['settings'] != 1 && $_REQUEST['a'] == 55)
{
  $e->setError(3);
  $e->dumpError();
}

$sql = "TRUNCATE TABLE $dbase.".$table_prefix."manager_log;";
$rs = @mysql_query($sql);

$header="Location: index.php?a=13";
header($header);

?>
