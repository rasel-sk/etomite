<?php
// sysinfo.static.action.php
// System Information Display Panels
// Modified 2008-04-08 [v1.0] by Ralph to use system date|time formatting
// Modified 2008-05-10 [v1.1] by Ralph Dahlgren
// - HTML markup improvements

if(IN_ETOMITE_SYSTEM != "true")
{
  die($_lang["include_ordering_error"]);
}

// Version stuff...
if(@$handle = fopen("http://www.etomite.com/status/status.php", "r"))
{
  $newversion = fgets($handle, 4096);
  fclose($handle);
  $newrelease = trim(strip_tags($newversion));
  if($release.$patch_level == $newrelease)
  {
    $newversiontext = $_lang['sys_info_version_ok'];
  }
  if($release.$patch_level < $newrelease)
  {
    $newversiontext = $_lang['sys_info_version_update']."<b>".$newrelease."</b>";
  }
  if($release.$patch_level > $newrelease)
  {
    $newversiontext = $_lang['sys_info_version_ok'];
  }
}
else
{
  $newversiontext = $_lang['sys_info_version_no_connect'];
}
?>

<div class="subTitle">
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <?php echo $site_name ;?> - <?php echo $_lang["view_sysinfo"]; ?>
  </span>
</div>

<script type="text/javascript">
function checkIM()
{
  im_on = document.settings.im_plugin[0].checked; // check if im_plugin is on
  if(im_on == true)
  {
    showHide(/imRow/, 1);
  }
}

function showHide(what, onoff)
{
  var all = document.getElementsByTagName( "*" );
  var l = all.length;
  var buttonRe = what;
  var id, el, stylevar;

  if(onoff == 1)
  {
    stylevar = "<?php echo $displayStyle; ?>";
  }
  else
  {
    stylevar = "none";
  }

  for(var i = 0; i < l; i++ )
  {
    el = all[i]
    id = el.id;
    if ( id == "" ) continue;
    if (buttonRe.test(id))
    {
      el.style.display = stylevar;
    }
  }
}
</script>

<!--<link type="text/css" rel="stylesheet" href="media/style/tabs.css" />-->
<script type="text/javascript" src="media/script/tabpane.js"></script>

<div class="tab-pane" id="sysInfoPane">

<script type="text/javascript">
  tpSettings = new WebFXTabPane(document.getElementById("sysInfoPane"));
</script>

<!-- Recent Documents Panel -->
<div class="tab-page" id="tabPage1">
  <div class="tab">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["activity_title"]; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage1"));
  </script>

  <div class="sectionBody" id="lyr1">
    <p><?php echo $_lang["sys_info_activity_message"]; ?></p>
    <table border=0 cellpadding=0 cellspacing=0 width=100%>
      <thead>
      <tr>
        <td><b><?php echo $_lang['id']; ?></b></td>
        <td><b><?php echo $_lang['document_title']; ?></b></td>
        <td><b><?php echo $_lang["sys_info_userid"]; ?></b></td>
        <td><b><?php echo $_lang['datechanged']; ?></b></td>
      </tr>
      </thead>
      <tbody>

<?php
$sql = "SELECT id, pagetitle, editedby, editedon FROM $dbase.".$table_prefix."site_content WHERE $dbase.".$table_prefix."site_content.deleted=0 ORDER BY editedon DESC LIMIT 20";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit < 1)
{
  echo $_lang['sys_info_nothing_found']."<p />";
}
else
{
  for ($i = 0; $i < $limit; $i++)
  {
    $content = mysql_fetch_assoc($rs);
    $sql = "select username from $dbase.".$table_prefix."manager_users WHERE id=".$content['editedby'];
    $rs2 = mysql_query($sql);
    $limit2 = mysql_num_rows($rs2);
    if($limit2 != 1)
    {
      echo $_lang['sys_info_bad_number_users'];
      include_once("includes/footer.inc.php");
      exit;
    }
    $user = mysql_fetch_assoc($rs2);
    $bgcolor = ($i % 2) ? 'odd' : 'even';
    echo "<tr class=\"$bgcolor\"><td>".$content['id']."</td><td><a href='index.php?a=3&id=".$content['id']."'>".$content['pagetitle']."</a></td><td>".$user['username']."</td><td>".strftime($date_format." @ ".$time_format, $content['editedon']+$server_offset_time)."</td></tr>";
  }
}
?>

      </tbody>
    </table>
  </div>
</div>

<!-- Server Information Panel -->
<div class="tab-page" id="tabPage2">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['server_info']; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage2"));
  </script>

  <div class="sectionBody" id="lyr2">
    <p><?php echo $_lang['sys_info_eto_install_info']; ?></p>

    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td width="150"><?php echo $_lang['sys_info_eto_version'];?></td>
      <td width="20">&nbsp;</td>
      <td><b><?php echo $release.$patch_level ?>&nbsp;</b><?php echo $newversiontext; ?></td>
      </tr>
      <tr>
      <td width="150"><?php echo $_lang['sys_info_eto_codename'];?></td>
      <td width="20">&nbsp;</td>
      <td><b><?php echo $code_name ?>&nbsp;</b></td>
      </tr>
      <tr>
      <td><?php echo $_lang['sys_info_phpinfo'];?></td>
      <td>&nbsp;</td>
      <td><b><a href="index.php?a=200"><?php echo $_lang['sys_info_view'];?></a>&nbsp;</b></td>
      </tr>
      <tr>
      <td><?php echo $_lang['sys_info_acc_perms'];?></td>
      <td>&nbsp;</td>
      <td><b><?php echo $use_udperms==1 ? $_lang['sys_info_perms_enabled'] : $_lang['sys_info_perms_disabled']; ?>&nbsp;</b></td>
      </tr>
      <tr>
      <td><?php echo $_lang['sys_info_time'];?></td>
      <td>&nbsp;</td>
      <td><b><?php echo strftime($time_format, time()); ?>&nbsp;</b></td>
      </tr>
      <tr>
      <td><?php echo $_lang['sys_info_local_time'];?></td>
      <td>&nbsp;</td>
      <td><b><?php echo strftime($time_format, time()+$server_offset_time); ?>&nbsp;</b></td>
      </tr>
      <tr>
      <td><?php echo $_lang['sys_info_offset'];?></td>
      <td>&nbsp;</td>
      <td><b><?php echo $server_offset_time/(60*60) ?></b> <?php echo $_lang['sys_info_offset_text'];?></td>
      </tr>
      <tr>
      <td><?php echo $_lang['sys_info_db_name'];?></td>
      <td>&nbsp;</td>
      <td><b><?php echo $dbase ?>&nbsp;</b></td>
      </tr>
      <tr>
      <td><?php echo $_lang['sys_info_db_server'];?></td>
      <td>&nbsp;</td>
      <td><b><?php echo $database_server ?>&nbsp;</b></td>
      </tr>
      <tr>
      <td><?php echo $_lang['sys_info_db_prefix'];?></td>
      <td>&nbsp;</td>
      <td><b><?php echo $table_prefix ?>&nbsp;</b></td>
      </tr>
      <tr>
      <td><?php echo $_lang['sys_info_base_page_abs'];?></td>
      <td>&nbsp;</td>
      <td><b><?php echo $ETOMITE_PAGE_BASE['absolute'];?>&nbsp;</b></td>
      </tr>
      <tr>
      <td><?php echo $_lang['sys_info_base_page_rel'];?></td>
      <td>&nbsp;</td>
      <td><b><?php echo $ETOMITE_PAGE_BASE['relative'];?>&nbsp;</b></td>
      </tr>
      <tr>
      <td><?php echo $_lang['sys_info_base_page_www'];?></td>
      <td>&nbsp;</td>
      <td><b><?php echo $ETOMITE_PAGE_BASE['www'];?>&nbsp;</b></td>
      </tr>
      <tr>
      <td>Session Name:</td>
      <td>&nbsp;</td>
      <td><b><?php echo session_name();?>&nbsp;</b></td>
      </tr>
      <tr>
      <td>Session ID:</td>
      <td>&nbsp;</td>
      <td><b><?php echo session_id();?>&nbsp;</b></td>
      </tr>
    </table>
  </div>
</div>

<!-- Database Table Information Panel -->
<div class="tab-page" id="tabPage3">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['database_tables']; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage3"));
  </script>

  <div class="sectionBody" id="lyr4">
    <p><?php echo $_lang['sys_info_table_instructions'];?></p>
    <table border=0 cellpadding=0 cellspacing=0 width="100%">
    <thead>
    <tr>
      <td width="160"><?php echo $_lang['sys_info_table_name'];?></td>
      <td width="40" align="right"><?php echo $_lang['sys_info_records'];?></td>
      <td width="120" align="right"><?php echo $_lang['sys_info_data_size'];?></td>
      <td width="120" align="right"><?php echo $_lang['sys_info_overhead']?></td>
      <td width="120" align="right"><?php echo $_lang['sys_info_effective_size'];?></td>
      <td width="120" align="right"><?php echo $_lang['sys_info_index_size'];?></td>
      <td width="120" align="right"><?php echo $_lang['sys_info_total_size'];?></td>
    </tr>
    </thead>
    <tbody>
<?php

function nicesize($size)
{
  $a = array("B", "KB", "MB", "GB", "TB", "PB");

  $pos = 0;
  while ($size >= 1024)
  {
    $size /= 1024;
    $pos++;
  }
  if($size == 0)
  {
    return "-";
  }
  else
  {
    return round($size,2)." ".$a[$pos];
  }
}

$sql = "SHOW TABLE STATUS FROM $dbase;";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
for ($i = 0; $i < $limit; $i++)
{
  $log_status = mysql_fetch_assoc($rs);
  $bgcolor = ($i % 2) ? 'odd' : 'even';
?>

      <tr class="<?php echo $bgcolor; ?>" title="<?php echo $log_status['Comment']; ?>" style="cursor:default">
      <td class="success"><?php echo $log_status['Name']; ?></td>
      <td align="right"><?php echo $log_status['Rows']; ?></td>
      <td align="right"><?php echo nicesize($log_status['Data_length']+$log_status['Data_free']); ?></td>
      <td align="right"><?php echo $log_status['Data_free']>0 ? "<a href='index.php?a=54&t=".$log_status['Name']."'>".nicesize($log_status['Data_free'])."</a>" : "-" ; ?></td>
      <td align="right"><?php echo nicesize($log_status['Data_length']-$log_status['Data_free']); ?></td>
      <td align="right"><?php echo nicesize($log_status['Index_length']); ?></td>
      <td align="right"><?php echo nicesize($log_status['Index_length']+$log_status['Data_length']+$log_status['Data_free']); ?></td>
      </tr>

<?php
$total = $total+$log_status['Index_length']+$log_status['Data_length'];
$totaloverhead = $totaloverhead+$log_status['Data_free'];
}
?>

      <tr>
      <td valign="top"><b>Totals:</b></td>
      <td colspan="2">&nbsp;</td>
      <td align="right" valign="top"><?php echo $totaloverhead>0 ? "<b style='color:#990033'>".nicesize($totaloverhead)."</b><br>(".number_format($totaloverhead)." B)" : "-"; ?></td>
      <td colspan="2">&nbsp;</td>
      <td align="right" valign="top"><?php echo "<b>".nicesize($total)."</b><br>(".number_format($total)." B)"; ?></td>
      </tr>
      </tbody>
    </table>

<?php
if($totaloverhead>0)
{
  echo "<p>".$_lang['sys_info_table_clear']."</p>";
}
?>

  </div>
</div>

<!-- Online Users Panel -->
<div class="tab-page" id="tabPage4">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["onlineusers_title"]; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage4"));
  </script>

  <div class="sectionBody" id="lyr5">
    <p><? echo $_lang["onlineusers_message"]; ?><br />
    <b><?php echo $_lang['current_time:'].strftime($time_format, time()+$server_offset_time); ?></b></p>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <thead>
        <tr>
        <td><?php echo $_lang["onlineusers_user"];?></td>
        <td><?php echo $_lang["onlineusers_userid"];?></td>
        <td><?php echo $_lang["onlineusers_ipaddress"];?></td>
        <td><?php echo $_lang["onlineusers_lasthit"];?></td>
        <td><?php echo $_lang["onlineusers_action"];?></td>
        <td><?php echo $_lang["onlineusers_action_id"];?></td>
        </tr>
      </thead>
      <tbody>

<?php
$timetocheck = (time()-(60*20));

include_once("includes/actionlist.inc.php");

$sql = "SELECT * FROM $dbase.".$table_prefix."active_users WHERE $dbase.".$table_prefix."active_users.lasthit>$timetocheck ORDER BY username ASC";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit < 1)
{
  echo $_lang['sys_info_no_active_users'];
}
else
{
  for ($i = 0; $i < $limit; $i++)
  {
    $activeusers = mysql_fetch_assoc($rs);
    $currentaction = getAction($activeusers['action'], $activeusers['id']);
    echo "<tr><td><b>".$activeusers['username']."</b></td><td>".$activeusers['internalKey']."</td><td>".$activeusers['ip']."</td><td>".strftime($time_format, $activeusers['lasthit']+$server_offset_time)."</td><td>$currentaction</td><td align='right'>".$activeusers['action']."</td></tr>";
  }
}
?>

      </tbody>
    </table>
  </div>
</div>
</div> <!-- sysInfoPane -->
