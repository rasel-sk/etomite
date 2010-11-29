<?php
// Context sensitive menu for document tree
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");

function constructLink($action, $img, $text, $allowed) {
  if($allowed==1) {
?>
<DIV  class="menuLink"
      onmouseover="this.className='menuLinkOver';"
      onmouseout="this.className='menuLink';"
      onclick="this.className='menuLink';
               parent.menuHandler(<?php echo $action; ?>);
               parent.hideMenu();">
  <img src="media/images/icons/<?php echo $img; ?>.gif" align="absmiddle">
  <?php echo $text; ?>
</DIV>
<?php
  } else {
?>
<DIV class="menuLinkDisabled">
  <img src="media/images/icons/<?php echo $img; ?>.gif" align="absmiddle">
  <?php echo $text; ?>
</DIV>
<?php
  }
}
?>

<html>
<head>
<title>ContextMenu</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $etomite_charset; ?>" />
<link rel="stylesheet" type="text/css" href="media/style/style.css" />

</HEAD>
<BODY onselectstart="return false;" onblur="parent.hideMenu();">
<div id='nameHolder'></div>
<?php
  constructLink(1, "context_view", $_lang["view_document"], 1);
  constructLink(2, "save", $_lang["edit_document"], $_SESSION['permissions']['edit_document']);
  constructLink(5, "cancel", $_lang["move_document"], $_SESSION['permissions']['edit_document']);
  constructLink(3, "save", $_lang["create_document_here"], $_SESSION['permissions']['new_document']);
  constructLink(6, "weblink", $_lang["create_weblink_here"], $_SESSION['permissions']['new_document']);
?>
<div class="seperator"></div>
<?php
  constructLink(4, "delete", $_lang["delete_document"], $_SESSION['permissions']['delete_document']);
  constructLink(8, "b092", $_lang["undelete_document"], $_SESSION['permissions']['delete_document']);
?>
<div class="seperator"></div>
<?php
  constructLink(9, "date", $_lang["publish_document"], $_SESSION['permissions']['edit_document']);
  constructLink(10, "date", $_lang["unpublish_document"], $_SESSION['permissions']['edit_document']);
?>
</BODY>
</HTML>
