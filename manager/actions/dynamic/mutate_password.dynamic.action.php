<?php
// mutate_password.dynamic.action.php
// Modified in 0.6.1 by Ralph
// Modified 2008-05-10 [v1.1] by Ralph Dahlgren
// - HTML markup improvements

if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);
if($_SESSION['permissions']['change_password']!=1 && $_REQUEST['a']==28) {  $e->setError(3);
  $e->dumpError();
}
?>

<div class="subTitle">
  <span class="floatLeft">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <a href="#" onClick="documentDirty=false; document.userform.submit();" class="doSomethingButton"><?php echo $_lang['save']; ?></a>
  </span>
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <?php echo $site_name ;?> - <?php echo $_lang['change_password']; ?>
  </span>
</div>

<!--<link type="text/css" rel="stylesheet" href="media/style/tabs.css" />-->
<script type="text/javascript" src="media/script/tabpane.js"></script>

<div class="tab-pane" id="settingsPane">

<script type="text/javascript">
  tpSettings = new WebFXTabPane(document.getElementById("settingsPane"),0);
</script>

<div class="sectionHeader">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['change_password']; ?>
</div>

<div class="sectionBody">
  <form action="index.php?a=34" method="post" name="userform">
    <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>" />

    <p><?php echo $_lang['change_password_message']; ?></p>

    <table border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td><?php echo $_lang['change_password_new']; ?>:</td>
        <td>&nbsp;</td>
        <td><input type="password" name="pass1" class="inputBox" style="width:150px" value=""></td>
      </tr>
      <tr>
        <td><?php echo $_lang['change_password_confirm']; ?>:</td>
        <td>&nbsp;</td>
        <td><input type="password" name="pass2" class="inputBox" style="width:150px" value=""></td>
      </tr>
    </table>

    <input type="submit" name="save" style="display:none">
  </form>
</div>
</div><!-- settingsPane -->