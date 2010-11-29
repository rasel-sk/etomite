<?php /* DRAG & DROP SORTABLE MENU TREE v 0.4.4, a mod by Johan Larsson (nalagar) */

//AJAX CALL, WE SET NEW PARENT AND PRINT ONLY THE MENU TREE
if(isset($_POST['id'])&&isNumber($_POST['id'])&&isset($_POST['newparent'])&&isNumber($_POST['newparent'])&&isset($_POST['scrollTop'])&&isNumber($_POST['scrollTop'])) { //CALLED BY AJAX, RETURN FULL TREE

   $cwd = getcwd();
   include($cwd.'/includes/config.inc.php');

   include_once("./processors/user_documents_permissions.class.php");
   $udperms = new udperms();
   $udperms->user = $_SESSION['internalKey'];
   $udperms->document = $_POST['id'];
   $udperms->role = $_SESSION['role'];

   $udperms2 = new udperms();
   $udperms2->user = $_SESSION['internalKey'];
   $udperms2->document = $_POST['newparent'];
   $udperms2->role = $_SESSION['role'];

   $handle = mysql_connect($database_server, $database_user, $database_password)or die('Could not connect: ' . mysql_error());
   mysql_select_db(str_replace("`","",$dbase)) or die('Could not select database');
   $db = $dbase.".".$table_prefix;
   $sql = "SELECT id from ".$db."site_content WHERE id=" . $_POST['newparent'] . " AND parent=" . $_POST['id']; //Make sure the new parent doesn't have the child as parent!
   $rs = mysql_query($sql) or die('Query failed: ' . mysql_error());
   $row = mysql_fetch_row($rs);
   if($_SESSION['permissions']['edit_document']!=1 || !$udperms->checkPermissions() || !$udperms2->checkPermissions())
        $allowed = false;
   else
        $allowed=true;
   if( (!$row||count($row)<1) && $allowed) { //Ok we have a healthy parent/child relationship and have access rights

	$sql = "UPDATE ".$db."site_content SET parent=" . $_POST['newparent'] . " WHERE id=" . $_POST['id'] ." LIMIT 1";
	$rs = mysql_query($sql) or die('Query failed: BEGIN' . $sql . 'END ' . mysql_error());
	$sql = "UPDATE ".$db."site_content SET isfolder=1 WHERE id=" . $_POST['newparent'] ." LIMIT 1";
	$rs = mysql_query($sql) or die('Query failed: ' . mysql_error());
	$sql = "UPDATE ".$db."site_content t1 LEFT JOIN ".$db."site_content t2 ON t1.id=t2.parent SET t1.isfolder=0 WHERE t2.parent IS NULL"; //Items should not be folders if they don't have any child documents
	$rs = mysql_query($sql) or die('Query failed: ' . mysql_error());
        $scriptToReturn="<script type=\"text/javascript\">ptmResetStates();document.body.scrollTop=" . $_POST['scrollTop'] . "; document.getElementById('workingmess').style.cursor = ''; document.getElementById('workingmess').style.display = 'none'; if(parent.main.document.mutate && parent.main.document.mutate.a && parent.main.document.mutate.a.value=='5' && parent.main.document.mutate.id.value=='".$_POST['id']."'){ parent.main.document.mutate.parent.value='".$_POST['newparent']."'; parent.main.document.getElementById('parentName').innerHTML = '".$_POST['newparent']."';}</script>";
   }
   else {
        if(!$allowed)
            $scriptToReturn="<script type=\"text/javascript\">alert('" . $_lang['access_permission_denied'] . "'); document.getElementById('workingmess').style.display = 'none';</script>";
        else
            $scriptToReturn="<script type=\"text/javascript\">alert('" . $_lang['tree_refresh_needed'] . "'); document.getElementById('workingmess').style.display = 'none';</script>";
   }

   /*
   if(isset($_POST['sortby']) && ($_POST['sortby']=='pagetitle'||$_POST['sortby']=='selected'||$_POST['sortby']=='menuindex'||$_POST['sortby']=='isfolder'||$_POST['sortby']=='createdon'||$_POST['sortby']=='editedon') )
        $orderby = $_POST['sortby'];
   else
        $orderby = "menuindex";
   if(isset($_POST['sortdir']) && $_POST['sortdir']=='DESC')
        $sortDir = 'DESC';
   else
        $sortDir = 'ASC'; */

   $orderby = isset($_SESSION['tree_sortby']) ? $_SESSION['tree_sortby'] : "menuindex";
   $sortDir = isset($_SESSION['tree_sortdir']) ? $_SESSION['tree_sortdir'] : "ASC";



   $fields = "id, type, pagetitle, alias, published, parent, isfolder, menuindex, deleted, showinmenu";
   $id = 0;
   $level = 1;
   $ptmLabel = 0;

   $menu = generateMenu($handle,$id,$orderby,$sortDir,$fields,$level,$ptmLabel,$db,"ul.ptm0"); //SORTABLE EDIT: NEW PARAMETER SUPPLIED TO generateMenu()
   $menu.= $scriptToReturn;
   echo $menu;
   exit(0);
} //END AJAX CALL
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- New Etomite Document Tree for 0.6.1 -- Created by: Ralph A. Dahlgren -->
<!-- Last Modified: 2005-09-30 : Modified to handle multiple folder types -->

<!-- DRAG & DROP SORTABLE MENU TREE v 0.4.4, a mod by Johan Larsson (nalagar) -->

<!-- START: HTML Page Information -->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Eto-ptm</title>

<?php
// Go get the configuration settings
$cwd = getcwd();
include($cwd.'/includes/config.inc.php');
/*  START: Connect to database and create the document tree  */
$handle = mysql_connect($database_server, $database_user, $database_password)or die('Could not connect: ' . mysql_error());
mysql_select_db(str_replace("`","",$dbase)) or die('Could not select database');
$db = $dbase.".".$table_prefix;


/* SORTABLE EDIT: START OF STORING THE NEW ORDER IN DATABASE */

function isNumber($var)
{
  if(strlen($var)==0) {
    return false;
  }
  for ($i=0;$i<strlen($var);$i++) {
    if ( substr_count ("0123456789", substr ($var, $i, 1) ) == 0 ) {
      return false;
    }
    }
  return true;
}

if(isset($_POST['id']) && isNumber($_POST['id']) && isset($_POST['orderby']) && isset($_POST['listing']) ) {

  $id = $_POST['id'];
  echo "<script type=\"text/javascript\">";
  // check permissions on the document
  include_once("./processors/user_documents_permissions.class.php");
  $udperms = new udperms();
  $udperms->user = $_SESSION['internalKey'];
  $udperms->document = $id;
  $udperms->role = $_SESSION['role'];

  if(!$udperms->checkPermissions()) {
    echo "alert(\"" . $_lang['access_permission_denied'] . "\");";
    if(strstr($_SERVER['HTTP_USER_AGENT'],'MSIE'))
        echo "parent.document.location.reload();";
    else
        echo "parent.document.location.replace(parent.document.location.href);"; //USE THIS INSTEAD OF LOCATION.RELOAD() TO RELOAD PAGE (FF ISSUE)
    echo "</script></head><body></body></html>";
    exit(0);
  }

  $orderby = mysql_escape_string($_POST['orderby']);

  $listingarr = explode('&',str_replace('list[]=','',mysql_escape_string($_POST['listing'])));
  if($orderby=='DESC') {
    $listingarr = array_reverse($listingarr);
  }
  else {
    $orderby = 'ASC';
  }








  $tomysql0 = "SELECT parent FROM ".$db."site_content WHERE id=" . $id;
  $rs0 = mysql_query($tomysql0) or die('Query failed: ' . mysql_error());
  if($rs0==false) {
     echo "alert('Could not get current order from database');</script></head><body></body></html>";
     exit(0);
  }
  $DraggedParent="";
  $DraggedParent = mysql_fetch_row($rs0);




  $testtext="";
  $firsttime=true;
  $displayorder = str_replace('list[]=','',mysql_escape_string($_POST['listing']));



  if(strlen($DraggedParent[0])>0) {
     $tomysql1 = "SELECT id FROM ".$db."site_content WHERE parent=" . $DraggedParent[0];

     $rs = mysql_query($tomysql1) or die('Query failed: ' . mysql_error());
     if($rs==false) {
        echo "alert('Could not get current order from database');</script></head><body></body></html>";
        exit(0);
     }

     while(list($thisid) = mysql_fetch_row($rs)) {
        if(array_search($thisid,$listingarr)===false) {
          $testtext='';
          break;
        }
        if(!$firsttime)
           $testtext .= "&";
        $testtext .= $thisid;
        $firsttime=false;
     }
   }
   if(strlen($testtext)!=strlen($displayorder)) {
     echo "if(parent.parent.main.document.mutate && parent.parent.main.document.mutate.a && parent.parent.main.document.mutate.a.value=='5')\n";
     echo "alert('" . $_lang['tree_refresh_save_first'] . "');\n";
     echo "else\n";
     echo "alert('" . $_lang['tree_refresh_needed'] . "');\n";

     if(strstr($_SERVER['HTTP_USER_AGENT'],'MSIE'))
        echo "parent.document.location.reload();";
     else
        echo "parent.document.location.replace(parent.document.location.href);"; //USE THIS INSTEAD OF LOCATION.RELOAD() TO RELOAD PAGE (FF ISSUE)
  }
  else {
     $tomysql2 = "UPDATE ".$db."site_content SET menuindex = CASE id ";
     $midcount = 1;
     foreach($listingarr as $value) {
       if(isNumber($value)) {
         $tomysql2 .= "WHEN " . $value . " THEN " . $midcount . " ";
         $midcount++;
       }
     }
     $tomysql2 .= "ELSE menuindex END";
     if($midcount>1)
        $rs2 = mysql_query($tomysql2) or die('Query failed: ' . mysql_error());
     if($rs2==false) {
        echo "alert('Could not store new tree order in database');";
     }
     echo "parent.document.getElementById('workingmess').style.display = 'none';";

     if(isset($_POST['editid']) && $_POST['editid']!="" && isNumber($_POST['editid'])) {
         echo "if(parent.parent.main.document.mutate && parent.parent.main.document.mutate.a && parent.parent.main.document.mutate.a.value=='5')";
         echo "    parent.parent.main.document.mutate.menuindex.value=" . $_POST['editid'] . ";";  //Update menuindex if we are editing a document
     }


  }
  echo "location.href='frames/index.php';</script></head><body></body></html>"; //GET A DOCUMENT WITH NO POST DATA IN THE IFRAME SO FF CAN RELOAD THE TREE WITHOUT PROBLEMS
  exit(0);
}

/* SORTABLE EDIT: END OF STORING THE NEW ORDER IN DATABASE */


$sql = "SELECT COUNT(*) FROM ".$db."site_content WHERE deleted=1";
$rs = mysql_query($sql);
$row = mysql_fetch_row($rs);
$count = $row[0];
?>


<script type="text/javascript" src="media/ptm/ptm.js"></script>
<link rel="stylesheet" type="text/css" href="media/style/style.css" />
<!--<style type="text/css">li{list-image:none;}</style>-->

<!-- SORTABLE EDIT: START -->

<script src="frames/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="frames/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script src="frames/scriptaculous/dragsort_etomite.js" type="text/javascript"></script>


<style>
   .handle {
      color: #aaccdd;
      font-family: 'Times New Roman','Arial';
<?php if(strstr($_SERVER['HTTP_USER_AGENT'],'MSIE')) { ?>
      font-size: 12px;
      font-weight: bold;
<?php } else { ?>
      font-size: 9px;
<?php } ?>
      cursor: move;
   }
   div { width:100%; }
</style>

<!-- SORTABLE EDIT: END -->

</head><!-- SORTABLE EDIT BELOW: ADDED STYLE ATTRIBUTE TO BODY TAG BUT PROBABLY NOT NEEDED, ADDED MOUSOVER CALL TO BUFFEREDITORSELECTION() -->
<body onload="sessionName='<?php echo $site_id . "_ptm"; ?>'; ptmResetStates();" id="ptm" style="height:100%" onmouseover="bufferEditorSelection();">
<!-- END: HTML Page Information -->

<!-- START: Doc Tree Sort Window Code -->
<div id="floater">
  <table width="99%"  border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td style="padding-left: 5px;padding-top: 0px;">
    <form name="sortFrm" method="post" style="margin: 0px; padding: 0px;">
      <select name="sortby" style="font-size: 9px;">
        <option value="pagetitle" <?php echo $_SESSION['tree_sortby']=='pagetitle' ? "selected='selected'" : "" ?>><?php echo $_lang['pagetitle']; ?></option>
        <option value="id" <?php echo $_SESSION['tree_sortby']=='id' ? "selected='selected'" : "" ?>><?php echo $_lang['id']; ?></option>
        <option value="menuindex" <?php echo $_SESSION['tree_sortby']=='menuindex' ? "selected='selected'" : "" ?>><?php echo $_lang['document_opt_menu_index']; ?> </option>
        <option value="isfolder" <?php echo $_SESSION['tree_sortby']=='isfolder' ? "selected='selected'" : "" ?>><?php echo $_lang['folder']; ?></option>
        <option value="createdon" <?php echo $_SESSION['tree_sortby']=='createdon' ? "selected='selected'" : "" ?>><?php echo $_lang['createdon']; ?></option>
        <option value="editedon" <?php echo $_SESSION['tree_sortby']=='editedon' ? "selected='selected'" : "" ?>><?php echo $_lang['editedon']; ?></option>
      </select>
    </td>
    <td width="1%" align="right" valign="top" style="padding-left: 3px; padding-right: 2px" onclick="showSorter();" title="<?php echo $_lang['cancel']; ?>"><img src="media/images/icons/close.gif">
    </td>
    </tr>
    <tr height="18">
      <td width="99%" style="padding-left: 5px;padding-top: 1px;">
      <select name="sortdir" style="font-size: 9px;">
        <option value="ASC" <?php echo $_SESSION['tree_sortdir']=='ASC' ? "selected='selected'" : "" ?>><?php echo $_lang['sort_asc']; ?></option>
        <option value="DESC" <?php echo $_SESSION['tree_sortdir']=='DESC' ? "selected='selected'" : "" ?>><?php echo $_lang['sort_desc']; ?></option>
      </select>

      <input type='hidden' name='dt' value='<?php echo $_REQUEST['dt']; ?>'>
    </form>
    </td>
    <td width="1%" align="right" onclick="updateTree();" title="<?php echo $_lang['sort_tree']; ?>">
      <img src="media/images/icons/sort-do.gif">
    </td>
    </tr>
  </table>
</div>
<!-- END: Doc Tree Sort Window Code -->

<div id="docTree">
<!-- START: Document Tree Control Buttons, SORTABLE EDIT: highlight code and allow redraw code for recycle bin and search icon -->
<div class="subTitleTree">
  &nbsp;<img src="media/images/icons/down.gif" class="docTreeBtn" onclick="ptmExpand();" title="<?php echo $_lang['expand_tree']; ?>">
  &nbsp;<img src="media/images/icons/up.gif" class="docTreeBtn" onclick="ptmCollapse();" title="<?php echo $_lang['collapse_tree']; ?>">
  &nbsp;<img src="media/images/icons/tree_search.gif" class="docTreeBtn" onclick="if( allowredraw() ) top.main.document.location.href='index.php?a=71';" title="<?php echo $_lang['search']; ?>">
  &nbsp;<img src="media/images/icons/refresh.gif" class="docTreeBtn" onclick="location.reload();" title="<?php echo $_lang['refresh_tree']; ?>">
  &nbsp;<img src="media/images/icons/sort.gif" class="docTreeBtn" onclick="document.getElementById('floater').style.top=document.body.scrollTop + 30 + 'px'; showSorter();" title="<?php echo $_lang['sort_tree']; ?>">
  &nbsp;<span style="width:16px; padding-top:6px; padding-right:5px; padding-left:2px; margin-top:-6px; margin-right:-5px; margin-left:-2px;"><img src="media/images/tree/trash<?php echo $count>0 ? "_full" : ""; ?>.gif" id="recycle" class="docTreeBtn" onclick="if( allowredraw() ) emptyTrash();" title="<?php echo $count>0 ? $_lang['empty_recycle_bin'] : $_lang['empty_recycle_bin_empty'] ; ?>"></span>
  &nbsp;<img src="media/images/tree/tree_close.gif" class="docTreeBtn" onclick="top.scripter.hideTreeFrame();" title="<?php echo $_lang['hide_tree']; ?>">
</div>
<!-- END: Document Tree Control Buttons -->

  <!-- START: Root of Document Tree -->
  <li id="id_0" style="padding-left:0px"><div onmouseover="addClass(this,'mouseover');" onmouseout="delClass(this,'mouseover');"><!-- DRAG FOR PARENT: ROOT NEED TO BE A LI ELEMENT TOO -->
    &nbsp;<img src="media/images/tree/globe.gif" align="absmiddle" width="19" height="18">&nbsp;
    <span oncontextmenu="docid=0;
                   pagetitle='<?php echo $site_name; ?>';
                   document.getElementById('contextmenu').style.top = document.body.scrollTop + 2 + 'px';
                   document.getElementById('contextmenu').style.display = 'block';
                   contextMenu();
                   return false;"><a <?php echo 'onclick="treeAction(0,\''.$site_name.'\');"'; ?>><b><?php echo $site_name; ?></b></a></span>
  </div>
  <!-- END: Root of Document Tree -->

<?php
if(isset($_REQUEST['tree_sortby'])) $_SESSION['tree_sortby'] = $_REQUEST['tree_sortby'];
if(isset($_REQUEST['tree_sortdir'])) $_SESSION['tree_sortdir'] = $_REQUEST['tree_sortdir'];
if($_SESSION['permissions']['edit_document']==true) echo '<script type="text/javascript">canEdit=true;</script>';
if($_SESSION['permissions']['new_document']==true) echo '<script type="text/javascript">canCreate=true;</script>';
if($_SESSION['permissions']['edit_document']==true) echo '<script type="text/javascript">canDelete=true;</script>';

/*  Query and Variable Settings  */
$orderby = isset($_SESSION['tree_sortby']) ? $_SESSION['tree_sortby'] : "menuindex";
$sortDir = isset($_SESSION['tree_sortdir']) ? $_SESSION['tree_sortdir'] : "ASC";
$fields = "id, type, pagetitle, alias, published, parent, isfolder, menuindex, deleted, showinmenu";
$id = 0;
$level = 1;
$ptmLabel = 0;

$menu = "<ul id=\"ul.ptm0\">\n"; //SORTABLE EDIT: NEW ID NEEDED BY THE SCRIPTACULOUS SCRIPT
$menu .= generateMenu($handle,$id,$orderby,$sortDir,$fields,$level,$ptmLabel,$db,"ul.ptm0"); //SORTABLE EDIT: NEW PARAMETER SUPPLIED TO generateMenu()
$menu .= "</ul></li></div><br/><br/>\n";
echo $menu;

/*  END: Connect to database and create the document tree  */

/*  START: generateMenu creates the menu from the site_content table  */
function generateMenu($handle,$id,$orderby,$sortDir,$fields,$level,$ptmLabel,$db,$ulid) { //SORTABLE EDIT: NEW PARAMETER IN DECLARATION
  global $_lang;
  $sql = "SELECT $fields FROM ".$db."site_content WHERE parent=$id ORDER BY ". $orderby." ".$sortDir.";";
  $rs = mysql_query($sql) or die('Query failed: ' . mysql_error());
  if($rs==false) return false;
  while(list($id, $type, $pagetitle, $alias, $published, $parent, $isfolder, $menuindex, $deleted, $showinmenu) = mysql_fetch_row($rs)) {
    $jsActions = " oncontextmenu=\"docid=".$id.";
                   pagetitle='".addslashes($pagetitle)."';
                   document.getElementById('contextmenu').style.top = document.body.scrollTop + 2 + 'px';
                   document.getElementById('contextmenu').style.display = 'block';
                   contextMenu();
                   return false;\"";
    $target = " target=\"main\"";
    if($alias!="")
      $tooltip =  " title=\"" . $_lang['alias'] . ": ".$alias." - " . $_lang['document_opt_menu_index'] . ": ".$menuindex."\""; //SORTABLE EDIT: ADDED CHECK FOR ALIAS AND REMOVED \r\n BETWEEN BECAUSE FF DISPLAYS CRAP INSTEAD
    else
      $tooltip =  " title=\"" . $_lang['document_opt_menu_index'] . ": ".$menuindex."\"";
    $icon = "";
    $class = "class='publishedNode'";
    if(!$published){ $icon = "unpublished"; $class = "class='unpublishedNode'";}
    if($deleted){ $icon = "deleted"; $class = "class='deletedNode'";}
    if(!$showinmenu){$class = "class='hiddenNode'";}
    if(!$showinmenu && $deleted){$class = "class='hiddenNode deletedNode'";}
    if($type=="document"){ $icon .= $isfolder ? "folder" : "page";}
    elseif($type=="reference") $icon .= $isfolder ? "weblinkfolder" : "weblink";
    $icon .= ".gif";
    if($isfolder==true) {
      $ptmLabel = $GLOBALS['ptmLabel'];
      $ptmLabel++;
      $GLOBALS['ptmLabel'] = $ptmLabel;//SORTABLE EDIT BELOW: NEW ID NAME NEEDED BY THE SCRIPTACULOUS SCRIPT ON LI ELEMENT AND ELEMENTS WITH THE TITLE PARAMETERS. ADDED, IF NEEDED, RELOAD OF TREE AFTER CALL TO ptmToggle()
      $menu .= "<li id=\"id_".$id."\">
                  <div onmouseover=\"addClass(this,'mouseover');\" onmouseout=\"delClass(this,'mouseover');\">
                    <a doOnUp=\"ptm".$ptmLabel."\">
                      <img src=\"media/images/tree/".$icon."\" alt=\"\" id=\"img.ptm".$ptmLabel."\" onmousedown=\"makeDraggable(document.getElementById('id_".$id."'));\"  title=\"" . $_lang['tree_drag_parent'] . "\"/>
                    </a>
                    <a id=\"title_" . $id . "\" onclick=\" treeAction(".$id.",'".addslashes($pagetitle)."');\"".$target.$tooltip.">
                      <span onmouseover=\"addClass(this,'mouseover');\" onmouseout=\"delClass(this,'mouseover');\" ".$class.$jsActions.">".$pagetitle."</span>
                    </a>
                    &nbsp;<small>(".$id.")</small>";
      if($orderby=="menuindex") //SORTABLE EDIT: PRINT DRAG HANDLE IF TREE IS SORTED BY MENU INDEX
           $menu .= "&nbsp;<span class=\"handle\" title=\"" . $_lang['tree_drag_sort'] . "\" onMouseOver=\"init_drag('".$ulid."',$id);\" onMouseDown=\"drag_active=true;\" onMouseUp=\"drag_active=false;\">&#9650;&#9660;</span>";
      $menu .= "
                  </div>\n";
      $menu .= "<ul id=\"ul.ptm".$ptmLabel."\" class=\"closed\">\n";
      $ptmLabel = $ptmLabel + 1;
      $menu .= generateMenu($handle,$id,$orderby,$sortDir,$fields,$level+1,$ptmLabel,$db,"ul.ptm".($ptmLabel-1)); //SORTABLE EDIT: NEW PARAMETER SENT TO generateMenu()
      $menu .= "</ul>\n</li>\n";
    }
    else
    {    //SORTABLE EDIT BELOW: ID NEEDED BY THE SCRIPTACULOUS SCRIPT
      $menu .= "<li id=\"id_".$id."\">
                  <div onmouseover=\"addClass(this,'mouseover');\" onmouseout=\"delClass(this,'mouseover');\">
                    <img src=\"media/images/tree/".$icon."\" alt=\"\"  onmousedown=\"makeDraggable(document.getElementById('id_".$id."'));\" title=\"" . $_lang['tree_drag_parent'] . "\"/>
                    <span id=\"title_" . $id . "\" onclick=\" treeAction(".$id.",'".addslashes($pagetitle)."');\"".$target.$jsActions.$tooltip.">
                      <span onmouseover=\"addClass(this,'mouseover');\" onmouseout=\"delClass(this,'mouseover');\" ".$class.">".$pagetitle."</span>
                    </span>
                    &nbsp;<small>(".$id.")</small>";
      if($orderby=="menuindex") //SORTABLE EDIT: PRINT DRAG HANDLE IF TREE IS SORTED BY MENU INDEX
           $menu .= "&nbsp;<span class=\"handle\" title=\"" . $_lang['tree_drag_sort'] . "\" onMouseOver=\"init_drag('".$ulid."',$id);\" onMouseDown=\"drag_active=true;\" onMouseUp=\"drag_active=false;\">&#9650;&#9660;</span>";
      $menu .= "
                  </div>
                </li>\n";
    }
  }
  return $menu;
}
/*  END: function generateMenu() */
?>

<!-- START: Internal Javascript Code -->
<!-- Document Tree Context Menu -->
<div id="contextmenu"></div>
<script type="text/javascript">
var ca='open';
var menu='';

oldRecycleTitle = "<?php echo $count>0 ? $_lang['empty_recycle_bin'] : $_lang['empty_recycle_bin_empty'] ; ?>";
tree_save_changes = "<?php echo $_lang['tree_save_changes']; ?>";
tree_drop_recycle = "<?php echo $_lang['tree_drop_recycle']; ?>";


function contextMenu() { //SORTABLE EDIT: Added dialog to actions that would redraw the frameset if we are currently editing a document
  menu = '';
  menu += '<table  border="0" cellpadding="0" cellspacing="0">';
  menu += '  <tr>';
  menu += '    <td class="subTitle" style="width:auto;">';
  menu += '      &nbsp;<img src="media/images/icons/close.gif" onclick="menu=' + "''" + '; hideMenu();">';
  menu += '    </td>';
  menu += '    <td class="subTitle titleText" align="center">';
  menu +=  '     <span class="titleText">' + pagetitle + '</span>';
  menu += '      <span id="showdocid"></span>';
  menu += '    </td>';
  menu += '  </tr>';
  if(docid!=0) {
    menu += '  <tr onmouseover="addClass(this,\'mouseover\');" onmouseout="delClass(this,\'mouseover\');" onclick="runURL(3,docid);">';
    menu += '    <td>&nbsp;<img src="media/images/icons/preview.gif"></td>';
    menu += '    <td nowrap>&nbsp;<?php echo $_lang[view_document]; ?></td>';
    menu += '  </tr>';
    menu += '  <tr onmouseover="addClass(this,\'mouseover\');" onmouseout="delClass(this,\'mouseover\');" onclick="runURL(27,docid);">';
    menu += '    <td>&nbsp;<img src="media/images/icons/logging.gif"></td>';
    menu += '    <td nowrap>&nbsp;<?php echo $_lang[edit_document]; ?></td>';
    menu += '  </tr>';
    menu += '  <tr onmouseover="addClass(this,\'mouseover\');" onmouseout="delClass(this,\'mouseover\');" onclick="if(allowredraw()) runURL(51,docid); else hideMenu();">';
    menu += '    <td>&nbsp;<img src="media/images/icons/move.gif"></td>';
    menu += '    <td nowrap>&nbsp;<?php echo $_lang[move_document]; ?></td>';
    menu += '  </tr>';
  }
  <?php if($_SESSION['permissions']['new_document']==1) { ?>
    menu += '  <tr onmouseover="addClass(this,\'mouseover\');" onmouseout="delClass(this,\'mouseover\');" onclick="newDoc(4,docid);">';
    menu += '    <td>&nbsp;<img src="media/images/icons/newdoc.gif"></td>';
    menu += '    <td nowrap>&nbsp;<?php echo $_lang[create_document_here]; ?></td>';
    menu += '  </tr>';
    menu += '  <tr onmouseover="addClass(this,\'mouseover\');" onmouseout="delClass(this,\'mouseover\');" onclick="newDoc(72,docid);">';
    menu += '    <td>&nbsp;<img src="media/images/icons/weblink.gif"></td>';
    menu += '    <td nowrap>&nbsp;<?php echo $_lang[create_weblink_here]; ?></td>';
    menu += '  </tr>';
  <?php } ?>
  if(docid!=0) {
    menu += '  <tr onmouseover="addClass(this,\'mouseover\');" onmouseout="delClass(this,\'mouseover\');" onclick="if(allowredraw()) publishdocument(docid); else hideMenu();">';
    menu += '    <td>&nbsp;<img src="media/images/icons/item_published.gif"></td>';
    menu += '    <td nowrap>&nbsp;<?php echo $_lang[publish_document]; ?></td>';
    menu += '  </tr>';
    menu += '  <tr onmouseover="addClass(this,\'mouseover\');" onmouseout="delClass(this,\'mouseover\');" onclick="if(allowredraw()) unpublishdocument(docid); else hideMenu();">';
    menu += '    <td>&nbsp;<img src="media/images/icons/item_not_published.gif"></td>';
    menu += '    <td nowrap>&nbsp;<?php echo $_lang[unpublish_document]; ?></td>';
    menu += '  </tr>';
    menu += '  <tr onmouseover="addClass(this,\'mouseover\');" onmouseout="delClass(this,\'mouseover\');" onclick="if(allowredraw()){ deletedocument(docid); menu=' + "''" + ';} hideMenu(); ">';
    menu += '    <td>&nbsp;<img src="media/images/icons/delete.gif"></td>';
    menu += '    <td nowrap>&nbsp;<?php echo $_lang[delete_document]; ?></td>';
    menu += '  </tr>';
    menu += '  <tr onmouseover="addClass(this,\'mouseover\');" onmouseout="delClass(this,\'mouseover\');" onclick="if(allowredraw()){ undeletedocument(docid); menu=' + "''" + ';} hideMenu(); ">';
    menu += '    <td>&nbsp;<img src="media/images/icons/undelete.gif"></td>';
    menu += '    <td nowrap>&nbsp;<?php echo $_lang[undelete_document]; ?></td>';
    menu += '  </tr>';
    menu += '  <tr onmouseover="addClass(this,\'mouseover\');" onmouseout="delClass(this,\'mouseover\');" onclick="if(allowredraw()){ showinmenu(docid); menu=' + "''" + ';} hideMenu(); ">';
    menu += '    <td>&nbsp;<img src="media/images/icons/showinmenu.png"></td>';
    menu += '    <td nowrap>&nbsp;<?php echo $_lang[showinmenu]; ?></td>';
    menu += '  </tr>';
    menu += '  <tr onmouseover="addClass(this,\'mouseover\');" onmouseout="delClass(this,\'mouseover\');" onclick="if(allowredraw()){ hideinmenu(docid); menu=' + "''" + ';} hideMenu(); ">';
    menu += '    <td>&nbsp;<img src="media/images/icons/hideinmenu.png"></td>';
    menu += '    <td nowrap>&nbsp;<?php echo $_lang[hideinmenu]; ?></td>';
    menu += '  </tr>';
  }
  menu += '</table>';
  document.getElementById('contextmenu').innerHTML = menu;
  showdocid();
}

// -- Document Tree Actions -- //
function showdocid(){ document.getElementById('showdocid').innerHTML = '(' + docid + ')'; }
function hideMenu(){ document.getElementById('contextmenu').style.display='none'; }
function runURL(a,docid){ top.main.document.location.href = 'index.php?a=' + a + '&id=' + docid; hideMenu(); }
function setshowinmenu(a,docid,show){ top.main.document.location.href = 'index.php?a=' + a + '&id=' + docid + '&show=' + show; hideMenu(); }
function newDoc(a,docid){ top.main.document.location.href = 'index.php?a=' + a + '&pid=' + docid; hideMenu(); }
// -- Context Menu Action Confirmations -- //
function deletedocument(docid) { if(confirm("<?php echo $_lang['confirm_delete_document'] ?>")==true) runURL(6,docid); }
function undeletedocument(docid) { if(confirm("<?php echo $_lang['confirm_undelete'] ?>")==true) runURL(63,docid); }
function publishdocument(docid) { if(confirm("<?php echo $_lang['confirm_publish'] ?>")==true) runURL(61,docid); }
function unpublishdocument(docid) { if(confirm("<?php echo $_lang['confirm_unpublish'] ?>")==true) runURL(62,docid); }
function showinmenu(docid) { if(confirm("<?php echo $_lang['confirm_showinmenu'] ?>")==true) setshowinmenu(49,docid,1); }
function hideinmenu(docid) { if(confirm("<?php echo $_lang['confirm_hideinmenu'] ?>")==true) setshowinmenu(49,docid,0); }
function emptyTrash() {
  if(confirm("<?php echo $_lang['confirm_empty_trash']; ?>")==true) {
    top.main.document.location.href="index.php?a=64";
  }
}

currSorterState="none";

function showSorter() {
  if(currSorterState=="none") {
    currSorterState="block";
    document.getElementById('floater').style.display=currSorterState;
  } else {
    currSorterState="none";
    document.getElementById('floater').style.display=currSorterState;
  }
}

function updateTree() {
  treeUrl = 'index.php?a=1&f=3&dt=' + document.sortFrm.dt.value + '&tree_sortby=' + document.sortFrm.sortby.value + '&tree_sortdir=' + document.sortFrm.sortdir.value;
  document.location.href=treeUrl;
}

try {
  top.topFrame.document.getElementById('buildText').innerHTML = "";
}
catch(oException) { }





function treeAction(id, name) { //SORTABLE EDIT: ADDED DIALOG TO PREVENT LOSS OF WORK. JUST SETTING ca='parent' HERE WILL RESULT IN AN 'UNSPECIFIED ERROR' IN IE !!!!!!
  if(ca!='parent'&& parent.main.document.mutate && parent.main.document.mutate.a && parent.main.document.mutate.a.value=='5') {
      alert("<?php echo $_lang['tree_save_changes']; ?>");
      return;
  }
  if(ca=="move") {
      try {
        parent.main.setMoveValue(id, name);
      } catch(oException) {
        alert("<?php echo $_lang['unable_set_parent']; ?>");
      }
  }
  if(ca=="open" || ca=="" ) {
    if(id==0) {
      parent.main.location.href="index.php?a=2";
    } else {
      parent.main.location.href="index.php?a=3&id=" + id;
    }
  }
  if(ca=="parent") {
    //START ADD LINK INTEGRATION, INSERT LINK ON SELECTED TEXT IN EDITORS ( linkToEditor(id) below )

    if(!linkToEditor(id) && confirm("<?php echo $_lang['confirm_move'] ?>")==true) {
      try {
        parent.main.setParent(id, name);
      } catch(oException) {
        alert('<?php echo $_lang['unable_set_parent']; ?>');
      }
    }
  }
}

function addClass(obj,cl)
{ if(realDrag) { //DRAG FOR PARENT EDIT: WE DON'T WANT A GREY BACKGROUND IF WE ARE DRAGGING OVER A FORBIDDEN ITEM
    p=obj;
    while(p && p.tagName!='LI')
      p=p.parentNode;
    if(p && dragObject && (p.getElementsByTagName('ul')[0]==dragObject.parentNode )){
      obj.style.background='none';
    }
    for(var x=p; x ; x=x.parentNode) {
      if(x==dragObject) {
         obj.style.background='none';
         return;
      }
    }
    return;
  } // END DRAG FOR PARENT EDIT
  var tmp = "";
  if(obj.className.length>0)
  {
    tmp = " ";
  }
  tmp += cl;
  obj.className += tmp;
}

function delClass(obj,cl)
{
  obj.style.cssText=''; //DRAG FOR PARENT EDIT: REMOVE INLINE STYLE POSSIBLY SET BY addClass()
  var tmp = new Array();
  var newClass = "";
  tmp = obj.className.split(' ');
  for (var i = 0; i<tmp.length; i++)
  {
    if(tmp[i] != cl)
    {
      newClass += tmp[i];
      if(i<tmp.length)
      {
        newClass += " ";
      }
    }
  }
  obj.className = newClass;
}

// Force Working/Loading doc tree text removal due to random failures
try {
  top.topFrame.document.getElementById('buildText').innerHTML = "";
}
catch(oException) { }
</script>
<!-- END: Internal Javascript Code -->

<!-- SORTABLE EDIT: A FORM THAT SUBMITS THE ORDER OF THE SORTED LIST TO A HIDDEN IFRAME -->
<iframe id="hiddenframe" name="hiddenframe" style="top: -1000px; left: -1000px;position: absolute;"></iframe>
<div><form name="hiddenform" id="hiddenform" method='post' target="hiddenframe" action="index.php">
<input type="hidden" name="a" value="1">
<input type="hidden" name="f" value="3">
<input type="hidden" name="id" value="">
<input type="hidden" name="orderby" value="<?php echo isset($_SESSION['tree_sortdir']) ? $_SESSION['tree_sortdir'] : 'ASC'; ?>">
<input type="hidden" name="listing" value="">
<input type="hidden" name="editid" value="">
</form></div>
<!-- SORTABLE EDIT: TRANSPARENT DIV ON TOP OF THE TREE TO BLOCK USER INPUT AND DISPLAY DIFFERENT CURSOR WHEN WAITING FOR RESPONSE FROM SERVER, NOT DISPLAYED INITIALLY OF COURSE -->
<div id="workingmess" style="position:absolute; top: 24px; left: 0px; width:100%; height:100%; background: white; display: none; z-index: 9999; filter: alpha(opacity='0'); opacity:0; cursor:wait"><h1>Working...</h1></div>

</body>
</html>