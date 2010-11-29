// Snippet name: MenuBuilder
// Snippet description: Builds the site menu
// Revision: 1.00 ships with Etomite 0.6.1-Final

$id = isset($id) ? $id : $etomite->documentIdentifier;
$sortby = "menuindex";
$sortdir = "ASC";
$fields = "id, pagetitle, description, parent, alias";

$indentString="";

if(!isset($indent)) {
    $indent = "";
    $indentString .= "";
} else {
    for($in=0; $in<$indent; $in++) {
        $indentString .= "&nbsp;";
    }
    $indentString .= "&raquo;&nbsp;";
}

$children = $etomite->getActiveChildren($id, $sortby, $sortdir, $fields);
$menu = "";
$childrenCount = count($children);
$active="";

if($children==false) {
    return false;
}
for($x=0; $x<$childrenCount; $x++) {
	if($children[$x]['id']==$etomite->documentIdentifier) {
		$active="class=\"highLight\"";
	} else {
		$active="";
	}
	if($children[$x]['id']==$etomite->documentIdentifier || $children[$x]['id']==$etomite->documentObject['parent']) {
		$menu .= "<a ".$active." href=\"[~".$children[$x]['id']."~]\">$indentString".$children[$x]['pagetitle']."</a>[[MenuBuilder?id=".$children[$x]['id']."&indent=2]]";	
	} else {
		$menu .= "<a href=\"[~".$children[$x]['id']."~]\">$indentString".$children[$x]['pagetitle']."</a>";
	}
}
return $menu."";
