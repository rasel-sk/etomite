<?php
// header.inc.php
// Creates the top section of each manager page
// Modified 2008-03-26 [v1.0] by Ralph - general cleanup
// Modified: 2008-05-08 [v1.1] by: Ralph A. Dahlgren

if(IN_ETOMITE_SYSTEM != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

// count messages
$sql = "SELECT count(*) FROM $dbase.".$table_prefix."user_messages where recipient=".$_SESSION['internalKey']." and messageread=0;";
$rs = mysql_query($sql);
$row = mysql_fetch_assoc($rs);
$_SESSION['nrnewmessages'] = $row['count(*)'];
$sql = "SELECT count(*) FROM $dbase.".$table_prefix."user_messages where recipient=".$_SESSION['internalKey']."";
$rs = mysql_query($sql);
$row = mysql_fetch_assoc($rs);
$_SESSION['nrtotalmessages'] = $row['count(*)'];
$messagesallowed = $_SESSION['permissions']['messages'];

if(strstr($_SERVER['HTTP_USER_AGENT'],'MSIE 7') && isset($_REQUEST['a']) && ($_REQUEST['a']=='4'||$_REQUEST['a']=='27') && $which_editor==5
      && $use_doc_editor==1){
/*If we use RTE in IE 7, set document type to xhtml (fixes RTE issue)*/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<?php } else {?>

<html>

<?php }?>

<head>
  <title>Etomite</title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $etomite_charset; ?>">
  <link rel="stylesheet" type="text/css" href="./media/style/style.css">
  <link type="text/css" rel="stylesheet" href="media/style/tabs.css" />
 <?php
  # Start - Texyla load
  if($which_editor == 6)
  {
    echo '<script type="text/javascript" src="http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . '/media/texyla/texyla-source.js" ></script>' . "\n";
  }
?>
  <script type="text/javascript" src="./media/script/ieemu.js"></script>

<?php if(file_exists('media/edit_area/')) { ?>

<script language="javascript" type="text/javascript" src="./media/edit_area/edit_area/edit_area_full.js"></script>

<?php } ?>

  <script language="JavaScript" type="text/javascript">
  //<![CDATA[
    if (/Mozilla\/5\.0/.test(navigator.userAgent))
    document.write('<script type="text/javascript" src="./media/script/mozInnerHTML.js"><\/script>');

    // set tree to default action.
    parent.menu.ca = "open";

    function msgCount() {
      try {
        top.scripter.startmsgcount(<?php echo $_SESSION['nrnewmessages'] ; ?>,<?php echo $_SESSION['nrtotalmessages'] ; ?>,<?php echo $messagesallowed ; ?>);
      } catch(oException) {
        ww = window.setTimeout('msgCount()',1000);
      }
    }

    function stopWorker() {
      try {
        parent.scripter.stopWork();
      } catch(oException) {
        ww = window.setTimeout('stopWorker()',500);
      }
    }

    function doRefresh(r) {
      try {
        rr = r;
        top.scripter.startrefresh(rr);
      } catch(oException) {
        vv = window.setTimeout('doRefresh()',1000);
      }
    }
    var documentDirty=false;

    function checkDirt() {
      if(documentDirty==true) {
        event.returnValue = "<?php echo $_lang['warning_not_saved']; ?>";
      }
    }

    function saveWait(fName) {
      document.getElementById("savingMessage").innerHTML = "<?php echo $_lang['saving']; ?>";
      for(i = 0; i < document.forms[fName].elements.length; i++) {
        document.forms[fName].elements[i].disabled='disabled';
      }
    }

    var managerPath = "";

    function hideLoader() {
      document.getElementById('preLoader').style.display = "none";
    }

    retry=0;
    function loadagain(id) {
      try {
        top.menu.Sync(<?php echo $syncid; ?>);
      } catch(oException) {
        retry=retry + 1;
        if(retry<4) {
          xyy=window.setTimeout("loadagain(<?php echo $syncid; ?>)", 2000);
        } else {
          //alert("Failed to sync to tree!");
        }
      }
    }

    hideL = window.setTimeout("hideLoader()", 2500);

  //]]>
  </script>

<?php if($_SESSION['browser']=='ie') { ?>

  <style>
  /* stupid box model hack for equally stupid MSIE */
  .sectionHeader, .sectionBody {
    width:100%;
  }
  </style>

<?php } ?>

</head>
<body ondragstart="return false"
      onLoad='stopWorker(); msgCount(); hideLoader(); <?php echo isset($_REQUEST['r']) ? " doRefresh(".$_REQUEST['r'].");" : "" ;?>'
      onbeforeunload="checkDirt();"
      onUnload="top.scripter.work();">

<div id="preLoader">
  <table border="0" cellpadding="0" align="center">
    <tr>
      <td align="center">
        <div id="preLoaderText">
          <?php echo $_lang['loading_page']; ?>
        </div>
      </td>
    </tr>
  </table>
</div>

