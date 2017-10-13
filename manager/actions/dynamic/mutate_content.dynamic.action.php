<?php
// mutate_content.dynamic.action.php
// Modified 2007-05-07 For better TinyMCE integration
// Modified 2007-11-15 [0615] by Ralph for modified createdon date functionality
// Modified 2007_11_18 by Cris D to remove editor integration to includes() files
// Modified 2008-04-28 [v1.0] by Ralph (too many to list)
// Modified 2008-05-08 by Ralph to fix minor bugs
// Modified 2008-06-13 by Petr Vaněk aka krteczek to added Texyla

// START::Date and Time format information
// $date_format = "%Y-%m-%d"; // stored in site_settings
// $time_format = "%I:%M %p"; // stored in site_settings
// determine whether jscalendarshould return 12 or 24 hour times
$time_hours = (substr($time_format,-2) == "%p") ? 12 : 24;
// tell jscalendar what separator to return between the date and time
$sep = " @ ";
// END::Date and Time format information

/* BEGIN AJAX RESPONSE FOR ALIAS UNIQUE CHECK */

if(isset($_POST['aliasUniqueCheck']))
{
  if(preg_match('/[^\w-]/',$_POST['aliasUniqueCheck'])) exit(0);
  $alias = $_POST['aliasUniqueCheck'];
  $cwd = getcwd();
  include($cwd.'/../../includes/config.inc.php');

  $handle = mysql_connect($database_server, $database_user, $database_password)or die('Could not connect: ' . mysql_error());
  mysql_select_db(str_replace("`","",$dbase)) or die('Could not select database');

  $aliassql = "SELECT alias FROM $dbase.".$table_prefix."site_content WHERE $dbase.".$table_prefix."site_content.alias = \"" . $alias . "\" LIMIT 1";
  $aliasrs = mysql_query($aliassql);
  if(!$aliasrs)
  {
    echo "The following SQL query failed: " . $aliassql;
    exit(0);
  }
  $alreadyInDb = mysql_num_rows($aliasrs);
  echo "<script language=\"javascript\">if(document.getElementById('aliasbox') && document.getElementById('aliasUniqueMessage') && document.mutate.alias.value == '" . $alias . "') {";
  if($alreadyInDb<1)
  {
    echo "document.getElementById('aliasbox').style.color='';";
    echo "document.getElementById('aliasUniqueMessage').style.visibility = 'hidden';";
    echo "aliasUniqueChecked=true; aliasUnique=true;}</script>";
  }
  else
  {
    echo "document.getElementById('aliasbox').style.color='red';";
    echo "document.getElementById('aliasUniqueMessage').style.visibility = 'visible';";
    echo "aliasUniqueChecked=true; aliasUnique=false;}</script>";
  }
  exit(0);
}

/* END AJAX RESPONSE FOR ALIAS UNIQUE CHECK */

?>

<!-- START::HTML head section -->
<style>
  body { overflow-x : hidden; /* stupid hack for equally stupid MSIE */ }
</style>

<script type="text/javascript" src="frames/scriptaculous/prototype.js" ></script>
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="media/jscalendar-1.0/calendar-etomite.css" title="etomite"/>
<!-- main calendar program -->
<script type="text/javascript" src="media/jscalendar-1.0/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="media/jscalendar-1.0/lang/calendar-en.js"></script>
<!-- the following script defines the Calendar.setup helper function, which makes
    adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="media/jscalendar-1.0/calendar-setup.js"></script>
<!-- END::HTML head section -->

<?php

if(IN_ETOMITE_SYSTEM!="true")
die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />
Please use the Etomite Manager instead of accessing this file directly.");

if($_SESSION['permissions']['edit_document']!=1 && ($_REQUEST['a']==27 || $_REQUEST['a']==4 || $_REQUEST['a']==72))
{
  $e->setError(3);
  $e->dumpError();
}

function isNumber($var)
{
  if(strlen($var)==0) return false;
  for($i=0;$i<strlen($var);$i++)
  {
    if(substr_count ("0123456789", substr ($var, $i, 1) ) == 0) return false;
  }
  return true;
}

if(!isset($_REQUEST['id']))
{
  $id=0;
}
else
{
  $id = $_REQUEST['id'];
}

// make sure the id's a number
if(!isNumber($id))
{
  $e->setError(4);
  $e->dumpError();
}

if($action==27 )
{ //editing an existing document

  // check permissions on the document
  include_once("./processors/user_documents_permissions.class.php");
  $udperms = new udperms();
  $udperms->user = $_SESSION['internalKey'];
  $udperms->document = $id;
  $udperms->role = $_SESSION['role'];

  if(!$udperms->checkPermissions())
  {
?>

    <br /><br />
    <div class="sectionHeader">
      <img src='./media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['access_permissions']; ?>
    </div>

    <!-- <div class="sectionBody"> -->
    <div class="sectionBody" style="padding:0px; border:0px;">
      <p><?php echo $_lang['access_permission_denied']; ?></p>

<?php
    include("./includes/footer.inc.php");
    exit;
  }
}
else
{ // new document, check the user is allowed to create a document here
  // check permissions on the parent of this document
  include_once("./processors/user_documents_permissions.class.php");
  $udperms = new udperms();
  $udperms->user = $_SESSION['internalKey'];
  $udperms->document = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : 0 ;
  $udperms->role = $_SESSION['role'];

  if(!$udperms->checkPermissions())
  {
?>

    <br /><br />
    <div class="sectionHeader">
      <img src='./media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['access_permissions']; ?>
    </div>
    <div class="sectionBody">
      <p><?php echo $_lang['access_permission_denied']; ?></p>
    </div>

<?php
  include("./includes/footer.inc.php");
  exit;
  }
}

// check to see the document isn't locked
$sql = "SELECT internalKey, username FROM $dbase.".$table_prefix."active_users WHERE $dbase.".$table_prefix."active_users.action=27 AND $dbase.".$table_prefix."active_users.id=$id";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit > 1)
{
  for ($i=0; $i < $limit; $i++)
  {
    $lock = mysql_fetch_assoc($rs);
    if($lock['internalKey'] != $_SESSION['internalKey'])
    {
      $msg = "The document is currently being edited by ".$lock['username']." and cannot be opened.";
      $e->setError(5, $msg);
      $e->dumpError();
    }
  }
}
// end check for lock

// get the entire document record
if(isset($_GET['id']))
{
  $sql = "SELECT * FROM $dbase.".$table_prefix."site_content WHERE $dbase.".$table_prefix."site_content.id = $id;";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  if($limit > 1)
  {
    $e->setError(6);
    $e->dumpError();
  }
  if($limit < 1)
  {
    $e->setError(7);
    $e->dumpError();
  }
  $content = mysql_fetch_assoc($rs);
}
else
{
  $content = array();
}

// get list of site keywords, code by stevew!
$sql = "SELECT * FROM $dbase.".$table_prefix."site_keywords ORDER BY keyword;";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit > 0)
{
  for($i=0; $i < $limit; $i++)
  {
    $row = mysql_fetch_assoc($rs);
    $keywords[$row['id']] = $row['keyword'];
  }
}
else
{
  $keywords = array();
}

// get ids of this documents selected keywords
if(isset($content['id']) && count($keywords) > 0)
{
  $sql = "SELECT keyword_id FROM $dbase.".$table_prefix."keyword_xref WHERE content_id = ".$content['id'];
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  if($limit > 0)
  {
    for($i=0; $i < $limit; $i++)
    {
      $row = mysql_fetch_assoc($rs);
      $keywords_selected[$row['keyword_id']] = "selected";
    }
  }
  else
  {
    $keywords_selected = array();
  }
}

?>

<script type="text/javascript">
function winOpen(url,width,height)
{
  var ManualWindow;
  if (!ManualWindow || ManualWindow.closed)
  {
    ManualWindow = window.open(url,'ManualWindow',"toolbar=1,location=1,directories=1,status=1," +
    "menubar=1,scrollbars=1,resizable=yes,width=" + width + ",height=" + height +
    ",screenX=200,screenY=200");

    if (!ManualWindow.opener)
    {
      ManualWindow.opener = window
    }
  }
  else
  {
    ManualWindow.location.href = url;
    self.ManualWindow.focus();
  }
}

function changestate(element)
{
  currval = eval(element).value;
  if(currval==1) {
    eval(element).value=0;
  } else {
    eval(element).value=1;
  }
}

function gotoKeywords()
{
  if(confirm("<?php echo $_lang['confirm_goto_keywords']; ?>")==true)
  {
    WebFXTabPane.setCookie( "webfxtab_resourcesPane", 3 );
    document.location.href="index.php?id=" + document.mutate.id.value + "&a=76"; // 81
  }
}

function deletedocument()
{
  if(confirm("<?php echo $_lang['confirm_delete_document']; ?>")==true)
  {
    document.location.href="index.php?id=" + document.mutate.id.value + "&a=6";
  }
}

function previewdocument()
{
  if(confirm("<?php echo $_lang['confirm_preview']; ?>")==true)
  {
    winOpen("../index.php?id=" + document.mutate.id.value + "&z=manprev", 900, 700);
  }
}

parent.menu.ca = "parent";

try
{
  top.menu.Sync(<?php echo $id; ?>);
}
catch(oException)
{
  xyy=window.setTimeout("loadagain(<?php echo $id; ?>)", 1000);
}

function setParent(pId, pName)
{
  document.mutate.parent.value=pId;
  document.getElementById('parentName').innerHTML = pId + " (" + pName + ")";
}

function clearSelection()
{
  var opt = document.mutate.elements["keywords[]"].options;
  for(i = 0; i < opt.length; i++)
  {
    opt[i].selected = false;
  }
}

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

  if(onoff==1)
  {
    stylevar = "<?php echo $displayStyle; ?>";
  } else {
    stylevar = "none";
  }

  for(var i=0; i<l; i++)
  {
    el = all[i]
    id = el.id;
    if(id == "") continue;
    if(buttonRe.test(id)) el.style.display = stylevar;
  }
}

//Below is code for preventing duplicated aliases and illegal characters in the title and alias fields.

var aliasUnique = false;
var aliasUniqueChecked = false;

function aliasUniqueCheck()
{
  if(document.mutate.alias.value != "" && document.mutate.alias.value != "<?php echo stripslashes($content['alias']);?>")
  {
    var failAliasUniqueCheck = function(t) { alert('Error ' + t.status + ' -- ' + t.statusText); }
    new Ajax.Updater({success:document.getElementById('aliasUniqueMessageResponse')},'actions/dynamic/mutate_content.dynamic.action.php',{method:'post', parameters:'aliasUniqueCheck=' + document.mutate.alias.value, onFailure:failAliasUniqueCheck, evalScripts:true});
  }
  else
  {
    document.getElementById('aliasbox').style.color='';
    document.getElementById('aliasUniqueMessage').style.visibility = 'hidden';
    aliasUnique = true;
    aliasUniqueChecked = true;
  }
}

function fieldsOk()
{
  if( /["]/.test(document.mutate.pagetitle.value))
  {
    alert("<?php echo $_lang["document_title"].": " . $_lang['remove'] . " "?> " + document.mutate.pagetitle.value.match(/["]/)[0]);
    return false;
  }

  if( /["]/.test(document.mutate.alias.value))
  {
    alert("<?php echo $_lang["document_alias"].": " . $_lang['remove'] . " "?> " + document.mutate.alias.value.match(/["]/)[0]); //We don't want quotes around quotes in the dialog
    return false;
  }
  if( /[^\w-]+/.test(document.mutate.alias.value))
  {
    alert("<?php echo $_lang['document_alias'].": " . $_lang['remove'] . " "?> \"" + document.mutate.alias.value.match(/[^\w-]+/)[0] + "\"");
    return false;
  }
   if(document.mutate.alias.value != "<?php echo stripslashes($content['alias']);?>" && !aliasUniqueChecked)
      return false;
   if(document.mutate.alias.value != "<?php echo stripslashes($content['alias']);?>" && !aliasUnique)
   {
      alert("<?php echo $_lang['alias_exists_alert']; ?>");
      return false;
   }
   return true;
}


</script>


<?php
/* handle IE 7 layout when editing link (xhtml doctype used)*/
if($content['type'] == "reference" && strstr($_SERVER['HTTP_USER_AGENT'],'MSIE 7') && $which_editor == 5 && $use_doc_editor == 1)
{
?>

<script type="text/javascript">
// handle onload
var nowOnload = window.onload; // save any existing assignment

window.onload = function ()
{
  resizer();
  if(nowOnload != null && typeof(nowOnload) == 'function')
  {
    nowOnload();
  }
}
// handle browser resize
onresize=resizer;

function resizer()
{
  scrollTo(0,0);
  var tabWidth = document.documentElement.clientWidth;
  divArray = document.getElementsByTagName('DIV');
  for(i=0;divArray[i];i++)
  {
    if(divArray[i].className=='sectionBody') divArray[i].style.width=tabWidth-67+'px';
    else if(divArray[i].className=='tab-page') divArray[i].style.width=tabWidth-37+'px';
  }
}
</script>

<?php
}
/* End handle IE 7 layout when editing link (xhtml doctype used)*/

if(!($content['type'] == "reference" || $_REQUEST['a'] == 72))
{
/* EditArea code starts here */
// Added 0614
// This resizes the editarea js code editor
// So it fills the available view port
?>

<script type="text/javascript">
// 2007/03/09 ~sl - Dynamically Resize textarea

// handle onload
var EaToTop = null;
var nowOnload = window.onload; // save any existing assignment

window.onload = function ()
{
  resizer();
  if(nowOnload != null && typeof(nowOnload) == 'function') nowOnload();
  if(EaToTop != null) EaToTop('ta');
}

// handle browser resize
onresize=resizer;

function resizer()
{
  scrollTo(0,0);
  var x = 27 // allowance

<?php
if(strstr($_SERVER['HTTP_USER_AGENT'],'MSIE 7') && $which_editor == 5 && $use_doc_editor == 1)
{
/* We use RTE in IE 7, the document type is xhtml and we need to define the width of the tab pages in pixels.*/ ?>

  var y = document.documentElement.clientHeight;
  var t = document.mutate.ta; // the textarea
  var o = y-findTop(t)-x;
  t.style.height=Math.max(1,o);
  var tabWidth = document.documentElement.clientWidth;
  t.style.width=tabWidth - 43 +'px';
  divArray = document.getElementsByTagName('DIV');
  for(i=0;divArray[i];i++)
  {
    if(divArray[i].className=='sectionBody') divArray[i].style.width=tabWidth-67+'px';
    else if(divArray[i].className=='tab-page') divArray[i].style.width=tabWidth-37+'px';
  }

<?php } else {?>

  var y = window.innerHeight ? window.innerHeight : document.body.clientHeight;
  var t = document.mutate.ta; // the textarea
  var o = y-findTop(t)-x;
  t.style.height=Math.max(1,o);

<?php } ?>
}

function findTop(obj)
{
  curtop = 0;
  if (obj.offsetParent)
  {
    curtop = obj.offsetTop
    while (obj = obj.offsetParent)
    {
      curtop += obj.offsetTop
    }
  }
  return curtop;
}
</script>

<?php
// Added 0614
// This loads the editarea js code editor for document editing
// If hightlight is set, highlighting is turned on

if($use_code_editor && ((isset($content['richtext']) && $content['richtext'] == 0) || $use_doc_editor == 0))
{
  $syntax = 'html';
  if(isset($content['contentType']) && $content['contentType'] == 'text/xml')
  	{
    $syntax = 'xml';
   }
  else if(isset($content['contentType']) && $content['contentType'] == 'text/plain')
  	{
    $syntax = 'none';
   }

?>

<script language="javascript" type="text/javascript">
function EaToTop(id) { //Scrolls the EditArea to the top
  if(window.frames["frame_"+id] && editAreas[id]["displayed"]==true && editAreaLoader.win=="loaded"
  && window.frames["frame_"+id].document.getElementById('line_number').innerHTML!='')
  {
    editAreaLoader.setSelectionRange(id,0,0);
    window.frames["frame_"+ id].document.getElementById("result").scrollTop=0;
    window.frames["frame_"+ id].document.getElementById("result").scrollLeft=0;
    window.frames["frame_"+ id].editArea.execCommand("onchange");
    //Make width autoresize
    document.getElementById('frame_ta').style.width="100%";
    window.frames["frame_ta"].document.getElementById("result").style.width="100%";
  }
  else
  {
    var t = setTimeout("EaToTop('"+id+"');",100); //EditArea's callback function doesn't work. Use this hack instead.
  }
}
</script>

<?php }
if($use_code_editor && $code_highlight && ((isset($content['richtext']) && $content['richtext']==0) || $use_doc_editor==0) && $syntax!='none')
{
?>

<script language="javascript" type="text/javascript">
editAreaLoader.init({
  id : "ta"        // textarea id
  ,syntax: "<?php echo $syntax; ?>" // syntax to be used for highgliting
  ,start_highlight: false        // to display with highlight mode on start-up
  ,allow_toggle: false
  ,toolbar: "search, go_to_line, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, |, help"
});
</script>

<?php
// Otherwise, don't turn hightlighing on
// automatically
}
elseif($use_code_editor && ((isset($content['richtext']) && $content['richtext'] == 0) || $use_doc_editor == 0))
{
?>

<script language="javascript" type="text/javascript">
editAreaLoader.init({
  id : "ta"        // textarea id
  ,start_highlight: false        // to display with highlight mode on start-up
  ,allow_toggle: false
  ,toolbar: "search, go_to_line, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, |, help"
});
</script>

<?php } } /* End of EditArea code */ ?>

<div class="subTitle">
  <span class="floatRight">
    <img src="./media/images/_tx_.gif" width="1" height="5"><br />
    <?php echo $site_name; ?> - <?php echo $_lang['edit_document_title']; ?>
  </span>
</div>

<?php //if ($_GET['a']=='27') { ?>

<div id="documentTitle"><span class="docTitleButtons">
  <a href="#" onClick="if(fieldsOk()){ documentDirty=false; document.mutate.save.click();}" class="doSomethingButton"><?php echo $_lang['save']; ?></a>

<?php if($_GET['a']!='4' && $_GET['a']!=72) { ?>

      <a href="#" onClick="deletedocument();" class="doSomethingButton"><?php echo $_lang['delete']; ?></a>
      <a href="#" onClick="previewdocument();" class="doSomethingButton"><?php echo $_lang['preview']; ?></a>

<?php } ?>

    <a href="<?php echo $id==0 ? "index.php?a=2" : "index.php?a=3&id=$id"; ?>" onClick="" class="doSomethingButton"><?php echo $_lang['cancel']; ?></a>
  </span><?php echo $id==0 ? "Un-Saved" : $content['pagetitle']."&nbsp;&nbsp;(".$id.")"; ?></div>

<?php //} ?>

<!--<link type="text/css" rel="stylesheet" href="./media/style/tabs.css" />-->
<script type="text/javascript" src="./media/script/tabpane.js"></script>

<div class="tab-pane" id="contentPane">

<script type="text/javascript">
  tpSettings = new WebFXTabPane(document.getElementById("contentPane"),0);
</script>

<form name="mutate" method="post" action="index.php" <?php if($which_editor==5 && ($content['richtext']==1 || $_GET['a']==4) && $use_doc_editor==1) echo "onsubmit=\"return submitForm();\""; ?> >
<input type="hidden" name="a" value="5" />
<input type="hidden" name="id" value="<?php echo $content['id'];?>" />
<input type="hidden" name="mode" value="<?php echo $_GET['a'];?>" />
<input type="hidden" name="createdby" value="<?php echo $content['createdby'];?>" />
<!--<input type="hidden" name="createdon" value="<?php echo $content['createdon'];?>" />-->

<?php if ($content['type']=="document" || $_REQUEST['a']==4) { ?>

<!--<div class="tab-page" id="tabPage1">-->
<div class="tab-page" id="tabPage1" <?php if(strstr($_SERVER['HTTP_USER_AGENT'],'MSIE')) echo ' style="position:absolute; top:18px; left:0px"';?>>
  <div class="tab">
    <img src='./media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['document_content']; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage1"));
  </script>

<!-- START::WYSIWYG Editor Startup Section -->
<?php
if(($content['richtext']==1 || $_GET['a']==4) && $use_doc_editor==1)
{
 
  # Start - RTE
  if($which_editor==5)
  {
    include('./media/rte/rte.php');
  }

  # Start - Xinha
  elseif($which_editor==4)
  {
    include('xhina_mutate_settings.php');
  }

  # Start - FCKeditor
  elseif($which_editor==3)
  {
    $sBasePath = "./media/fckeditor/";
    include($sBasePath."fckeditor.php");
    $oFCKeditor = new FCKeditor('ta');
    $oFCKeditor->BasePath = $sBasePath;
    $oFCKeditor->Value = $content['content'];
    $oFCKeditor->Height = '420';
    $oFCKeditor->Create();
  }
  #End FCKeditor

  # HTMLArea Editor
  elseif($which_editor==2)
  {
    include('HTMLArea_mutate_settings.php');
  }

  #tinyMCE Editor
  elseif($which_editor==1)
  {
    include('tinyMCE_mutate_settings.php');
  }
# Start - Texyla load
  elseif($which_editor == 6)
  {
    //include texyla textarea + settings for javascript
    require_once('texyla_mutate_settings.php');
  }

}
else
{
?>
<!-- END::WYSIWYG Editor Startup Section -->

  <!-- Standard TextArea Box -->
  <div style="width:100%">
  <textarea id="ta" name="ta" style="width:100%; " onChange="documentDirty=true;"><?php if(!isset($syntax) || $content['content']!='') echo htmlspecialchars($content['content']); else echo " "; ?></textarea>
  </div>

<?php } 
 
?>

<!--    </div>-->
</div>

<?php } ?>

<!--tabPage1-->

<!-- <div class="tab-page" id="tabPage2"> -->
<div class="tab-page" id="tabPage2" <?php if(strstr($_SERVER['HTTP_USER_AGENT'],'MSIE')) echo ' style="position:absolute; top:18px; left:0px"';?>>
  <div class="tab">
    <img src='./media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['document_identification']; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage2"));
  </script>

  <div class="sectionBody">
    <?php if($content['type']=="reference" || $_REQUEST['a']==72) { echo $_lang['weblink_message']; } ?>
    <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr style="height: 24px;">
        <td style="width:100px; text-align:left;">
          <span class='warning'><?php echo $_lang['document_title']; ?></span></td>
        <td>
          <input name="pagetitle" type="text" maxlength="100" value="<?php echo stripslashes($content['pagetitle']);?>" class="inputBox" style="width:300px;" onChange="documentDirty=true;">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['document_title_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
      </tr>
      <tr style="height: 24px;">
        <td style="width:100px; text-align:left;">
          <span class='warning'><?php echo $_lang['long_title']; ?></span>
        </td>
        <td>
          <input name="setitle" type="text" maxlength="120" value="<?php echo stripslashes($content['longtitle']);?>" class="inputBox" style="width:300px;" onChange="documentDirty=true;">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['document_long_title_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
      </tr>
      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['document_description']; ?></span></td>
        <td>
          <input name="description" type="text" maxlength="255" value="<?php echo stripslashes($content['description']);?>" class="inputBox" style="width:300px;" onChange="documentDirty=true;">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['document_description_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
      </tr>
      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['document_alias']; ?></span></td>
        <td>
          <input id="aliasbox" name="alias" type="text" maxlength="100" value="<?php echo stripslashes($content['alias']);?>" class="inputBox" style="width:300px;" onkeyup="documentDirty=true;aliasUniqueChecked=false;aliasUniqueCheck();">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['document_alias_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
          <div id="aliasUniqueMessage" style="position:absolute;display:inline;padding-left:7px;color:red;font-weight:bold;visibility:hidden;"><?php echo$_lang['alias_exists']; ?></div><div id="aliasUniqueMessageResponse" style="display:none;"></div>
        </td>
      </tr>

<?php if($content['type']=="reference" || $_REQUEST['a']==72) { ?>

      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['weblink']; ?></span></td>
        <td>
          <input name="ta" type="text" maxlength="255" value="<?php echo !empty($content['content']) ? stripslashes($content['content']) : "http://" ;?>" class="inputBox" style="width:300px;" onChange="documentDirty=true;">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['document_weblink_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
      </tr>

<?php } ?>

      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['document_parent']; ?></span></td>
        <td valign="top">

<?php
        if(isset($_GET['id']))
        {
          if($content['parent']==0)
          {
            $parentname = $site_name;
          }
          else
          {
            $sql = "SELECT pagetitle FROM $dbase.".$table_prefix."site_content WHERE $dbase.".$table_prefix."site_content.id = ".$content['parent'].";";
            $rs = mysql_query($sql);
            $limit = mysql_num_rows($rs);
            if($limit!=1)
            {
              $e->setError(8);
              $e->dumpError();
            }
            $parentrs = mysql_fetch_assoc($rs);
            $parentname = $parentrs['pagetitle'];
          }
        }
        else if(isset($_GET['pid']))
        {
          if($_GET['pid']==0)
          {
            $parentname = $site_name;
          }
          else
          {
            $sql = "SELECT pagetitle FROM $dbase.".$table_prefix."site_content WHERE $dbase.".$table_prefix."site_content.id = ".$_GET['pid'].";";
            $rs = mysql_query($sql);
            $limit = mysql_num_rows($rs);
            if($limit!=1)
            {
              $e->setError(8);
              $e->dumpError();
            }
            $parentrs = mysql_fetch_assoc($rs);
            $parentname = $parentrs['pagetitle'];
          }
        }
        else
        {
            $parentname = $site_name;
            $content['parent']=0;
        }
?>

        &nbsp;&nbsp;<b><span id="parentName"><?php echo isset($_REQUEST['pid']) ? $_REQUEST['pid'] : $content['parent']; ?> (<?php echo $parentname; ?>)</span></b>&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['document_parent_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        <input type="hidden" name="parent" value="<?php echo isset($_REQUEST['pid']) ? $_REQUEST['pid'] : $content['parent']; ?>" onChange="documentDirty=true;">
        </td>
      </tr>
    </table>
  </div>
</div> <!--tabPage2-->

<!-- <div class="tab-page" id="tabPage3"> -->
<div class="tab-page" id="tabPage3" <?php if(strstr($_SERVER['HTTP_USER_AGENT'],'MSIE')) echo ' style="position:absolute; top:18px; left:0px"';?>>
  <div class="tab">
    <img src='./media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['document_opt']; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage3"));
  </script>

  <div class="sectionBody">

    <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr style="height: 24px;">
        <td width="150"><span class='warning'><?php echo $_lang['document_opt_folder']; ?></span></td>
        <td>
      <input name="isfoldercheck" type="checkbox" <?php echo $content['isfolder']==1 ? "checked" : "" ;?> onClick="changestate(document.mutate.isfolder);"><input type="hidden" name="isfolder" value="<?php echo $content['isfolder']==1 ? 1 : 0 ;?>" onChange="documentDirty=true;">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['document_opt_folder_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
        <td rowspan="11" width="150" align="center">
          <span class='warning'><?php echo $_lang['keywords']; ?></span> <img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['document_keywords_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
          <select multiple name="keywords[]" size="15" class="inputBox" style="width:100%;" onChange="documentDirty=true;">

<?php
          $keys = array_keys($keywords);
          for($i=0;$i<count($keys);$i++)
          {
            $key = $keys[$i];
            $value = $keywords[$key];
            $selected = $keywords_selected[$key];
            echo "<option $selected value=\"$key\">$value\n";
          }
?>

          </select>
          <input type="button" value="<?php echo $_lang['deselect_keywords']; ?>" onClick="clearSelection();" />
          <input type="button" value="<?php echo $_lang['manage_keywords'];?>" onclick="gotoKeywords();" />
        </td>
      </tr>

<?php if($content['type']!="reference" && $_REQUEST['a']!=72) { ?>

      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['document_opt_richtext']; ?></span></td>
    <td><input name="richtextcheck" type="checkbox" <?php echo $content['richtext']==0 && $_REQUEST['a']==27 ? "" : "checked" ;?> onClick="changestate(document.mutate.richtext);"><input type="hidden" name="richtext" value="<?php echo $content['richtext']==0 && $_REQUEST['a']==27 ? 0 : 1 ;?>" onChange="documentDirty=true;">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['document_opt_richtext_help']; ?>" onClick="alert(this.alt);" style="cursor:help;"></td>
      </tr>

<?php } ?>

      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['document_opt_published']; ?></span></td>
        <td>
      <input name="publishedcheck" type="checkbox" <?php echo (isset($content['published']) && $content['published']==1) || (!isset($content['published']) && $publish_default==1) ? "checked" : "" ;?> onClick="changestate(document.mutate.published);"><input type="hidden" name="published" value="<?php echo (isset($content['published']) && $content['published']==1) || (!isset($content['published']) && $publish_default==1) ? 1 : 0 ;?>">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['document_opt_published_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
      </tr>

<!-- START:createdondate Added [0615] by Ralph -->
      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['createdon']; ?></span></td>
        <td>
          <input id="createdon" name="createdon" type="hidden" value="<?php echo $content['createdon']=="0" || !isset($content['createdon']) ? "" : $content['createdon'];?>" onBlur="documentDirty=true;">

          <table width="270" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="180" style="border: 1px solid #808080; padding:0px 5px;">
                <span id="createdon_show" class="inputBox"><?php echo $content['createdon']=="0" || !isset($content['createdon']) ? strftime($date_format.$sep.$time_format, time()) : strftime($date_format.$sep.$time_format, $content['createdon']);?></span>
              </td>
              <td>&nbsp;
                <img id="createdon_cal" src="./media/images/icons/cal.gif" width="16" height="16" border="0">
                <a onClick="document.mutate.createdon.value=''; document.getElementById('createdon_show').innerHTML='<i>(set on save)</i>'; return true;" onMouseover="window.status='Don\'t set a publish date'; return true;" onMouseout="window.status=''; return true;"><img src="./media/images/icons/icon_today.gif" width="16" height="16" border="0" alt="No date"></a>
                &nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['page_data_createdon_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
              </td>
            </tr>
          </table>
        </td>
      </tr>
<!-- END:createdondate Added [0615] by Ralph -->

      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['page_data_publishdate']; ?></span></td>
        <td>
          <input id="pub_date" name="pub_date" type="hidden" value="<?php echo $content['pub_date']=="0" || !isset($content['pub_date']) ? "" : $content['pub_date'];?>" onBlur="documentDirty=true;">

          <table width="270" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="180" style="border: 1px solid #808080; padding:0px 5px;">
                <span id="pub_date_show" class="inputBox"><?php echo $content['pub_date']=="0" || !isset($content['pub_date']) ? "<i>(not set)</i>" : strftime($date_format.$sep.$time_format, $content['pub_date']);?></span>
              </td>
              <td>&nbsp;
                <img id="pub_date_cal" src="./media/images/icons/cal.gif" width="16" height="16" border="0">
                <a onClick="document.mutate.pub_date.value=''; document.getElementById('pub_date_show').innerHTML='<i>(not set)</i>'; return true;" onMouseover="window.status='Don\'t set a publish date'; return true;" onMouseout="window.status=''; return true;"><img src="./media/images/icons/cal_nodate.gif" width="16" height="16" border="0" alt="No date"></a>
                &nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['page_data_publishdate_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['page_data_unpublishdate']; ?></span></td>
        <td>
          <input id="unpub_date" name="unpub_date" type="hidden" value="<?php echo $content['unpub_date']=="0" || !isset($content['unpub_date']) ? "" : $content['unpub_date']; ?>" onBlur="documentDirty=true;" />
          <table width="270" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="180" style="border: 1px solid #808080; padding:0px 5px;">
                <span id="unpub_date_show" class="inputBox"><?php echo $content['unpub_date']=="0" || !isset($content['unpub_date']) ? "<i>(not set)</i>" : strftime($date_format.$sep.$time_format, $content['unpub_date']);?></span>
              </td>
              <td>&nbsp;
                <img id="unpub_date_cal" src="./media/images/icons/cal.gif" width="16" height="16" border="0">
                <a onClick="document.mutate.unpub_date.value=''; document.getElementById('unpub_date_show').innerHTML='<i>(not set)</i>'; return true;" onMouseover="window.status='Don\'t set an unpublish date'; return true;" onMouseout="window.status=''; return true;"><img src="./media/images/icons/cal_nodate.gif" width="16" height="16" border="0" alt="No date"></a>
                &nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['page_data_unpublishdate_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr style="height: 24px;">
        <td align="left" style="width:100px;">
          <span class='warning'><?php echo $_lang['document_opt_menu_index']; ?></span>
        </td>
        <td>
          <input name="menuindex" type="text" maxlength="5" value="<?php echo $content['menuindex'];?>" class="inputBox" style="width:40px; text-align:right;" onChange="documentDirty=true;">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['document_opt_menu_index_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
      </tr>

      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['page_data_searchable']; ?></span></td>
        <td>
          <input name="searchablecheck" type="checkbox" <?php echo (isset($content['searchable']) && $content['searchable']==1) || (!isset($content['searchable']) && $search_default==1) ? "checked" : "" ;?> onClick="changestate(document.mutate.searchable);"><input type="hidden" name="searchable" value="<?php echo (isset($content['searchable']) && $content['searchable']==1) || (!isset($content['searchable']) && $search_default==1) ? 1 : 0 ;?>" onChange="documentDirty=true;">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['page_data_searchable_help']; ?>" onClick="alert(this.alt);" style="cursor:help;" />
        </td>
      </tr>

<?php if($content['type']!="reference" && $_REQUEST['a']!=72) { ?>

      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['page_data_cacheable']; ?></span></td>
        <td>
          <input name="cacheablecheck" type="checkbox" <?php echo (isset($content['cacheable']) && $content['cacheable']==1) || (!isset($content['cacheable']) && $cache_default==1) ? "checked" : "" ;?> onClick="changestate(document.mutate.cacheable);"><input type="hidden" name="cacheable" value="<?php echo (isset($content['cacheable']) && $content['cacheable']==1) || (!isset($content['cacheable']) && $cache_default==1) ? 1 : 0 ;?>" onChange="documentDirty=true;">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['page_data_cacheable_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
      </tr>

      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['document_opt_emptycache']; ?></span></td>
        <td>
          <input name="syncsitecheck" type="checkbox" <?php echo (isset($syncsitecheck_default) && $syncsitecheck_default==1) || (!isset($syncsitecheck_default) && $syncsitecheck_default==1) ? "checked" : "" ;?> onClick="changestate(document.mutate.syncsite);"><input type="hidden" name="syncsite" value="<?php echo ($syncsitecheck_default==1) ? 1 : 0;?>">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['document_opt_emptycache_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
      </tr>

      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['page_data_template']; ?></span></td>
        <td>

<?php

          $sql = "select templatename, id from $dbase.".$table_prefix."site_templates ORDER BY templatename ASC";
          $rs = mysql_query($sql);

?>

          <select name="template" class="inputBox" onChange='documentDirty=true;' style="width:150px">

<?php
          while ($row = mysql_fetch_assoc($rs))
          {
            if(isset($content['template']))
            {
              $selectedtext = $row['id']==$content['template'] ? "selected='selected'" : "" ;
            }
            else
            {
              $selectedtext = $row['id']==$default_template ? "selected='selected'" : "" ;
            }
?>
            <option value="<?php echo $row['id']; ?>" <?php echo $selectedtext; ?>><?php echo $row['templatename']; ?></option>
<?php       }?>

          </select>
          &nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['page_data_template_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
      </tr>

<?php if($_SESSION['role']==1) { ?>

      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['page_data_contentType']; ?></span></td>
        <td>
          <select name="contentType" class="inputBox" onChange='documentDirty=true;' style="width:150px">
            <option value="text/html" <?php echo $content['contentType']=="text/html" ? "selected='selected'" : "" ;?>>text/html</option>
            <option value="text/plain" <?php echo $content['contentType']=="text/plain" ? "selected='selected'" : "" ;?>>text/plain</option>
            <option value="text/xml" <?php echo $content['contentType']=="text/xml" ? "selected='selected'" : "" ;?>>text/xml</option>
          </select>
          &nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['page_data_contentType_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
      </tr>

<?php } else { ?>

      <input type="hidden" name="contentType" value="<?php echo isset($content['contentType']) ? $content['contentType'] : "text/html"; ?>" />

<?php } ?>

      <input type="hidden" name="type" value="document">
      <?php } else { ?>
      <input type="hidden" name="contentType" value="text/html" />
      <input type="hidden" name="cacheable" value="0" />
      <input type="hidden" name="syncsite" value="1" />
      <input type="hidden" name="template" value="0" />
      <input type="hidden" name="richtext" value="0" />
      <input type="hidden" name="type" value="reference" />
      </tr>

<?php } ?>

<!-- MARKER:removed reset_createdon_date checkbox in [0615] by Ralph -->

      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['require_authenticate']; ?></span></td>
        <td>
          <input name="authenticatecheck" type="checkbox" <?php echo $content['authenticate']==1 ? "checked" : "" ;?> onClick="changestate(document.mutate.authenticate);"><input type="hidden" name="authenticate" value="<?php echo $content['authenticate']==1 ? 1 : 0 ;?>" onChange="documentDirty=true;">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['require_authenticate_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
      </tr>

      <!-- Start::New in [0614] Ralph -->
      <?php if($content['showinmenu'] == "") $content['showinmenu'] = $showinmenu_default;?>
      <tr style="height: 24px;">
        <td><span class='warning'><?php echo $_lang['showinmenu']; ?></span></td>
        <td>
          <input name="showinmenucheck" type="checkbox"<?php echo ($content['showinmenu'] == 1) ? " checked" : "" ;?>
          onClick="changestate(document.mutate.showinmenu);"><input type="hidden" name="showinmenu" value="<?php echo ($content['showinmenu'] == 1) ? 1 : 0 ;?>" onChange="documentDirty=true;">&nbsp;&nbsp;<img src="./media/images/icons/b02_trans.gif" onMouseover="this.src='./media/images/icons/b02.gif';" onMouseout="this.src='./media/images/icons/b02_trans.gif';" alt="<?php echo $_lang['showinmenu_help']; ?>" onClick="alert(this.alt);" style="cursor:help;">
        </td>
      </tr>
      <!-- End::New in [0614] Ralph -->

    </table>
  </div>
</div> <!--tabPage3-->

  <!--tabPage4-->
<?php
if($use_udperms==1)
{
  $groupsarray = array();

  if($_GET['a']=='27')
  { // fetch permissions on the document from the database
    $sql = "SELECT * FROM $dbase.".$table_prefix."document_groups where document=".$id;
    $rs = mysql_query($sql);
    $limit = mysql_num_rows($rs);
    for ($i = 0; $i < $limit; $i++)
    {
      $currentgroup=mysql_fetch_assoc($rs);
      $groupsarray[$i] = $currentgroup['document_group'];
    }
  }
  else
  {
    if($_REQUEST['pid']==0)
    { // New root level document, we assign it to the document groups that the user has access to
      $sql = "SELECT $dbase.".$table_prefix."documentgroup_names.id, $dbase.".$table_prefix."documentgroup_names.name
      FROM $dbase.".$table_prefix."documentgroup_names, $dbase.".$table_prefix."membergroup_access, $dbase.".$table_prefix."member_groups
      WHERE $dbase.".$table_prefix."documentgroup_names.id = $dbase.".$table_prefix."membergroup_access.documentgroup
      AND $dbase.".$table_prefix."membergroup_access.membergroup = $dbase.".$table_prefix."member_groups.user_group
      AND $dbase.".$table_prefix."member_groups.member = " . $_SESSION['internalKey'];

      $rs = mysql_query($sql)or die("Could not get the user's document groups from the database: " . mysql_error());
      $limit = mysql_num_rows($rs);
      for ($i = 0; $i < $limit; $i++)
      {
        $currentgroup=mysql_fetch_assoc($rs);
        $groupsarray[$i] = $currentgroup['id'];
      }
    }
    else
    {
      if(!empty($_REQUEST['pid']))
      {  // Set permissions based on the parent document
        $sql = "SELECT * FROM $dbase.".$table_prefix."document_groups where document=".$_REQUEST['pid'];
        $rs = mysql_query($sql);
        $limit = mysql_num_rows($rs);
        for($i = 0; $i < $limit; $i++)
        {
          $currentgroup=mysql_fetch_assoc($rs);
          $groupsarray[$i] = $currentgroup['document_group'];
        }
      }
    }
  }
  if($_SESSION['permissions']['access_permissions']==1)
  {
?>

  <!-- <div class="tab-page" id="tabPage4"> -->
<div class="tab-page" id="tabPage4" <?php if(strstr($_SERVER['HTTP_USER_AGENT'],'MSIE')) echo ' style="position:absolute; top:18px; left:0px"';?>>
  <div class="tab">
    <img src='./media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['access_permissions']; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage4"));
  </script>

  <div class="sectionBody">
    <p class="menuHeader"><?php echo $_lang['access_permissions_docs_message']; ?></p>
    <br />

<?php
  }
      $sql = "SELECT name, id FROM $dbase.".$table_prefix."documentgroup_names";
      $rs = mysql_query($sql);
      $limit = mysql_num_rows($rs);
      for($i=0; $i<$limit; $i++)
      {
        $row=mysql_fetch_assoc($rs);
        if($_SESSION['permissions']['access_permissions']==1)
        {

?>
    <input type="checkbox" name="document_groups['<?php echo $row['id']; ?>']" <?php echo in_array($row['id'], $groupsarray) ? "checked='checked'" : "" ; ?>><?php echo $row['name']; ?><br />
<?php
        }
        else
        {
?>

  <input type="hidden" name="document_groups['<?php echo $row['id']; ?>']" value="<?php echo in_array($row['id'], $groupsarray) ? "on" : "" ; ?>">

<?php
        }
      }
?>

  </div>

<?php
}
?>

  <input type="submit" name="save" style="display:none">
  </form>

</div> <!--tabPage4-->

</div> <!--tab-pane-->

<!-- START::jscalendar-1.0 date selector popup calendar widgets -->
<script type="text/javascript">
//<![CDATA[

Calendar.setup({
  inputField  : "createdon",      // id of the input field
  ifFormat    : "%s",             // format of the input field
  displayArea : "createdon_show", // id of the display element
  daFormat    : "<?php echo $date_format.$sep.$time_format; ?>", // format for the display element
  showsTime   : true,             // will display a time selector
  timeFormat  : "<?php echo $time_hours; ?>",// 12 or 24 hour time display
  button      : "createdon_cal",  // trigger for the calendar (button ID)
  electric    : false,            // instant update
  singleClick : false,            // single-click mode
  weekNumbers : false,            // display week numbers
  step        : 1,                // increment for drop-downs
  align       : "BL"              // calendar alignment
});

Calendar.setup({
  inputField  : "pub_date",       // id of the input field
  ifFormat    : "%s",             // format of the input field
  displayArea : "pub_date_show",  // id of the display element
  daFormat    : "<?php echo $date_format.$sep.$time_format; ?>", // format for the display element
  showsTime   : true,             // will display a time selector
  timeFormat  : "<?php echo $time_hours; ?>",// 12 or 24 hour time display
  button      : "pub_date_cal",   // trigger for the calendar (button ID)
  electric    : false,            // instant update
  singleClick : false,            // single-click mode
  weekNumbers : false,            // display week numbers
  step        : 1,                // increment for drop-downs
  align       : "BL"              // calendar alignment
});

Calendar.setup({
  inputField  : "unpub_date",       // id of the input field
  ifFormat    : "%s",             // format of the input field
  displayArea : "unpub_date_show",  // id of the display element
  daFormat    : "<?php echo $date_format.$sep.$time_format; ?>", // format for the display element
  showsTime   : true,             // will display a time selector
  timeFormat  : "<?php echo $time_hours; ?>",// 12 or 24 hour time display
  button      : "unpub_date_cal",   // trigger for the calendar (button ID)
  electric    : false,            // instant update
  singleClick : false,            // single-click mode
  weekNumbers : false,            // display week numbers
  step        : 1,                // increment for drop-downs
  align       : "BL"              // calendar alignment
});

//]]>
</script>
<!-- END::jscalendar-1.0 date selector popup calendar widgets -->
