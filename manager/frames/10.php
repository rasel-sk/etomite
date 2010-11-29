<?php
// Tree State modifications provided by Jeroen and Raymond
if (isset($_GET['opened'])) $_SESSION['openedArray'] = $_GET['opened'];
if (isset($_GET['savestateonly'])) {
  echo 'send some data';
  exit;
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $etomite_charset; ?>">
</head>
<body onload="javascript:parent.rpcLoadData(document.body.innerHTML)">

<?php

$indent    = $_GET['indent'];
$parent    = $_GET['parent'];
$expandAll = $_GET['expandAll'];
$output    = "";

if (isset($_SESSION['openedArray'])){
  $opened = explode("|", $_SESSION['openedArray']);
} else {
  $opened = array();
}
$opened2 = array();
$closed2 = array();

makeHTML($indent,$parent,$expandAll);

echo $output;

function makeHTML($indent,$parent,$expandAll){
  global $etomiteDBConn, $output, $dbase, $table_prefix, $_lang, $opened, $opened2, $closed2; //added global vars

  $spacer = "";
  for ($i = 1; $i <= $indent; $i++){ $spacer .= "&nbsp;&nbsp;&nbsp;"; }

  $orderby = "isfolder DESC";
  if(isset($_SESSION['tree_sortby']) && isset($_SESSION['tree_sortdir'])){
    $orderby = $_SESSION['tree_sortby']." ".$_SESSION['tree_sortdir'];
  } else {
    $_SESSION['tree_sortby'] = 'isfolder';
    $_SESSION['tree_sortdir'] = 'DESC';
  }
  if($_SESSION['tree_sortby'] == 'isfolder') $orderby .= ", pagetitle";

  $result = mysql_query("SELECT id, pagetitle, parent, isfolder, published, deleted, type, menuindex, alias FROM $dbase.".$table_prefix."site_content WHERE parent=$parent ORDER BY $orderby", $etomiteDBConn);
  if(mysql_num_rows($result)==0) {
    $output .= '<div style="white-space: nowrap;">'.$spacer.'<img align="absmiddle" src="media/images/tree/deletedpage.gif" width="18" height="18">&nbsp;<span class="emptyNode">'.$_lang['empty_folder'].'</span></div>';
  }
  while(list($id,$pagetitle,$parent,$isfolder,$published,$deleted,$type,$menuindex,$alias) = mysql_fetch_row($result)){
    $pagetitleDisplay = $published==0 ? "<span class='unpublishedNode'>$pagetitle</span>" : "<span class='publishedNode'>$pagetitle</span>";
    $pagetitleDisplay = $deleted==1 ? "<span class='deletedNode'>$pagetitle</span>" : $pagetitleDisplay;
    $weblinkDisplay = $type=="reference" ? '&nbsp;<img align="absmiddle" src="media/images/tree/web.gif">' : '' ;
    $alt = !empty($alias) ? $_lang['alias'].": ".$alias : $_lang['alias'].": -";
    $alt .= "\n".$_lang['document_opt_menu_index'].": ".$menuindex;

    if (!$isfolder){
      $output .= '<div style="white-space: nowrap;">'.$spacer.'<img align="absmiddle" src="media/images/tree/page.gif" width="18" height="18">&nbsp;<span oncontextmenu="dopopup(event.x,event.y);return false;" onclick="treeAction('.$id.', \''.addslashes($pagetitle).'\'); setSelected(this);" onmouseover="setHoverClass(this, 1);" onmouseout="setHoverClass(this, 0);" class="treeNode" onmousedown="itemToChange='.$id.'; selectedObjectName=\''.addslashes($pagetitle).'\'; selectedObjectDeleted='.$deleted.';"  title="'.addslashes($alt).'">'.$pagetitleDisplay.$weblinkDisplay.'</span> <small>('.$id.')</small></div>';
    } else {
      if ($expandAll ==1 || ($expandAll == 2 && in_array($id, $opened))){
        if ($expandAll == 1){
          array_push($opened2, $id);
        }
        $output .= '<div style="white-space: nowrap;">'.$spacer.'<img align="absmiddle" style="cursor: pointer" src="media/images/tree/folderopen.gif" width="18" height="18" oncontextmenu="toggleNode(this,'.($indent+1).','.$id.',1); return false;" onclick="toggleNode(this,'.($indent+1).','.$id.',0); return false;" >&nbsp;<span oncontextmenu="dopopup(event.x,event.y);return false;" onclick="treeAction('.$id.', \''.addslashes($pagetitle).'\'); setSelected(this);" onmouseover="setHoverClass(this, 1);" onmouseout="setHoverClass(this, 0);" class="treeNode" onmousedown="itemToChange='.$id.'; selectedObjectName=\''.addslashes($pagetitle).'\'; selectedObjectDeleted='.$deleted.';"  title="'.addslashes($alt).'">'.$pagetitleDisplay.$weblinkDisplay.'</span> <small>('.$id.')</small><div style="display:block">';
        makeHTML($indent+1,$id,$expandAll);
        $output .= '</div></div>';
      } else {
        $output .= '<div style="white-space: nowrap;">'.$spacer.'<img align="absmiddle" style="cursor: pointer" src="media/images/tree/folder.gif" width="18" height="18" oncontextmenu="toggleNode(this,'.($indent+1).','.$id.',1); return false;" onclick="toggleNode(this,'.($indent+1).','.$id.',0); return false;" >&nbsp;<span oncontextmenu="dopopup(event.x,event.y);return false;" onclick="treeAction('.$id.', \''.addslashes($pagetitle).'\'); setSelected(this);" onmouseover="setHoverClass(this, 1);" onmouseout="setHoverClass(this, 0);" class="treeNode" onmousedown="itemToChange='.$id.'; selectedObjectName=\''.addslashes($pagetitle).'\'; selectedObjectDeleted='.$deleted.';"  title="'.addslashes($alt).'">'.$pagetitleDisplay.$weblinkDisplay.'</span> <small>('.$id.')</small><div style="display:none"></div></div>';
        array_push($closed2, $id);
      }
    }

    if ($expandAll == 1){
      echo '<script language="JavaScript">';
      foreach ($opened2 as $item){
        printf("parent.openedArray[%d] = 1;", $item);
      }
      echo '</script>';
    } elseif ($expandAll == 0){
      echo '<script language="JavaScript">';
      foreach ($closed2 as $item){
        printf("parent.openedArray[%d] = 0;", $item);
      }
      echo '</script>';
    }
  }
}
?>

</body>
</html>
