<?php
// document_date.static.action.php
// Modified in 0.6.1 by Ralph
// Modified 2006-02-21 Code was modified to allow for undelete button functionality
// Modified 2007-03-06 See datestamped code for details
// Modified 2007-05-04 To get rid of nagging Working... message in top frame
// Modified 2007-05-07 To fix preview message display centering
// Modified 2008-03-22 [v1.0] by Ralph by Ralph to use system date|time formatting

$id = $_REQUEST['id'];
// Tree State modifications provided by Jeroen and Raymond
if (isset($_GET['opened'])) $_SESSION['openedArray'] = $_GET['opened'];

$sql = "SELECT * FROM $dbase.".$table_prefix."site_content WHERE $dbase.".$table_prefix."site_content.id = $id;";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit>1) {
  echo "Oops, something went terribly wrong...<p>";
  print "More results returned than expected. Which sucks. <p>Aborting.";
  exit;
}
$content = mysql_fetch_assoc($rs);

$createdby = $content['createdby'];
$sql = "SELECT username FROM $dbase.".$table_prefix."manager_users WHERE id=$createdby;";
$rs = mysql_query($sql);

$row=mysql_fetch_assoc($rs);
$createdbyname = $row['username'];

$editedby = $content['editedby'];
$sql = "SELECT username FROM $dbase.".$table_prefix."manager_users WHERE id=$editedby;";
$rs = mysql_query($sql);

$row=mysql_fetch_assoc($rs);
$editedbyname = $row['username'];

$templateid = $content['template'];
$sql = "SELECT templatename FROM $dbase.".$table_prefix."site_templates WHERE id=$templateid;";
$rs = mysql_query($sql);

$row=mysql_fetch_assoc($rs);
$templatename = $row['templatename'];

$_SESSION['itemname']=$content['pagetitle'];

// keywords stuff, by stevew (thanks Steve!)
$sql = "SELECT k.keyword FROM $dbase.".$table_prefix."site_keywords as k, $dbase.".$table_prefix."keyword_xref as x WHERE k.id = x.keyword_id AND x.content_id = $id ORDER BY k.keyword ASC";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit > 0) {
  for($i=0;$i<$limit;$i++) {
    $row = mysql_fetch_assoc($rs);
    $keywords[$i] = $row['keyword'];
  }
} else {
  $keywords = array();
}
// end keywords stuff
?>

<script type="text/javascript">
function deletedocument() {
  if(confirm("<?php echo $_lang['confirm_delete_document'] ?>")==true) {
    document.location.href="index.php?id=<?php echo $_REQUEST['id']; ?>&a=6";
  }
}
function undeletedocument() {
  if(confirm("<?php echo $_lang['confirm_undelete'] ?>")==true) {
    document.location.href="index.php?id=<?php echo $_REQUEST['id']; ?>&a=63";
  }
}
function editdocument() {
    document.location.href="index.php?id=<?php echo $_REQUEST['id']; ?>&a=27";
}
function movedocument() {
    document.location.href="index.php?id=<?php echo $_REQUEST['id']; ?>&a=51";
}
</script>

<!-- Display subtitle and action buttons -->
<div class="subTitle">

  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <?php echo $site_name ;?> - <?php echo $_lang["doc_data_title"]; ?>
  </span>
</div>

<div id="documentTitle"><span class="docTitleButtons">
    <a href="index.php?id=<?php echo $_REQUEST['id']; ?>&a=27" class="doSomethingButton"><?php echo $_lang["edit"]; ?></a>
    <a href="index.php?id=<?php echo $_REQUEST['id']; ?>&a=51" class="doSomethingButton"><?php echo $_lang["move"]; ?></a>
    <a href="#" onClick="deletedocument();" class="doSomethingButton"><?php echo $_lang["delete"]; ?></a>
    <?php if($content['deleted']) { ?>
    <a href="#" onClick="undeletedocument();" class="doSomethingButton"><?php echo $_lang["undelete"]; ?></a>
    <?php } ?>
  </span><?php echo $content['pagetitle']."&nbsp;&nbsp;(".$id.")"; ?>

</div>

<!-- Load tabpane related scripting -->
<script type="text/javascript">
function checkIM() {
  im_on = document.settings.im_plugin[0].checked; // check if im_plugin is on
  if(im_on==true) {
    showHide(/imRow/, 1);
  }
}

function showHide(what, onoff){

  var all = document.getElementsByTagName( "*" );
  var l = all.length;
  var buttonRe = what;
  var id, el, stylevar;

  if(onoff==1) {
    stylevar = "<?php echo $displayStyle; ?>";
  } else {
    stylevar = "none";
  }

  for ( var i = 0; i < l; i++ ) {
    el = all[i]
    id = el.id;
    if ( id == "" ) continue;
    if (buttonRe.test(id)) {
      el.style.display = stylevar;
    }
  }
}
</script>

<?php
// Added 0614
// This resizes the editarea js code editor
// So it fills the available view port
?>
<script type="text/javascript">
// 2007/03/09 ~sl - Dynamically Resize textarea

// handle onload
var nowOnload = window.onload; // save any existing assignment
window.onload = function () {
resizer();
if(nowOnload != null && typeof(nowOnload) == 'function') {
nowOnload();
}
}

// handle browser resize
onresize=resizer;

function resizer() {
scrollTo(0,0);
var x = 42 // allowance
var y = window.innerHeight ? window.innerHeight : document.body.clientHeight;
var t = document.getElementById("preview"); // the iframe
var o = y-findTop(t)-x;
t.style.height=Math.max(1,o);
}

function findTop(obj) {
curtop = 0;
if (obj.offsetParent) {
curtop = obj.offsetTop
while (obj = obj.offsetParent) {
curtop += obj.offsetTop
}
}
return curtop;
}
</script>


<link type="text/css" rel="stylesheet" href="media/style/tabs.css" />
<script type="text/javascript" src="media/script/tabpane.js"></script>

<div class="tab-pane" id="docInfoPane">

<script type="text/javascript">
  tpSettings = new WebFXTabPane(document.getElementById("docInfoPane"),0);
</script>

<!-- Document Preview -->
<div class="tab-page" id="tabPage1">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["preview"]; ?>
  </div>
    <script type="text/javascript">
      tpSettings.addTabPage(document.getElementById("tabPage1"));
    </script>
  <div class="sectionBody">
  <?php if($content['published']==1 && $show_doc_data_preview==1) { ?>
	<iframe id='preview' src="../index.php?id=<?php echo $id; ?>&z=manprev" frameborder="0" border="0" style="width: 100%; height: 400px; border: 3px solid #4791C5;">
  </iframe>
  <?php } else { ?>
    <div class="noSymbolWrapper">
      <div class="noSymbolPage">
        <div class="noSymbolText">
          <?php
            // Start::2007-03-06 - Ralph - Fixed message display logic
            if($show_doc_data_preview==0)
            {
              echo $_lang['preview_disabled'];
            }
            elseif($content['published']==0)
            {
              echo $_lang['no_preview'];
            }
            // End::2007-03-06 - Ralph - Fixed message display logic
          ?>
        </div>
      </div>
    </div>
  <?php } ?>
  </div>
</div>

<!-- Document General Information -->
<div class="tab-page" id="tabPage2">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["page_data_title"]; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage2"));
  </script>

  <div class="sectionBody" id="lyr1">

  <table width="600" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td colspan="2"><b><?php echo $_lang["page_data_general"]; ?></b></td>
    </tr>
    <tr>
      <td width="200" valign="top">ID: </td>
      <td><b><?php echo $content['id']; ?></b></td>
    </tr>
    <tr>
      <td width="200" valign="top"><?php echo $_lang["document_title"]; ?>: </td>
      <td><b><?php echo $content['pagetitle']; ?></b></td>
    </tr>
    <tr>
      <td width="200" valign="top"><?php echo $_lang["long_title"]; ?>: </td>
      <td><small><?php echo $content['longtitle']!='' ? $content['longtitle'] : "(<i>".$_lang["notset"]."</i>)" ; ?></small></td>
    </tr>
    <tr>
      <td valign="top"><?php echo $_lang["document_description"]; ?>: </td>
      <td><?php echo $content['description']!='' ? $content['description'] : "(<i>".$_lang["notset"]."</i>)" ; ?></td>
    </tr>
    <tr>
      <td valign="top"><?php echo $_lang["type"]; ?>: </td>
      <td><?php echo $content['type']=='reference' ? $_lang['weblink'] : $_lang['document'] ; ?></td>
    </tr>
    <tr>
      <td valign="top"><?php echo $_lang["document_alias"]; ?>: </td>
      <td><?php echo $content['alias']!='' ? $content['alias'] : "(<i>".$_lang["notset"]."</i>)" ; ?></td>
    </tr>
      <tr>
      <td valign="top"><?php echo $_lang['keywords']; ?>: </td>
    <td>
      <?php
        if(count($keywords) != 0) {
          echo join($keywords, ", ");
        } else {
          echo "(<i>".$_lang['notset']."</i>)";
        }
      ?>
    </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2"><b><?php echo $_lang["page_data_changes"]; ?></b></td>
    </tr>
    <tr>
      <td><?php echo $_lang["page_data_created"]; ?>: </td>
      <td><?php echo strftime($date_format." %H:%M:%S", $content['createdon']+$server_offset_time); ?> (<b><?php echo $createdbyname ?></b>)</td>
    </tr>
  <?php
  if($editedbyname!='') {
  ?>
    <tr>
      <td><?php echo $_lang["page_data_edited"]; ?>: </td>
      <td><?php echo strftime($date_format." %H:%M:%S", $content['editedon']+$server_offset_time); ?> (<b><?php echo $editedbyname ?></b>)</td>
    </tr>
  <?php
  }
  ?>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2"><b><?php echo $_lang["page_data_status"]; ?></b></td>
    </tr>
    <tr>
      <td><?php echo $_lang["page_data_status"]; ?>: </td>
    <td><?php echo $content['published']==0 ? "<b style='color: #821517'>".$_lang['page_data_unpublished']."</b>" : "<b style='color: #006600'>".$_lang['page_data_published']."</b>"; ?></td>
    </tr>
    <tr>
      <td><?php echo $_lang["page_data_publishdate"]; ?>: </td>
    <td><?php echo $content['pub_date']==0 ? "(<i>".$_lang["notset"]."</i>)" : strftime($date_format." %H:%M:%S", $content['pub_date']); ?></td>
    </tr>
    <tr>
      <td><?php echo $_lang["page_data_unpublishdate"]; ?>: </td>
    <td><?php echo $content['unpub_date']==0 ? "(<i>".$_lang["notset"]."</i>)" : strftime($date_format." %H:%M:%S", $content['unpub_date']); ?></td>
    </tr>
    <tr>
      <td><?php echo $_lang["page_data_cacheable"]; ?>: </td>
    <td><?php echo $content['cacheable']==0 ? $_lang['no'] : $_lang['yes']; ?></td>
    </tr>
    <tr>
      <td><?php echo $_lang["page_data_searchable"]; ?>: </td>
    <td><?php echo $content['searchable']==0 ? $_lang['no'] : $_lang['yes']; ?></td>
    </tr>
      <tr>
      <td><?php echo $_lang['document_opt_menu_index']; ?>: </td>
    <td><?php echo $content['menuindex']; ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2"><b><?php echo $_lang["page_data_markup"]; ?></b></td>
    </tr>
    <tr>
      <td><?php echo $_lang["page_data_template"]; ?>: </td>
    <td><?php echo $templatename ?></td>
    </tr>
    <tr>
      <td><?php echo $_lang["page_data_editor"]; ?>: </td>
    <td><?php echo $content['richtext']==0 ? $_lang['no'] : $_lang['yes']; ?></td>
    </tr>
    <tr>
      <td><?php echo $_lang["page_data_folder"]; ?>: </td>
    <td><?php echo $content['isfolder']==0 ? $_lang['no'] : $_lang['yes']; ?></td>
    </tr>
  </table>

  <?php
  if($track_visitors==1) {

    $day      = date('j');
    $month    = date('n');
    $year     = date('Y');

      $monthStart = mktime(0,   0,  0, $month, 1, $year);
      $monthEnd   = mktime(23, 59, 59, $month, date('t', $monthStart), $year);

      $dayStart = mktime(0,   0,  0, $month, $day, $year);
      $dayEnd   = mktime(23, 59, 59, $month, $day, $year);

    // get page impressions for today
    $tbl = "$dbase.".$table_prefix."log_access";
    $sql = "SELECT COUNT(*) FROM $tbl WHERE timestamp > '".$dayStart."' AND timestamp < '".$dayEnd."' AND document='".$id."'";
    $rs = mysql_query($sql);
    $tmp = mysql_fetch_assoc($rs);
    $piDay = $tmp['COUNT(*)'];

    // get page impressions for this month
    $tbl = "$dbase.".$table_prefix."log_access";
    $sql = "SELECT COUNT(*) FROM $tbl WHERE timestamp > '".$monthStart."' AND timestamp < '".$monthEnd."' AND document='".$id."'";
    $rs = mysql_query($sql);
    $tmp = mysql_fetch_assoc($rs);
    $piMonth = $tmp['COUNT(*)'];

    // get page impressions for all time
    $tbl = "$dbase.".$table_prefix."log_access";
    $sql = "SELECT COUNT(*) FROM $tbl WHERE document='".$id."'";
    $rs = mysql_query($sql);
    $tmp = mysql_fetch_assoc($rs);
    $piAll = $tmp['COUNT(*)'];

    // get visitors for today
    $tbl = "$dbase.".$table_prefix."log_access";
    $sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tbl WHERE timestamp > '".$dayStart."' AND timestamp < '".$dayEnd."' AND document='".$id."'";
    $rs = mysql_query($sql);
    $tmp = mysql_fetch_assoc($rs);
    $visDay = $tmp['COUNT(DISTINCT(visitor))'];

    // get visitors for this month
    $tbl = "$dbase.".$table_prefix."log_access";
    $sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tbl WHERE timestamp > '".$monthStart."' AND timestamp < '".$monthEnd."' AND document='".$id."'";
    $rs = mysql_query($sql);
    $tmp = mysql_fetch_assoc($rs);
    $visMonth = $tmp['COUNT(DISTINCT(visitor))'];

    // get visitors for all time
    $tbl = "$dbase.".$table_prefix."log_access";
    $sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tbl WHERE document='".$id."'";
    $rs = mysql_query($sql);
    $tmp = mysql_fetch_assoc($rs);
    $visAll = $tmp['COUNT(DISTINCT(visitor))'];
  ?>

  <?php echo $_lang["document_visitor_stats"]; ?>

  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#000000">
  <thead>
    <tr>
      <td width="33%">&nbsp;</td>
      <td align="right" width="33%"><b><?php echo $_lang['visitors']; ?></b></td>
      <td align="right" width="33%"><b><?php echo $_lang['page_impressions']; ?></b></td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td align="right" class='row3'><?php echo $_lang['today']; ?></td>
      <td align="right" class='row1'><?php echo number_format($visDay); ?></td>
      <td align="right" class='row1'><?php echo number_format($piDay); ?></td>
    </tr>
    <tr>
      <td align="right" class='row3'><?php echo $_lang['this_month']; ?></td>
      <td align="right" class='row1'><?php echo number_format($visMonth); ?></td>
      <td align="right" class='row1'><?php echo number_format($piMonth); ?></td>
    </tr>
    <tr>
      <td align="right" class='row3'><?php echo $_lang['all_time']; ?></td>
      <td align="right" class='row1'><?php echo number_format($visAll); ?></td>
      <td align="right" class='row1'><?php echo number_format($piAll); ?></td>
    </tr>
  </tbody>
  </table>

  <?php
  } else {
  echo $_lang['no_stats_message'];
  }
  ?>
  </div>
</div>

<!-- View Document Source (cached doc's only) -->
<div class="tab-page" id="tabPage3">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["page_data_source"]; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage3"));
  </script>

  <div class="sectionBody">
  <?php
  $buffer = "";
  $filename = "../assets/cache/docid_".$id.".etoCache";
  $handle = @fopen($filename, "r");
  if(!$handle) {
    $buffer = "<div class=\"noSymbolPage\"><div class=\"noSymbolText\">".$_lang['page_data_notcached']."</div></div>";
  } else {
    while (!feof($handle)) {
      $buffer .= fgets($handle, 4096);
    }
    fclose ($handle);
	$buffer=$_lang['page_data_cached']."<p><textarea style='width: 100%; border: 3px solid #4791C5;'>".htmlspecialchars($buffer)."</textarea>";
  }

  echo $buffer;
  ?>
  </div>
</div>

<script type="text/javascript">
try {
  top.menu.Sync(<?php echo $id; ?>);
} catch(oException) {
  xyy=window.setTimeout("loadagain(<?php echo $id; ?>)", 1000);
  // get rid of nagging Working... message
  top.topFrame.document.getElementById('workText').innerHTML="";
}
</script>
