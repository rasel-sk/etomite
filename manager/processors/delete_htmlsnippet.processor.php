<?php
// delete_htmlsnippet.processor.php
// Last Modified 2008-03-18 by Ralph

if(IN_ETOMITE_SYSTEM != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if($_SESSION['permissions']['delete_chunk'] != 1 && $_REQUEST['a'] == 80)
{
  $e->setError(3);
  $e->dumpError();
}

$id = $_GET['id'];
// attempt to delete the chunk
$sql = "DELETE FROM $dbase.".$table_prefix."site_htmlsnippets WHERE $dbase.".$table_prefix."site_htmlsnippets.id=".$id.";";
$rs = mysql_query($sql);
if(!$rs)
{
  echo "Something went wrong while trying to delete the htmlsnippet...";
  exit;
}
else
{
  header("Location: index.php?a=76&r=2");
}

?>
