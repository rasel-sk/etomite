<?php
// showinmenu.processor.php
// Created 2008-03-18 by Ralph
// toggles document showinmenu setting from the doc tree context menu

if(IN_ETOMITE_SYSTEM != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if($_SESSION['permissions']['edit_document'] != 1 && $_REQUEST['a'] == 49)
{
  $e->setError(3);
  $e->dumpError();
}

// retrieve the document id
$id = $_REQUEST['id'];
$show = $_REQUEST['show'];

// check permissions on the document
include_once("./processors/user_documents_permissions.class.php");
$udperms = new udperms();
$udperms->user = $_SESSION['internalKey'];
$udperms->document = $id;
$udperms->role = $_SESSION['role'];

if(!$udperms->checkPermissions())
{
  include("../includes/header.inc.php");
?>

<!-- START:display permissions error message -->
<br />
<br />
<div class="sectionHeader">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['access_permissions']; ?>
</div>
<div class="sectionBody">
  <p><?php echo $_lang['access_permission_denied']; ?></p>
</div>
<!-- END:display permissions error message -->

<?php
  include("../includes/footer.inc.php");
  exit;
}

// update the document
$sql = "UPDATE $dbase.".$table_prefix."site_content SET showinmenu=$show, editedby=".$_SESSION['internalKey'].", editedon=".time()." WHERE id=$id;";

$rs = mysql_query($sql);

if(!$rs)
{
  echo "An error occured while attempting to update the document.";
}

header("Location: index.php?r=1&id=$id&a=7");

?>
