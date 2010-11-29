<?php
// delete_user.processor.php

if(IN_ETOMITE_SYSTEM != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if($_SESSION['permissions']['delete_user'] != 1 && $_REQUEST['a'] == 33)
{
  $e->setError(3);
  $e->dumpError();
}

// delete the template, but first check it doesn't have any documents using it
$id=$_GET['id'];
if($id==$_SESSION['internalKey'])
{
  echo "You can't delete yourself!";
  exit;
}

//ok, delete the user.
$sql = "DELETE FROM $dbase.".$table_prefix."manager_users WHERE $dbase.".$table_prefix."manager_users.id=".$id.";";
$rs = mysql_query($sql);
if(!$rs)
{
  echo "Something went wrong while trying to delete the user...";
  exit;
}

$sql = "DELETE FROM $dbase.".$table_prefix."member_groups WHERE $dbase.".$table_prefix."member_groups.member=".$id.";";
$rs = mysql_query($sql);
if(!$rs)
{
  echo "Something went wrong while trying to delete the user's access permissions...";
  exit;
}

// delete the attributes
$sql = "DELETE FROM $dbase.".$table_prefix."user_attributes WHERE $dbase.".$table_prefix."user_attributes.internalKey=".$id.";";
$rs = mysql_query($sql);
if(!$rs)
{
  echo "Something went wrong while trying to delete the user attributes...";
  exit;
}
else
{
  $header="Location: index.php?a=75";
  header($header);
}

?>
