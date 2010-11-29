<?php
// welcome.static.action.php
// Creates initial welcome section when manager starts
// Modified in 0.6.1 by Ralph
// Modified 2008-04-18 [v1.0] by Ralph Dahlgren
// Modified 2008-05-10 [v1.1] by Ralph Dahlgren
// - HTML markup improvements

if(IN_ETOMITE_SYSTEM != "true")
{
  die($_lang["include_ordering_error"]);
}

unset($_SESSION['itemname']); // clear this, because it's only set for logging purposes

// if this is a new install send the user to the configuration page
if(!isset($settings_version) || $settings_version != $release)
{
  echo "<script type=\"text/javascript\">document.location.href=\"index.php?a=17\";</script>";
  exit;
}

// do some config checks
include_once("includes/config_check.inc.php");
?>

<div class="subTitle">
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5" /><br />
    <?php echo $site_name ;?>&nbsp;-&nbsp;
    <?php echo $_lang["home"]; ?>
  </span>
</div>

<script type="text/javascript">
function checkIM()
{
  im_on = document.settings.im_plugin[0].checked; // check if im_plugin is on
  if(im_on==true)
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

  for( var i = 0; i < l; i++ )
  {
    el = all[i]
    id = el.id;
    if(id == "") continue;
    if(buttonRe.test(id))
    {
      el.style.display = stylevar;
    }
  }
}
</script>

<!--<link type="text/css" rel="stylesheet" href="media/style/tabs.css" />-->
<script type="text/javascript" src="media/script/tabpane.js"></script>

<div class="tab-pane" id="welcomePane">

<script type="text/javascript">
  tpSettings = new WebFXTabPane(document.getElementById("welcomePane"));
</script>

<!-- Etomite Welcome Panel -->
<div class="tab-page" id="tabPage1">
  <div class="tab">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["welcome_title"]; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage1"));
  </script>

  <div class="sectionBody">
    <table border="0" cellpadding="5" wdith="100%">
      <tr>
        <td width="10%" align="right">
          <img src='./media/images/misc/logo.gif' alt='<?php echo $_lang["etomite_slogan"]; ?>' /><br />
          <a href='http://www.etomite.com/downloads.html' title='v: <?php echo $release; ?>'><img src='http://www.etomite.com/status/<?php echo $release; ?>.gif' title='v: <?php echo $release; ?>' alt='v: <?php echo $release; ?>' style='border:0px;' /></a>
      </td>
      <td valign="top">
        <p><b><?php echo $_lang["welcome_title"]; ?></b><br /><?php echo $_lang["welcome_message"]; ?></p>

<?php
if($track_visitors == 1)
{
  $day      = date('j');
  $month    = date('n');
  $year     = date('Y');

  $monthStart = mktime(0,   0,  0, $month, 1, $year);
  $monthEnd   = mktime(23, 59, 59, $month, date('t', $monthStart), $year);

  $dayStart = mktime(0,   0,  0, $month, $day, $year);
  $dayEnd   = mktime(23, 59, 59, $month, $day, $year);

  // get page impressions for today
  $tbl = "$dbase.".$table_prefix."log_access";
  $sql = "SELECT COUNT(*) FROM $tbl WHERE timestamp > '".$dayStart."' AND timestamp < '".$dayEnd."'";
  $rs = mysql_query($sql);
  $tmp = mysql_fetch_assoc($rs);
  $piDay = $tmp['COUNT(*)'];

  // get page impressions for this month
  $tbl = "$dbase.".$table_prefix."log_access";
  $sql = "SELECT COUNT(*) FROM $tbl WHERE timestamp > '".$monthStart."' AND timestamp < '".$monthEnd."'";
  $rs = mysql_query($sql);
  $tmp = mysql_fetch_assoc($rs);
  $piMonth = $tmp['COUNT(*)'];

  // get page impressions for all time
  $tbl = "$dbase.".$table_prefix."log_access";
  $sql = "SELECT COUNT(*) FROM $tbl";
  $rs = mysql_query($sql);
  $tmp = mysql_fetch_assoc($rs);
  $piAll = $tmp['COUNT(*)'];

  // get visits for today
  $tbl = "$dbase.".$table_prefix."log_access";
  $sql = "SELECT COUNT(*) FROM $tbl WHERE timestamp > '".$dayStart."' AND timestamp < '".$dayEnd."' AND entry='1'";
  $rs = mysql_query($sql);
  $tmp = mysql_fetch_assoc($rs);
  $viDay = $tmp['COUNT(*)'];

  // get visits for this month
  $tbl = "$dbase.".$table_prefix."log_access";
  $sql = "SELECT COUNT(*) FROM $tbl WHERE timestamp > '".$monthStart."' AND timestamp < '".$monthEnd."' AND entry='1'";
  $rs = mysql_query($sql);
  $tmp = mysql_fetch_assoc($rs);
  $viMonth = $tmp['COUNT(*)'];

  // get visits for all time
  $tbl = "$dbase.".$table_prefix."log_access WHERE entry='1'";
  $sql = "SELECT COUNT(*) FROM $tbl";
  $rs = mysql_query($sql);
  $tmp = mysql_fetch_assoc($rs);
  $viAll = $tmp['COUNT(*)'];

  // get visitors for today
  $tbl = "$dbase.".$table_prefix."log_access";
  $sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tbl WHERE timestamp > '".$dayStart."' AND timestamp < '".$dayEnd."'";
  $rs = mysql_query($sql);
  $tmp = mysql_fetch_assoc($rs);
  $visDay = $tmp['COUNT(DISTINCT(visitor))'];

  // get visitors for this month
  $tbl = "$dbase.".$table_prefix."log_access";
  $sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tbl WHERE timestamp > '".$monthStart."' AND timestamp < '".$monthEnd."'";
  $rs = mysql_query($sql);
  $tmp = mysql_fetch_assoc($rs);
  $visMonth = $tmp['COUNT(DISTINCT(visitor))'];

  // get visitors for all time
  $tbl = "$dbase.".$table_prefix."log_access";
  $sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tbl";
  $rs = mysql_query($sql);
  $tmp = mysql_fetch_assoc($rs);
  $visAll = $tmp['COUNT(DISTINCT(visitor))'];

  $statMsg = $_lang['welcome_visitor_stats'];
}
else
{
  $statMsg = $_lang['no_stats_message'];
}
echo '<span class="menuHeader">'.$statMsg.'</span>';
?>

    </td>
    </tr>

<?php if($_SESSION['permissions']['messages']==1) { ?>

    <tr>
      <td colspan="2">
        <i><a href="index.php?a=10"><img src="media/images/icons/messages.gif" align="absmiddle" border=0>
        </a>&nbsp;<?php printf($_lang["welcome_messages"], $_SESSION['nrtotalmessages'], $_SESSION['nrnewmessages']); ?></i>
      </td>
    </tr>

<?php } ?>

  </table>
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#000000">
  <thead>
    <tr>
      <td width="25%">&nbsp;</td>
      <td align="right" width="25%"><?php echo $_lang['visitors']; ?></td>
      <td align="right" width="25%"><?php echo $_lang['visits']; ?></td>
      <td align="right" width="25%"><?php echo $_lang['page_impressions']; ?></td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td align="right" class='row3'><?php echo $_lang['today']; ?></td>
      <td align="right" class='row1'><?php echo number_format($visDay); ?></td>
      <td align="right" class='row1'><?php echo number_format($viDay); ?></td>
      <td align="right" class='row1'><?php echo number_format($piDay); ?></td>
    </tr>
    <tr>
      <td align="right" class='row3'><?php echo $_lang['this_month']; ?></td>
      <td align="right" class='row1'><?php echo number_format($visMonth); ?></td>
      <td align="right" class='row1'><?php echo number_format($viMonth); ?></td>
      <td align="right" class='row1'><?php echo number_format($piMonth); ?></td>
    </tr>
    <tr>
      <td align="right" class='row3'><?php echo $_lang['all_time']; ?></td>
      <td align="right" class='row1'><?php echo number_format($visAll); ?></td>
      <td align="right" class='row1'><?php echo number_format($viAll); ?></td>
      <td align="right" class='row1'><?php echo number_format($piAll); ?></td>
    </tr>
  </tbody>
  </table>

  </div>
</div>

<!-- Users Recent Document Activity -->
<div class="tab-page" id="tabPage2">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["activity_title"]; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage2"));
  </script>

  <div class="sectionBody">

<?php
$sql = "SELECT id, pagetitle, description FROM $dbase.".$table_prefix."site_content WHERE $dbase.".$table_prefix."site_content.deleted=0 AND ($dbase.".$table_prefix."site_content.editedby=".$_SESSION['internalKey']." OR $dbase.".$table_prefix."site_content.createdby=".$_SESSION['internalKey'].") ORDER BY editedon DESC LIMIT ".$settings['top_howmany'].";";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
$activity = ($limit<1) ? $_lang["no_activity_message"] : $_lang["activity_message"];
?>

  <div class="menuHeader"><?php echo $activity; ?></div>
  <br />
  <table>

<?php
for ($i = 0; $i < $limit; $i++):
  $content = mysql_fetch_assoc($rs);
  if($i == 0) $syncid = $content['id'];
?>

  <tr style="padding:0; height:auto; margin:0; padding:0; border:none; ">
    <td style="vertical-align:top;">
      <img src="media/images/misc/li.gif">
    </td>
    <td style="text-align:right; vertical-align:top; margin:0; padding:0 4px 0 4px; border:none;">
      <?php echo $content['id']; ?>
    </td>
    <td style="vertical-align:top;"><a href='index.php?a=3&id=<?php echo $content['id']; ?>'><?php echo $content['pagetitle']; ?></a>
      <span style="vertical-align:top;">&nbsp;-&nbsp;<?php echo $content['description']!='' ? $content['description'] : ''; ?></span>
    </td>
  </tr>

<?php endfor; ?>

  </table>
  </div>
</div>

<!-- Your Info Section -->
<div class="tab-page" id="tabPage3">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["yourinfo_title"]; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage3"));
  </script>

  <div class="sectionBody">
    <div class="menuHeader"><?php echo $_lang["yourinfo_message"]; ?></div>
    <br />
    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="150"><?php echo $_lang["yourinfo_username"]; ?></td>
        <td width="20">&nbsp;</td>
        <td><b><?php echo $_SESSION['shortname']; ?></b></td>
      </tr>
      <tr>
        <td><?php echo $_lang['user_full_name']; ?></td>
        <td>&nbsp;</td>
        <td><b><?php echo $_SESSION['fullname']; ?></b></td>
      </tr>
      <tr>
        <td><?php echo $_lang["yourinfo_role"]; ?></td>
        <td>&nbsp;</td>
        <td><b><?php echo $_SESSION['permissions']['name']; ?></b></td>
      </tr>
      <tr>
        <td><?php echo $_lang["document_description"]; ?>:</td>
        <td>&nbsp;</td>
        <td><b><?php echo $_SESSION['permissions']['description']; ?></b></td>
      </tr>
      <tr>
        <td><?php echo $_lang["yourinfo_previous_login"]; ?></td>
        <td>&nbsp;</td>
        <td><b><?php echo strftime($date_format.' @ '.$time_format, $_SESSION['lastlogin']+$server_offset_time); ?></b></td>
      </tr>
      <tr>
        <td><?php echo $_lang["your_ip_address"]; ?></td>
        <td>&nbsp;</td>
        <td><b><?php echo $_SESSION['ip']; ?></b></td>
      </tr>
      <tr>
        <td><?php echo $_lang["yourinfo_total_logins"]; ?></td>
        <td>&nbsp;</td>
        <td><b><?php echo $_SESSION['nrlogins']+1; ?></b></td>
      </tr>
    </table>
  </div>
</div>

<!-- Users Online Details -->
<div class="tab-page" id="tabPage4">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["onlineusers_title"]; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage4"));
  </script>

  <div class="sectionBody">
    <div class="menuHeader">
      <?php echo $_lang["onlineusers_message"]."&nbsp;".strftime($time_format, time()+$server_offset_time); ?>
    </div>
    <br />
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <thead>
        <tr>
          <td><b><?php echo $_lang["onlineusers_user"]; ?></b></td>
          <td><b><?php echo $_lang["onlineusers_userid"]; ?></b></td>
          <td><b><?php echo $_lang["onlineusers_ipaddress"]; ?></b></td>
          <td><b><?php echo $_lang["onlineusers_lasthit"]; ?></b></td>
          <td><b><?php echo $_lang["onlineusers_action"]; ?></b></td>
        </tr>
      </thead>
      <tbody>

<?php
$timetocheck = (time()-(60*20));//+$server_offset_time;
include_once("includes/actionlist.inc.php");
$sql = "SELECT * FROM $dbase.".$table_prefix."active_users WHERE $dbase.".$table_prefix."active_users.lasthit>'$timetocheck' ORDER BY username ASC";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit < 1)
{
  echo "No active users found.<br />";
}
else
{
  for($i = 0; $i < $limit; $i++)
  {
    $activeusers = mysql_fetch_assoc($rs);
    $currentaction = getAction($activeusers['action'], $activeusers['id']);
    echo "<tr><td><b>".$activeusers['username']."</td><td>".$activeusers['internalKey']."</td><td></b>".$activeusers['ip']."</td><td>".strftime($time_format, $activeusers['lasthit']+$server_offset_time)."</td><td>$currentaction</td></tr>";
  }
}
?>

    </tbody>
    </table>
  </div>
</div>

<!-- Check Configuration Status -->
<div class="tab-page" id="tabPage5">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["configcheck_title"]; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage5"));
  </script>

  <div class="sectionBody">
    <div class="menuHeader"><?php echo $config_check_results; ?></div>
  </div>
</div>

<script type="text/javascript">
try
{
  top.menu.Sync(<?php echo $syncid; ?>);
}
catch(oException)
{
  xyy=window.setTimeout("loadagain(<?php echo $syncid; ?>)", 1000);
}
</script>

</div>

<?php
//  Debug code for displaying helpful cookie and session information during development
$debug = false;
if($debug == true)
{
  $sessionid = session_id();
  $sessionname = session_name();

  echo "<center>
  Current Session ID: $sessionid<br />
  Current Session Name: $sessionname<br />
  </center>
  ";

  print_r($_COOKIE);
  echo "<br /><br />";
  print_r($_SESSION);
}
?>
