<?php
// Modified in 0.6.1 by Ralph
if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);

if($_SESSION['permissions']['edit_document']!=1 && $_REQUEST['a']==51) {  $e->setError(3);
  $e->dumpError();
}

function isNumber($var)
{
  if(strlen($var)==0) {
    return false;
  }
  for ($i=0;$i<strlen($var);$i++) {
    if ( substr_count ("0123456789", substr ($var, $i, 1) ) == 0 ) {
      return false;
    }
    }
  return true;
}

if(isset($_REQUEST['id'])) {
  $id = $_REQUEST['id'];
} else {
  $e->setError(2);
  $e->dumpError();
}

// check permissions on the document
include_once("./processors/user_documents_permissions.class.php");
$udperms = new udperms();
$udperms->user = $_SESSION['internalKey'];
$udperms->document = $id;
$udperms->role = $_SESSION['role'];

if(!$udperms->checkPermissions()) {
  ?>
  <br /><br />
  <div class="sectionHeader">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['access_permissions']; ?>
  </div>

  <div class="sectionBody">
    <p><?php echo $_lang['access_permission_denied']; ?></p>
  <?php
  include("includes/footer.inc.php");
  exit;
}
?>

<script language="javascript">
parent.menu.ca = "move";

function setMoveValue(pId, pName) {
  document.newdocumentparent.new_parent.value=pId;
  document.getElementById('parentName').innerHTML = "<?php echo $_lang['new_parent']; ?>: <b>" + pId + "</b> (" + pName + ")";
}

function confirmMove() {
  if(confirm("<?php echo $_lang['confirm_move'] ?>")==true) {
    document.newdocumentparent.submit();
  }
}
</script>

<div class="subTitle">
  <span class="floatLeft">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
  </span>
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <?php echo $site_name ;?> - <?php echo $_lang['move_document_title']; ?>
  </span>
</div>

<div class="sectionHeader">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['move_document_title']; ?>
</div>

<div class="sectionBody">
  <?php echo $_lang['move_document_message']; ?>
  <form method="post" action="index.php" name='newdocumentparent'>
    <input type="hidden" name="a" value="52" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <input type="hidden" name="idshow" value="<?php echo $id; ?>" />
    <?php echo $_lang['document_to_be_moved']; ?>: <b><?php echo $id; ?></b><br />
    <span id="parentName" class="warning">
      <?php echo $_lang['move_document_new_parent']; ?>
    </span><br />
    <input type="hidden" name="new_parent" value="">
    <input class="doSomethingButton" type='button' name="save" value="<?php echo $_lang['save']; ?>" onClick="confirmMove();" />
    <input class="doSomethingButton" type='button' name="cancel" value="<?php echo $_lang['cancel']; ?>" onclick="document.location.href='index.php?a=2';" />
  </form>
</div>
