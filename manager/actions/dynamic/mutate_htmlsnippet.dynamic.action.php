<?php
// mutate_htmlsnippet.dynamic.action.php
// Last Modified: 2008-04-28 [v1.0] by Ralph A. Dahlgren

if(IN_ETOMITE_SYSTEM != "true")
{
  die($_lang["include_ordering_error"]);
}

if($_SESSION['permissions']['edit_chunk'] != 1 && $_REQUEST['a'] == 77)
{
  $e->setError(3);
  $e->dumpError();
}

if($_SESSION['permissions']['new_chunk'] != 1 && $_REQUEST['a'] == 78)
{
  $e->setError(3);
  $e->dumpError();
}

if(isset($_REQUEST['id']))
{
  $id = $_REQUEST['id'];
}
else
{
  $id=0;
}

// check to see the snippet editor isn't locked
$sql = "SELECT internalKey, username FROM $dbase.".$table_prefix."active_users WHERE $dbase.".$table_prefix."active_users.action=77 AND $dbase.".$table_prefix."active_users.id=$id";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit > 1)
{
  for($i=0; $i < $limit; $i++)
  {
    $lock = mysql_fetch_assoc($rs);
    if($lock['internalKey'] != $_SESSION['internalKey'])
    {
      $msg = $lock['username']." is currently editing this snippet. Please wait until the other user has finished and try again.";
      $e->setError(5, $msg);
      $e->dumpError();
    }
  }
}
// end check for lock

// make sure the id's a number
if(!is_numeric($id))
{
  echo "Passed ID is NaN!";
  exit;
}

if(isset($_GET['id']))
{
  $sql = "SELECT * FROM $dbase.".$table_prefix."site_htmlsnippets WHERE $dbase.".$table_prefix."site_htmlsnippets.id = $id;";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  if($limit > 1)
  {
    echo "Oops, Multiple htmlsnippets sharing same unique id. Not good.<p>";
    exit;
  }
  if($limit < 1)
  {
    echo "Oops, HTMLSnippet doesn't exist.";
    exit;
  }
  $content = mysql_fetch_assoc($rs);
  $_SESSION['itemname'] = $content['name'];
  if($content['locked'] == 1 && $_SESSION['role'] != 1)
  {
    $e->setError(3);
    $e->dumpError();
  }
}
else
{
  $_SESSION['itemname']="New HTMLSnippet";
}
?>

<script language="JavaScript">

function deletedocument()
{
  if(confirm("<?php echo $_lang['confirm_delete_htmlsnippet']; ?>") == true)
  {
    document.location.href="index.php?id=" + document.mutate.id.value + "&a=80";
  }
}

<?php
// Added 0614
// This stores the scroll offset of the textarea/EditArea to a cookie.
// The scroll offset is remembered if we continue editing after saving.
?>

function saveEAscroll(id)
{
  try
  {
    if(!document.mutate.stay.checked)
    {
      document.cookie = "EAscrl=;expires=Thu, 01-Jan-1970 00:00:01 GMT";
      return;
    }
    var rangearr = editAreaLoader.getSelectionRange(id);
    if(window.frames["frame_"+id] && editAreas[id]["displayed"] == true)
    {
      var cookietext = "EAscrl=" + window.frames["frame_"+ id].document.getElementById("result").scrollTop + ",";
      cookietext += window.frames["frame_"+id].document.getElementById("result").scrollLeft + ",";
    }
    else
    {
      var cookietext = "EAscrl="+ document.getElementById(id).scrollTop + ","
      cookietext += document.getElementById(id).scrollLeft + ",";
    }
    cookietext += rangearr['start'] + ",";
    cookietext += rangearr['end'] + "";
    document.cookie = cookietext;
    document.mutate.post.value = editAreaLoader.getValue('html_editor');
  }
  catch(e){}
}

function readEAscroll(id)
{
  try
  {
    if(document.cookie.length > 0)
    {
      c_name="EAscrl";
        c_start = document.cookie.indexOf(c_name + "=")
        if(c_start != -1)
        {
          c_start=c_start + c_name.length + 1
          c_end = document.cookie.indexOf(";",c_start)
          if(c_end == -1)
          {
            c_end = document.cookie.length;
          }
          var cargarr = document.cookie.substring(c_start,c_end).split(',');
        }
      }
<?php if($use_code_editor) {?>

      if(window.frames["frame_"+id] && editAreas[id]["displayed"] == true && editAreaLoader.win == "loaded"
  && window.frames["frame_"+id].document.getElementById('line_number').innerHTML != '')
  {
    if(!cargarr)
    {
      cargarr = new Array(0,0,0,0);
    }
    editAreaLoader.setSelectionRange(id,cargarr[2],cargarr[3]);
    window.frames["frame_"+ id].document.getElementById("result").scrollTop = cargarr[0];
    window.frames["frame_"+ id].document.getElementById("result").scrollLeft = cargarr[1];
    window.frames["frame_"+ id].editArea.execCommand("onchange");
    document.cookie = "EAscrl=;expires=Thu, 01-Jan-1970 00:00:01 GMT";
    //Make EditArea autoadjust the width on resize
    document.getElementById("frame_"+ id).style.width="100%";
    window.frames["frame_"+ id].document.getElementById("result").style.width="100%";
  }
  else
  {
    var t = setTimeout("readEAscroll('"+id+"');", 100); //EditArea's callback function doesn't work. Use this hack instead.
  }

<?php } else { ?>

  if(document.getElementById(id) && cargarr && cargarr.length == 4)
  {
    document.getElementById(id).scrollTop = cargarr[0];
    document.getElementById(id).scrollLeft = cargarr[1];
    editAreaLoader.setSelectionRange(id,cargarr[2],cargarr[3]);
    document.cookie = "EAscrl=;expires=Thu, 01-Jan-1970 00:00:01 GMT";
  }

<?php } ?>

  }
  catch(e){}
}

</script>

<?php
// Added 0614
// This loads the editarea js code editor
// for snippet editing
// If hightlight is set, highlighting is turned on
if($use_code_editor && $code_highlight)
{

  $syntax = 'html';
  if(isset($content['name']) && !(strrpos($content['name'],'.') === false))
  {
    switch (substr($content['name'],strrpos($content['name'],'.')))
    {
      case '.js' :
      case '.JS' :
        $syntax = 'js';
      break;
      case '.css' :
      case '.CSS' :
        $syntax = 'css';
      break;
      case '.python' :
      case '.PYTHON' :
        $syntax = 'python';
      break;
      case '.vb' :
      case '.VB' :
        $syntax = 'vb';
      break;
      case '.xml' :
      case '.XML' :
        $syntax = 'xml';
      break;
      default :
        $syntax = 'html';
      break;
    }
  }
?>

<script language="javascript" type="text/javascript">
editAreaLoader.init({
  id : "html_editor"        // textarea id
  ,syntax: "<?php echo $syntax; ?>"            // syntax to be uses for highgliting
  ,start_highlight: false        // to display with highlight mode on start-up
  ,allow_toggle : false
  ,toolbar: "search, go_to_line, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, |, help"
});
</script>

<?php
// Otherwise, don't turn hightlighing on
// automatically
}
elseif($use_code_editor)
{
?>

<script language="javascript" type="text/javascript">
editAreaLoader.init({
  id : "html_editor"        // textarea id
  ,syntax: "<?php echo $syntax; ?>"            // syntax to be uses for highgliting
  ,start_highlight: false        // to display with highlight mode on start-up
  ,allow_toggle : false
  ,toolbar: "search, go_to_line, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, |, help"
});
</script>

<?php } ?>

<?php
// Added 0614
// This resizes the editarea js code editor
// So it fills the available view port
?>

<script type="text/javascript">
// 2007/03/09 ~sl - Dynamically Resize textarea

// handle onload
var nowOnload = window.onload; // save any existing assignment
window.onload = function ()
{
  resizer();
  if(nowOnload != null && typeof(nowOnload) == 'function')
  {
    nowOnload();
  }
  readEAscroll('html_editor');
}

// handle browser resize
onresize=resizer;

function resizer()
{
  scrollTo(0,0);
  var x = 27 // allowance
  var y = window.innerHeight ? window.innerHeight : document.body.clientHeight;
  var t = document.mutate.post; // the textarea
  var o = y-findTop(t)-x;
  t.style.height=Math.max(1,o);
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

<form name="mutate" method="post" action="index.php?a=79">
<input type="hidden" name="id" value="<?php echo $content['id'];?>">
<input type="hidden" name="mode" value="<?php echo $_GET['a'];?>">

<div class="subTitle">
  <span class="floatLeft">
  <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <a href="#" onClick="documentDirty=false; saveEAscroll('html_editor'); document.mutate.post.value=editAreaLoader.getValue('html_editor'); document.mutate.submit(); saveWait('mutate');" class="doSomethingButton"><?php echo $_lang['save']; ?></a>

<?php if($_GET['a']=='77' && $_SESSION['permissions']['new_chunk']==1) { ?>

    <a href="#" onClick="deletedocument();" class="doSomethingButton"><?php echo $_lang['delete']; ?></a>

<?php } ?>

    <a href="index.php?a=76" class="doSomethingButton"><?php echo $_lang['cancel']; ?></a>
    </span>
  <span class="floatRight"><img src="media/images/_tx_.gif" width="1" height="5"><br />

<?php echo $site_name ;?> - <?php echo $_lang['htmlsnippet_title']; ?>

  </span>
</div>

<div class="sectionHeader">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['htmlsnippet_title']; ?>
</div>

<div class="sectionBody">
  <?php echo $_lang['htmlsnippet_msg']; ?><br />
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td align="left"><?php echo $_lang['htmlsnippet_name']; ?>:</td>
      <td align="left"><span style="font-family:'Courier New', Courier, mono">{{</span><input name="name" type="text" maxlength="100" value="<?php echo $content['name'];?>" class="inputBox" style="width:300px;" onChange='documentDirty=true;'><span style="font-family:'Courier New', Courier, mono">}}</span></td>
    </tr>
    <tr>
      <td align="left"><?php echo $_lang['htmlsnippet_desc']; ?>:&nbsp;&nbsp;</td>
      <td align="left"><span style="font-family:'Courier New', Courier, mono">&nbsp;&nbsp;</span><input name="description" type="text" maxlength="255" value="<?php echo $content['description'];?>" class="inputBox" style="width:300px;" onChange='documentDirty=true;'></td>
    </tr>
    <tr>
      <td align="left"><?php echo $_lang['stay']; ?>:</td>
      <td align="left"><span style="font-family:'Courier New', Courier, mono">&nbsp;&nbsp;</span><input name="stay" type="checkbox" checked class="inputBox">
            <span class="warning" id='savingMessage'>&nbsp;</span>
    </td>
    </tr>
    <tr>
      <td align="left"><?php echo $_lang['lock_htmlsnippet']; ?>:</td>
      <td align="left"><span style="font-family:'Courier New', Courier, mono">&nbsp;&nbsp;</span><input name="locked" type="checkbox" <?php echo $content['locked']==1 ? "checked='checked'" : "" ;?> class="inputBox"><?php echo $_lang['lock_snippet_msg']; ?>
    </td>
    </tr>
  </table>
  <textarea name="post" id = "html_editor" style="width:100%; " onChange='documentDirty=true;'><?php echo htmlspecialchars($content['snippet']); ?></textarea>
  <input type="submit" name="save" style="display:none">
</div>
</form>
