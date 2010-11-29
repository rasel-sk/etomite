<?php
// manage_keywords.dynamic.action.php
// Last Modified 2007-08-20 By Ralph Dahlgren
// (now displays entry box when no keywords are present)
// Modified 2008-03-24 [v1.0] by Ralph to provide longer input boxes

if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);

if($_SESSION['permissions']['new_document']!=1) {
  $e->setError(3);
  $e->dumpError();
}

$sql = "SELECT * FROM $dbase.".$table_prefix."site_keywords ORDER BY keyword ASC";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);

echo $_lang['keywords_intro']."<br /><br />" ;

if($limit < 1)
{
  echo "<p><b>{$_lang['keywords_no_keywords']}</b></p>";
}
?>
<script type="text/javascript">
function checkForm() {
var requireConfirm=false;
var deleteList="";
<?php
while($row=mysql_fetch_assoc($rs))
{
?>
  if(document.getElementById('delete<?php echo $row['id']; ?>').checked==true) {
    requireConfirm = true;
    deleteList = deleteList + "\n - <?php echo addslashes($row['keyword']); ?>";
  }
<?php
}
?>
  if(requireConfirm) {
    var agree=confirm("<?php echo $_lang['confirm_delete_keywords']; ?>\n" + deleteList);
    if(agree) {
      return true;
    } else {
      return false;
    }
  }
  return true;
}
</script>

<form name="keywordsFrm" method="post" action="index.php" onsubmit="return checkForm();">
<input type="hidden" name="a" value="82" /><!--82-->
<table border=0 cellpadding=2 cellspacing=0>
  <thead>
    <tr>
      <td>
        <b><?php echo $_lang['delete']; ?></b>
      </td>
      <td>&nbsp;</td>
      <td>
        <b><?php echo $_lang['keyword']; ?></b>
      </td>
      <td>&nbsp;</td>
      <td>
        <b><?php echo $_lang['rename']; ?></b>
      </td>
    </tr>
  </thead>
  <?php
    if($limit > 0)
    {
      mysql_data_seek($rs, 0);
    }
    while($row = mysql_fetch_assoc($rs))
    {
    ?>
    <tr>
      <td>
        <input type="checkbox" name="delete_keywords[<?php echo $row['id']; ?>]" id="delete<?php echo $row['id']; ?>">
      </td>
      <td>&nbsp;</td>
      <td>
        <a onclick="if(document.getElementById('delete<?php echo $row['id']; ?>').checked==true) { document.getElementById('delete<?php echo $row['id']; ?>').checked=false; } else { document.getElementById('delete<?php echo $row['id']; ?>').checked=true; }; return false;" style="cursor:pointer; width:auto;"><b><?php echo $row['keyword']; ?></b></a>
      </td>
      <td>&nbsp;</td>
      <td>
        <input type="hidden" name="orig_keywords[keyword<?php echo $row['id']; ?>]" value="<?php echo $row['keyword']; ?>" /><input style="width:auto; type="text" name="rename_keywords[keyword<?php echo $row['id']; ?>]" value="<?php echo $row['keyword'];?>" />
      </td>
    </tr>
    <?php
    }
  ?>
    <tr><td colspan="5">&nbsp;</td></tr>
    <tr>
      <td colspan="3" align="right">
        <i><?php echo $_lang['new_keyword']; ?></i>
      </td>
      <td>&nbsp;</td>
      <td>
        <input type="text" name="new_keyword" value="" style="width:200px;"/>
      </td>
    </tr>
  </table>
<br />
<input class="doSomethingButton" type="submit" name="submitKeywords" value="<?php echo $_lang['save_all_changes']; ?>" />
</form>
