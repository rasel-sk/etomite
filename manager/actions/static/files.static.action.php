<?php
// files.static.action.php
// Modified: 2006-10-19 By: Ralph A Dahlgren - Added create new directory feature
// Modified: 2007-05-04 By Ralph A Dahlgren - Added code to sort file listings
// Multiple other modifications made by others for 0614
// Modified 2008-04-29 [v1.0] by Ralph to incorporate system settings
// Modified 2008-05-08 [v1.1] by Ralph to fix minor bugs

if(IN_ETOMITE_SYSTEM!="true")
{
  die($_lang["include_ordering_error"]);
}

if($_SESSION['permissions']['file_manager'] != 1 && $_REQUEST['a'] == 31)
{
  $e->setError(3);
  $e->dumpError();
}

// settings
// define the timestamp format based system config settings
$timestampFormat = $date_format." @ ".$time_format;
// define maximum upload size, converting "M" or "K" to bytes
// recalculate upload_max_filesize from php.ini
$php_maxuploadsize = trim(@ini_get("upload_max_filesize"));
$multiplier = strtoupper(substr($php_maxuploadsize, -1));
switch ($multiplier)
{
  case "M" : $multiplier = 1048576; break;
  case "K" : $multiplier = 1024; break;
  default: $multiplier = 1;
}
$php_maxuploadsize = $php_maxuploadsize * $multiplier;
// recalculate $maxuploadsize
$etomite_maxuploadsize = trim($maxuploadsize);
$multiplier = strtoupper(substr($etomite_maxuploadsize, -1));
switch ($multiplier)
{
  case "M" : $multiplier = 1048576; break;
  case "K" : $multiplier = 1024; break;
  default: $multiplier = 1;
}
$etomite_maxuploadsize = $etomite_maxuploadsize * $multiplier;

$uploadmaxsize = ($etomite_maxuploadsize < $php_maxuploadsize && $etomite_maxuploadsize != "") ? $etomite_maxuploadsize : $php_maxuploadsize;
// define allowable filetypes based on configuration settings
$excludes =  split(",", $exclude_paths);
$editablefiles = split(",", $edit_files);
$editablefiles[] = $friendly_url_suffix;
$inlineviewablefiles = split(",", $inlineview_files);
$inlineviewablefiles[] = $friendly_url_suffix;
$viewablefiles = split(",", $view_files);
$uploadablefiles = split(",", $upload_files);
//fix path for windows
$_REQUEST['path'] = strtr($_REQUEST['path'],"\\",'/');
$filemanager_path = $filemanager_path != '' ? strtr($filemanager_path,"\\",'/') : $_SERVER['DOCUMENT_ROOT'];

// add a leading dot onto file extensions
// $uploadablefiles
$count = count($uploadablefiles);
for($i=0; $i<$count; $i++)
{
  $uploadablefiles[$i] = ".".$uploadablefiles[$i];
}

// $editablefiles
$count = count($editablefiles);
for($i=0; $i<$count; $i++)
{
  $editablefiles[$i] = ".".$editablefiles[$i];
}

// $inlineviewablefiles
$count = count($inlineviewablefiles);
for($i=0; $i<$count; $i++)
{
  $inlineviewablefiles[$i] = ".".$inlineviewablefiles[$i];
}

// $viewablefiles
$count = count($viewablefiles);
for($i=0; $i<$count; $i++)
{
  $viewablefiles[$i] = ".".$viewablefiles[$i];
}

// get the current work directory
if($_REQUEST['path']!="")
{
  $startpath = is_dir($_REQUEST['path']) ? $_REQUEST['path'] : removeLastPath($_REQUEST['path']);
}
else
{
  $startpath = $filemanager_path;
}

$len = strlen($_SERVER['DOCUMENT_ROOT']);

// end settings
/**
* @desc Show only viewer or editor
*/
?>

<div class="subTitle">
  <span class="floatRight">
  <img src="media/images/_tx_.gif" width="1" height="5"><br />
  <?php echo $site_name ;?>&nbsp;-&nbsp;<?php echo $_lang['files_title']; ?>
  </span>
</div>
<div class="sectionHeader">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['files_files']; ?>
</div>

<div class="sectionBody">

<?php
if($_REQUEST['mode'] == "edit" || $_REQUEST['mode'] == "view")
{
$filename=$_REQUEST['path'];
$handle = @fopen($filename, "r");
if(!$handle)
{
  echo 'Error opening file for reading.';
  exit;
}
else
{
  while(!feof($handle))
  {
    $buffer .= fgets($handle, 4096);
  }
  fclose ($handle);
}
// Added 0614
// This loads the editarea js code editor
// for snippet editing
// If hightlight is set, highlighting is turned on

if($use_code_editor)
{
  $is_editable = ($_REQUEST['mode'] == "edit") ? ",is_editable: true" : ",is_editable: false" ;
?>

<script language="javascript" type="text/javascript">
function EaToTop(id) { //Scrolls the EditArea to the top
  if(window.frames["frame_"+id] && editAreas[id]["displayed"]==true && editAreaLoader.win=="loaded"
  && window.frames["frame_"+id].document.getElementById('line_number').innerHTML!=''){
    editAreaLoader.setSelectionRange(id,0,0);
    window.frames["frame_"+ id].document.getElementById("result").scrollTop=0;
    window.frames["frame_"+ id].document.getElementById("result").scrollLeft=0;
    window.frames["frame_"+ id].editArea.execCommand("onchange");
    //Make EditArea autoadjust the width on resize
    document.getElementById("frame_"+ id).style.width="100%";
    window.frames["frame_"+ id].document.getElementById("result").style.width="100%";
  }
  else{
    var t = setTimeout("EaToTop('"+id+"');",100); //EditArea's callback function doesn't work. Use this hack instead.
  }
}
</script>

<?php }
if($use_code_editor && $code_highlight)
{
  switch(getExtension($filename))
  {
    case '.php' :
    case '.php3' :
    case '.php4' :
    case '.php5' :
      $syntax = 'php';
    break;
    case '.css' :
      $syntax = 'css';
    break;
    case '.js' :
      $syntax = 'js';
    break;
    case '.python' :
      $syntax = 'python';
    break;
    case '.vb' :
      $syntax = 'vb';
    break;
    case '.xml' :
      $syntax = 'xml';
    break;
    case '.html' :
    case '.htm' :
    default :
      $syntax = 'html';
  }

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
  if(EaToTop != null)
  {
    EaToTop('file_editor');
  }
}

// handle browser resize
onresize=resizer;

function resizer()
{
  scrollTo(0,0);
  var x = 27 // allowance
  var y = window.innerHeight ? window.innerHeight : document.body.clientHeight;
  var t = document.getElementById('file_editor'); // the textarea
  var o = y-findTop(t)-x;
  t.style.height=Math.max(1,o);
}

function findTop(obj)
{
  curtop = 0;
  if(obj.offsetParent)
  {
    curtop = obj.offsetTop
    while(obj = obj.offsetParent)
    {
      curtop += obj.offsetTop
    }
  }
  return curtop;
}
</script>

<script language="javascript" type="text/javascript">
editAreaLoader.init({
  id : "file_editor"        // textarea id
  ,syntax: "<?php echo $syntax; ?>" // syntax to be uses for highgliting
  ,start_highlight: false        // to display with highlight mode on start-up
  ,allow_toggle: false
  ,toolbar: "search, go_to_line, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, |, help"
  <?php echo $is_editable; ?>
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
  id : "file_editor"        // textarea id
  ,start_highlight: false        // to display with highlight mode on start-up
  ,allow_toggle: true
  ,toolbar: "search, go_to_line, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, |, help"
  <?php echo $is_editable; ?>
});
</script>

<?php } ?>

<form action="index.php" method="post" name="editFile">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="padding-bottom:5px;">

<?php if($_REQUEST['mode']=="edit") { ?>

    <input class="doSomethingButton" type="submit" name="submit" value="<?php echo $_lang["save"]; ?>" />
    <input class="doSomethingButton" type="button" name="cancel" value="<?php echo $_lang["cancel"]; ?>" onclick="document.location.href='index.php?a=31&mode=drill&path=<?php echo removeLastPath($_REQUEST['path']) ?>';" />

<?php } else { ?>

    <input class="doSomethingButton" type="button" name="cancel" value="<?php echo $_lang["cancel"]; ?>" onclick="document.location.href='index.php?a=31&mode=drill&path=<?php echo removeLastPath($_REQUEST['path']) ?>';" />

<?php } ?>

      <span style="font-weight: bold; margin: 0px 10px;"><?php echo str_replace($filemanager_path, "", $filename); ?></span>
    </td>
  </tr>
  </table>
  <textarea style="width:100%; height:370px;" id="file_editor" name="content"><?php echo htmlentities($buffer); ?></textarea>
  <input type="hidden" name="a" value="31" />
  <input type="hidden" name="mode" value="save" />
  <input type="hidden" name="path" value="<?php echo $_REQUEST['path']; ?>" />
</form>

<?php
}
else
{
// <!-- START: create new directory feature added 2006-10-19 by RAD -->
if(isset($_POST['newDir']))
{
  // strip out any characters that might be part of a malicious attack
  $newDir = preg_replace("/[^\w\.@-]/", "", htmlspecialchars($_POST['newDir']));
  // if a filename still exists we can continue
  if($newDir != "")
  {
  // get the path passed as a hidden form field
    $path = $_POST['path'];
    // only make a directory if it can be populated
    if(((@ini_set("file_uploads", 1) === true)
    || ini_get("file_uploads") == 1)
  && is_writable($path))
    {
      // create the new directory and assign it full read + write permissions
      mkdir($path."/".$newDir, 0777);
    }
  }
}

if(isset($_FILES['userfile']['tmp_name']) && ($_POST['newDir']==""))
{
// <!-- END: create new directory feature added 2006-10-19 by RAD -->

// if(isset($_FILES['userfile']['tmp_name'])) { // old code replaced by new code above RAD
// this seems to be an upload action.
printf($_lang['files_uploading'], $_FILES['userfile']['name'], substr($startpath, $len, strlen($startpath)));
echo $_FILES['userfile']['error']==0 ? $_lang['files_file_type'].$_FILES['userfile']['type'].", ".fsize($_FILES['userfile']['tmp_name'])."<br />" : "" ;

  $userfile = $_FILES['userfile']['tmp_name'];

  if(is_uploaded_file($userfile))
  {
    // file is uploaded file, process it!
    if(!in_array(getExtension($_FILES['userfile']['name']), $uploadablefiles))
    {
      echo "<br /><span class='warning'>".$_lang['files_filetype_notok']."</span><br />";
    }
    else
    {
      if(@move_uploaded_file($_FILES['userfile']['tmp_name'], $_POST['path']."/".$_FILES['userfile']['name']))
      {
        @chmod($_POST['path']."/".$_FILES['userfile']['name'], 0644);
        echo "<br /><span class='success'>".$_lang['files_upload_ok']."</span><br />";
      }
      else
      {
        echo "<br /><span class='warning'>".$_lang['files_upload_copy_failed']."</span> Possible permission problems - the directory you want to upload to needs to be set to 0777 permissions.<br />";
      }
    }
  }
  else
  {
    echo "<br /><span class='warning'><b>".$_lang['files_upload_error'].":</b> ";
    switch($_FILES['userfile']['error'])
    {
      case 0: //no error; possible file attack!
        echo $_lang['files_upload_error0'];
      break;
      case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
        echo $_lang['files_upload_error1'];
      break;
      case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
        echo $_lang['files_upload_error2'];
      break;
      case 3: //uploaded file was only partially uploaded
        echo $_lang['files_upload_error3'];
      break;
      case 4: //no file was uploaded
        echo $_lang['files_upload_error4'];
      break;
      default: //a default error, just in case!  :)
        echo $_lang['files_upload_error5'];
      break;
    }
    echo "</span><br />";
  }
  echo "<hr/>";
}

if($_POST['mode'] == "save")
{
  echo $_lang['editing_file'];
  $filename = $_POST['path'];
  $content = $_POST['content'];
  if (!$handle = fopen($filename, 'w'))
  {
    echo "Cannot open file ($filename)";
    exit;
  }

  // Write $content to our opened file.
  if(fwrite($handle, $content) === FALSE)
  {
    echo "<span class='warning'><b>".$_lang['file_not_saved']."</b></span><br /><br />";
  }
  else
  {
    echo "<span class='success'><b>".$_lang['file_saved']."</b></span><br /><br />";
    $_REQUEST['mode'] = "edit";
  }
  fclose($handle);
  // Read and write for owner, read for everybody else
  @chmod($filename, 0644);
}

if($_REQUEST['mode'] == "delete")
{
  printf($_lang['deleting_file'], str_replace('\\', '/', $_REQUEST['path']));
  $file = $_REQUEST['path'];
  if(!@unlink($file))
  {
    echo "<span class='warning'><b>".$_lang['file_not_deleted']."</b></span><br /><br />";
  }
  else
  {
    echo "<span class='success'><b>".$_lang['file_deleted']."</b></span><br /><br />";
  }
}

echo $_lang['files_dir_listing'];

?>

<b>

<?php
echo substr($startpath, $len, strlen($startpath))=="" ? "/" : substr($startpath, $len, strlen($startpath)) ;
?>

</b>
<br />
<br />

<?php

// check to see user isn't trying to move below the document_root
if(substr(strtolower($startpath), 0, strlen($filemanager_path))!= strtolower($filemanager_path))
{
?>

<?php echo $_lang['files_access_denied']; ?>
</div>

<?php
  exit;
}

$uponelevel = removeLastPath($startpath);

if($startpath == $filemanager_path)
{
  echo "<img src='media/images/tree/deletedfolder.gif' border=0><span style='color:#bbb;cursor:default;'> <b>".$_lang['files_top_level']."</b></span><br />\n";
}
else
{
  echo "<img src='media/images/tree/folder.gif' border=0> <a href='index.php?a=31&mode=drill&path=".$savepath."'><b>".$_lang['files_top_level']."</b></a><br />\n";
} # Original variable in line above: $savepath was $_SERVER['DOCUMENT_ROOT'] originally

if($startpath == $filemanager_path)
{
  echo "<img src='media/images/tree/deletedfolder.gif' border=0><span style='color:#bbb;cursor:default;'> <b>".$_lang['files_up_level']."</b></span><br />\n";
}
else
{
  echo "<a href='index.php?a=31&mode=drill&path=$uponelevel'><img src='media/images/tree/folder.gif' border=0> <b>".$_lang['files_up_level']."</b></a><br />\n";
}
echo "<br />";

$filesize = 0;
$files = 0;
$folders = 0;
$dirs_array = array();
$files_array = array();

function ls($curpath)
{
  global $excludes, $editablefiles, $inlineviewablefiles, $viewablefiles, $folders, $files, $filesizes, $len, $dirs_array, $files_array, $timestampFormat, $server_offset_time;
  $dircounter = 0;
  $filecounter = 0;
  $dir = dir($curpath);

  // first, get info
  while($file = $dir->read())
  {
    if(!in_array($file, $excludes))
    {
      $newpath = $curpath."/".$file;
      if(is_dir($newpath))
      {
        $dirs_array[$dircounter]['dir'] = $newpath;
        $dirs_array[$dircounter]['stats'] = lstat($newpath);
        $dirs_array[$dircounter]['text'] = "<img src='media/images/tree/folder.gif' border=0> <a href='index.php?a=31&mode=drill&path=$newpath'><b style=' font-size: 11px;'>$file</b></a>";
        // increment the counter
        $dircounter++;
        }
        else
        {
          $type=getExtension($newpath);
          $files_array[$filecounter]['file'] = $newpath;
          $files_array[$filecounter]['stats'] = lstat($newpath);
          $files_array[$filecounter]['text'] = "<img src='media/images/tree/page.gif' border=0> $file";
          $files_array[$filecounter]['view'] = (in_array($type, $viewablefiles)) ? "<span style='cursor:pointer; width:20px;' onClick='viewfile(\"".substr($newpath, $len, strlen($newpath))."\");'><img src='media/images/icons/context_view.gif' border=0></span>&nbsp;" : "<span class='disabledImage'><img src='media/images/icons/context_view.gif' border=0></span>&nbsp;" ;
          $files_array[$filecounter]['view'] = (in_array($type, $inlineviewablefiles)) ? "<span style='width:20px;'><a href='index.php?a=31&mode=view&path=$newpath'><img src='media/images/icons/context_view.gif' border=0></a></span>&nbsp;" : $files_array[$filecounter]['view'] ;
          $files_array[$filecounter]['edit'] = (in_array($type, $editablefiles) && is_writable($curpath) && is_writable($newpath)) ? "<span style='width:20px;'><a href='index.php?a=31&mode=edit&path=$newpath'><img src='media/images/icons/save.gif' border=0></a></span>&nbsp;" : "<span class='disabledImage'><img src='media/images/icons/save.gif' border=0></span>&nbsp;" ;
          $files_array[$filecounter]['delete'] = is_writable($curpath) && is_writable($newpath) ? "<span style='width:20px;'><a href='javascript:confirmDelete(\"".addslashes("index.php?a=31&mode=delete&path=$newpath")."\");'><img src='media/images/icons/delete.gif' border=0></a></span>" : "<span class='disabledImage'><img src='media/images/icons/delete.gif' border=0></span>" ;
          // increment the counter
          $filecounter++;
        }
      }
    }
    $dir->close();

    // dump array entries for directories
    $folders = count($dirs_array);
    sort($dirs_array);
    for($i=0; $i<$folders; $i++)
    {
      $filesizes += $dirs_array[$i]['stats']['7'];
      echo "<div style='position: relative; float: left; width: 300px; font-size: 11px;'>".$dirs_array[$i]['text']."</div>";
      echo "<div style='position: relative; float: left; width: 140px; text-align:right; font-size: 11px;'>".strftime($timestampFormat, $dirs_array[$i]['stats']['9'] + $server_offset_time)."</div>";
      echo "<div style='position: relative; float: left; width: 120px; text-align:right; font-size: 11px;'>".ufilesize($dirs_array[$i]['stats']['7'])."</div>";
      echo "<br clear='all' />\n";
    }

    // dump array entries for files
    $files = count($files_array);
    sort($files_array);
    for($i=0; $i<$files; $i++)
    {
      $filesizes += $files_array[$i]['stats']['7'];
      echo "<div style='position: relative; float: left; width: 300px; font-size: 11px;'>".$files_array[$i]['text']."</div>";
      echo "<div style='position: relative; float: left; width: 140px; text-align:right; font-size: 11px;'>".strftime($timestampFormat, $files_array[$i]['stats']['9'] + $server_offset_time)."</div>";
      echo "<div style='position: relative; float: left; width: 120px; text-align:right; font-size: 11px;'>".ufilesize($files_array[$i]['stats']['7'])."</div>";
      echo "<div style='position: relative; float: left; width: 120px; text-align:right; font-size: 11px;'>";
      echo $files_array[$i]['view'];
      echo $files_array[$i]['edit'];
      echo $files_array[$i]['delete'];
      echo "</div>";
      echo "<br clear='all' />\n";
    }
    return;
  }
  echo "\n\n\n\n\n\n\n";
?>

<div style='position: relative; float: left; width: 300px; font-size: 11px;'>
  <b><?php echo $_lang['files_filename']; ?></b>
</div>

<div style='position: relative; float: left; width: 140px; text-align:right; font-size: 11px;'>
  <b><?php echo $_lang['files_modified']; ?></b>
</div>

<div style='position: relative; float: left; width: 120px; text-align:right; font-size: 11px;'>
  <b><?php echo $_lang['files_filesize']; ?></b>
</div>

<div style='position: relative; float: left; width: 100px; text-align:right; font-size: 11px;'>
  <b><?php echo $_lang['files_fileoptions']; ?></b>
</div>

<br /><br />
<?php
  ls($startpath);
  echo "\n\n\n\n\n\n\n";
  if($folders == 0 && $files == 0)
  {
    echo "<img src='media/images/tree/deletedfolder.gif' border=0><span style='color:#888;cursor:default;'> This directory is empty.</span><br />\n";
  }

  echo "<br />";
  echo "<div style='position: relative; float: left; width: 140px;'>".$_lang['files_directories'].":</div><b>$folders</b><br />";
  echo "<div style='position: relative; float: left; width: 140px;'>".$_lang['files_files'].":</div><b>$files</b><br />";
  echo "<div style='position: relative; float: left; width: 140px;'>".$_lang['files_data'].":</div><b>".ufilesize($filesizes)."</b><br />";
?>

<span style='position: relative; float: left; width: 140px;'>
  <?php echo $_lang['files_dirwritable']; ?>
</span>
<b><?php echo is_writable($startpath)==1 ? "Yes." : "No."; ?></b><br />


<?php
  if(is_writable($startpath) == 1)
  {
    $max = ufilesize($uploadmaxsize);
    echo "
    <span style='position: relative; float: left; width: 140px;'>
      {$_lang['maxuploadsize_title']}
    </span>
    <b>{$max}</b><br />
    ";
  }
?>

<script type="text/javascript">
function viewfile(url) {
  document.getElementById('imageviewer').style.border="1px solid #000080";
  document.getElementById('imageviewer').src=url;
}

function confirmDelete(url)
{
  if(confirm("<?php echo $_lang['confirm_delete_file'] ?>"))
  {
    document.location.href=url;
  }
}
</script>

<div align="center">
  <img src="media/images/_tx_.gif" id='imageviewer'>
</div>
<hr />

<?php
  if (((@ini_set("file_uploads", 1) === true)
  || get_cfg_var("file_uploads") == 1)
  && is_writable($startpath))
  {
    @ini_set("upload_max_filesize", $uploadmaxsize);
?>

<form enctype="multipart/form-data" action="index.php?a=31" method="post">
  <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $uploadmaxsize; ?>">
  <input type="hidden" name="path" value="<?php echo $startpath; ?>">
  <b><?php echo $_lang['files_uploadfile']; ?></b><br />
  <span style="width:300px;">
  <?php echo $_lang['files_uploadfile_msg']; ?>
  </span>
  <input class="doSomethingButton" name="userfile" type="file" style="height: 20px;">
  <input class="doSomethingButton" type="submit" value="<?php echo $_lang['files_uploadfile']; ?>">

<!-- START: create new directory feature added 2006-10-19 by RAD -->
  <br />
  <span style="width:300px;">
    <?php echo $_lang['create_new_dir']; ?>
  </span>
  <input type="text" name="newDir" style="height: 20px; margin: 10px 0 0 15px;" />
  <input class="doSomethingButton" type="submit" value="<?php echo $_lang['create_new_dir_button']; ?>">
  <br />
  <span><?php echo $_lang['create_new_dir_note']; ?></span>
<!-- END: create new directory feature added 2006-10-19 by RAD -->

</form>

<?php
  }
  else
  {
    echo $_lang['files_upload_inhibited_msg'];
  }
?>


</div>

<?php
}
?>

<?php
function ufilesize($size)
{
  $a = array("B", "KB", "MB", "GB", "TB", "PB");
  $pos = 0;
  while ($size >= 1024)
  {
    $size /= 1024;
    $pos++;
  }
  return round($size,2)." ".$a[$pos];
}

function removeLastPath($string)
{
  $pos = false;
  $search = "/";
  if(is_int(strpos($string, $search)))
  {
    $endPos = strlen($string);
    while ($endPos > 0)
    {
      $endPos = $endPos - 1;
      $pos = strpos($string, $search, $endPos);
      if(is_int($pos))
      {
        break;
      }
    }
  }
  if (is_int($pos))
  {
    $len = strlen($search);
    return substr($string, 0, $pos);
  }
  return $string;
}

function getExtension($string)
{
  $pos = false;
  $search = ".";
  $string = strtolower($string);
  if(is_int(strpos($string, $search)))
  {
    $endPos = strlen($string);
    while($endPos > 0)
    {
      $endPos = $endPos - 1;
      $pos = strpos($string, $search, $endPos);
      if(is_int($pos))
      {
        break;
      }
    }
  }
  if(is_int($pos))
  {
    $len = strlen($search);
    return substr($string, $pos);
  }
  return $string;
}

function fsize($file)
{
  $a = array("B", "KB", "MB", "GB", "TB", "PB");
  $pos = 0;
  $size = filesize($file);
  while($size >= 1024)
  {
    $size /= 1024;
    $pos++;
  }
  return round($size,2)." ".$a[$pos];
}

?>
