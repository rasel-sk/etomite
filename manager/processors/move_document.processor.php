<?php
// move_document.processor.php
// Last Modified: 2006-06-01 (security bug fix (By: Alfabetto))

if(IN_ETOMITE_SYSTEM != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if($_SESSION['permissions']['edit_document'] != 1 && $_REQUEST['a'] == 52)
{
  $e->setError(3);
  $e->dumpError();
}

// ok, two things to check.
// first, document cannot be moved to itself
// second, new parent must be a folder. If not, set it to folder.
if($_REQUEST['id'] == $_REQUEST['new_parent'])
{
  $e->setError(600);
  $e->dumpError();
}
if($_REQUEST['id'] == "")
{
  $e->setError(601);
  $e->dumpError();
}
if($_REQUEST['new_parent'] == "")
{
  $e->setError(602);
  $e->dumpError();
}

// START: security bug fix (By: Alfabetto)
// check to see if the user is allowed to move the document in the new place
include_once("user_documents_permissions.class.php");
$udperms = new udperms();
$udperms->user = $_SESSION['internalKey'];
$udperms->document = $_REQUEST['new_parent'];
$udperms->role = $_SESSION['role'];

if(!$udperms->checkPermissions())
{
  include("../includes/header.inc.php");
  echo "
    <br />
    <br />
    <div class=\"sectionHeader\">
      <img src=\"media/images/misc/dot.gif\" alt=\".\" />
      &nbsp;".$_lang['access_permissions']."
    </div>
    <div class=\"sectionBody\">
      <p>".$_lang['access_permission_parent_denied']."</p>
    </div>
  ";
  include("../includes/footer.inc.php");
  exit;
}
// END: security bug fix (By: Alfabetto)

$sql = "SELECT parent FROM $dbase.".$table_prefix."site_content WHERE id=".$_REQUEST['id'].";";
$rs = mysql_query($sql);
if(!$rs)
{
  echo "An error occured while attempting to find the document's current parent.";
}

$row = mysql_fetch_assoc($rs);
$oldparent = $row['parent'];

$sql = "UPDATE $dbase.".$table_prefix."site_content SET isfolder=1 WHERE id=".$_REQUEST['new_parent'].";";
$rs = mysql_query($sql);
if(!$rs)
{
  echo "An error occured while attempting to change the new parent to a folder.";
}

$sql = "UPDATE $dbase.".$table_prefix."site_content SET parent=".$_REQUEST['new_parent'].", editedby=".$_SESSION['internalKey'].", editedon=".time()." WHERE id=".$_REQUEST['id'].";";
$rs = mysql_query($sql);
if(!$rs)
{
  echo "An error occured while attempting to move the document to the new parent.";
}

// finished moving the document, now check to see if the old_parent should no longer be a folder.
$sql = "SELECT count(*) FROM $dbase.".$table_prefix."site_content WHERE parent=$oldparent;";
$rs = mysql_query($sql);
if(!$rs)
{
  echo "An error occured while attempting to find the old parents' children.";
}
$row = mysql_fetch_assoc($rs);
$limit = $row['count(*)'];

if(!$limit > 0)
{
  $sql = "UPDATE $dbase.".$table_prefix."site_content SET isfolder=0 WHERE id=$oldparent;";
  $rs = mysql_query($sql);
  if(!$rs)
  {
    echo "An error occured while attempting to change the old parent to a regular document.";
  }
}

// empty cache & sync site
include_once("cache_sync.class.processor.php");
$sync = new synccache();
$sync->setCachepath("../assets/cache/");
$sync->setReport(false);
$sync->emptyCache(); // first empty the cache

$header="Location: index.php?r=1&id=$id&a=7";
header($header);

?>
