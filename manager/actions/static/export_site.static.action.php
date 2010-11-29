<?php

if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);

if($_SESSION['permissions']['edit_document']!=1) {
  $e->setError(3);
  $e->dumpError();
}

// figure out the base of the server, so we know where to get the documents in order to export them
$base = 'http://'.$_SERVER['SERVER_NAME'].str_replace("/manager/index.php", "", $_SERVER["PHP_SELF"]);

?>

<div class="subTitle">
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5" alt="" /><br />
    <?php echo $site_name ;?>&nbsp;-&nbsp;
    <?php echo $_lang['export_site']; ?>
  </span>
</div>

<div class="sectionHeader">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;
  <?php echo $_lang['export_site']; ?>
</div>

<div class="sectionBody">

<?php

if(!isset($_POST['export'])) {
echo $_lang['export_site_message'];
?>
<fieldset><legend><?php echo $_lang['export_site']; ?></legend>
<form action="index.php" method="post" name="exportFrm">
<input type="hidden" name="export" value="export" />
<input type="hidden" name="a" value="83" />
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
  <tr>
    <td valign="top"><b><?php echo $_lang['export_site_cacheable']; ?></b></td>
    <td width="30">&nbsp;</td>
    <td><input type="radio" name="includenoncache" value="1" checked="checked"><?php echo $_lang['yes'];?><br />
    <input type="radio" name="includenoncache" value="0"><?php echo $_lang['no'];?></td>
  </tr>
  <tr>
    <td><b><?php echo $_lang['export_site_prefix']; ?></b></td>
    <td>&nbsp;</td>
    <td><input type="text" name="prefix" value="<?php echo $friendly_url_prefix; ?>" /></td>
  </tr>
  <tr>
    <td><b><?php echo $_lang['export_site_suffix']; ?></b></td>
    <td>&nbsp;</td>
    <td><input type="text" name="suffix" value="<?php echo $friendly_url_suffix; ?>" /></td>
  </tr>
  <tr>
    <td valign="top"><b><?php echo $_lang['export_site_maxtime']; ?></b></td>
    <td>&nbsp;</td>
    <td><input type="text" name="maxtime" value="30" />
    <br />
    <small><?php echo $_lang['export_site_maxtime_message']; ?></small>
  </td>
  </tr>
</table>
<p />
<table cellpadding="0" cellspacing="0">
  <td>
    <input type="button" name="start" value="<?php echo $_lang["export_site_start"]; ?>" onClick="document.exportFrm.submit();" />
    </span>
  </td>
</table>
</form>

</fieldset>
<?php
} else {

  $maxtime = $_POST['maxtime'];
  if(!is_numeric($maxtime)) {
    $maxtime = 30;
  }

  set_time_limit($maxtime);
  $mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $exportstart = $mtime;

  $filepath = "../assets/export/";
  if(!is_writable($filepath)) {
    echo $_lang['export_site_target_unwritable'];
    include("includes/footer.inc.php");
    exit;
  }

  $prefix = $_POST['prefix'];
  $suffix = $_POST['suffix'];

  $noncache = $_POST['includenoncache']==1 ? "" : "AND $dbase.".$table_prefix."site_content.cacheable=1";

  $sql = "SELECT id, alias, pagetitle FROM $dbase.".$table_prefix."site_content WHERE $dbase.".$table_prefix."site_content.deleted=0 AND $dbase.".$table_prefix."site_content.published=1 AND $dbase.".$table_prefix."site_content.type='document' $noncache";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  printf($_lang['export_site_numberdocs'], $limit);

  for($i=0; $i<$limit; $i++) {

    $row=mysql_fetch_assoc($rs);

    $id = $row['id'];
    printf($_lang['export_site_exporting_document'], $i, $limit, $row['pagetitle'], $id);
    $alias = $row['alias'];

    $filename = !empty($alias) ? $prefix.$alias.$suffix : $prefix.$id.$suffix ;

    // get the file
    if(@$handle = fopen("$base/index.php?id=$id", "r")) {
      $buffer = "";
      while (!feof ($handle)) {
        $buffer .= fgets($handle, 4096);
      }
      fclose ($handle);

      // save it
      $filename = "$filepath$filename";
      $somecontent = $buffer;

      if(!$handle = fopen($filename, 'w')) {
        echo $_lang['export_site_failed']." Cannot open file ($filename)<br />";
        exit;
      } else {
        // Write $somecontent to our opened file.
        if(fwrite($handle, $somecontent) === FALSE) {
          echo $_lang['export_site_failed']." Cannot write file.<br />";
          exit;
        }
        fclose($handle);
      echo $_lang['export_site_success']."<br />";
      }
    } else {
      echo $_lang['export_site_failed']." Could not retrieve document.<br />";
    }
  }

  $mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $exportend = $mtime;
  $totaltime = ($exportend - $exportstart);
  printf ("<p />".$_lang['export_site_time'], round($totaltime, 3));
}

?>
