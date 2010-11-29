<?php
// delete_snippet.processor.php

if(IN_ETOMITE_SYSTEM != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if($_SESSION['permissions']['delete_snippet'] != 1 && $_REQUEST['a'] == 25)
{
  $e->setError(3);
  $e->dumpError();
}

$id = $_GET['id'];
//ok, delete the snippet.
$sql = "DELETE FROM $dbase.".$table_prefix."site_snippets WHERE $dbase.".$table_prefix."site_snippets.id=".$id.";";
$rs = mysql_query($sql);
if(!$rs)
{
  echo "Something went wrong while trying to delete the snippet...";
  exit;
}
else
{
  $header="Location: index.php?a=76&r=2";
  header($header);
}
?>
